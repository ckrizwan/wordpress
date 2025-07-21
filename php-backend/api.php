<?php

// api.php - Your React app makes requests to this script
require_once __DIR__ . '/vendor/autoload.php';

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;
use GuzzleHttp\Client as GuzzleHttpClient;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // IMPORTANT: For CORS in dev. Restrict in production!
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow necessary headers
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow necessary methods

// Handle OPTIONS preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$input      = json_decode(file_get_contents('php://input'), true);
$userPrompt = $input['prompt'] ?? '';

if (empty($userPrompt)) {
    echo json_encode(['error' => 'Prompt is required.']);
    exit;
}

// --- Configuration ---
$geminiApiKey  = getenv('GEMINI_API_KEY'); // Your Gemini API Key
$wpMcpUrl      = 'http://wordpress:80/wp-json/wp/v2/wpmcp'; // Your WordPress MCP endpoint
$wpMcpJwtToken = 'YOUR_WORDPRESS_MCP_JWT_TOKEN'; // The token you generated in WordPress

if ( ! $geminiApiKey || ! $wpMcpJwtToken) {
    echo json_encode(['error' => 'API keys or tokens not configured.']);
    exit;
}

$geminiClient = new Client($geminiApiKey);
$httpClient   = new GuzzleHttpClient();

try {
    // --- Step 1: Discover available tools from WordPress MCP Plugin ---
    $toolsDefinition = [];
    try {
        $response  = $httpClient->post("{$wpMcpUrl}/tools/list", [
            'headers' => [
                'Authorization' => 'Bearer ' . $wpMcpJwtToken,
                'Content-Type'  => 'application/json',
            ],
            'json'    => [] // Empty body for list-tools
        ]);
        $toolsList = json_decode($response->getBody()->getContents(), true);

        if (isset($toolsList['tools']) && is_array($toolsList['tools'])) {
            foreach ($toolsList['tools'] as $tool) {
                // Extract name and description, and build a valid Gemini function declaration
                $functionDeclaration = [
                    'name'        => $tool['name'],
                    'description' => $tool['description'] ?? "A WordPress MCP tool.",
                    'parameters'  => [
                        'type'       => 'object',
                        'properties' => [],
                        'required'   => []
                    ]
                ];

                // Parse tool parameters (assuming the plugin provides schema like 'arguments_schema')
                if (isset($tool['arguments_schema']['properties'])) {
                    foreach ($tool['arguments_schema']['properties'] as $paramName => $paramDetails) {
                        $functionDeclaration['parameters']['properties'][$paramName] = [
                            'type'        => $paramDetails['type'] ?? 'string', // Default to string if type is missing
                            'description' => $paramDetails['description'] ?? '',
                        ];
                        if (isset($paramDetails['required']) && $paramDetails['required']) {
                            $functionDeclaration['parameters']['required'][] = $paramName;
                        }
                    }
                }
                $toolsDefinition[] = $functionDeclaration;
            }
        }
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        error_log(
            "Failed to list WordPress MCP tools: " . $e->getMessage() . " - " . $e->getResponse()->getBody(
            )->getContents()
        );
        // Continue without tools or throw, depending on your error strategy
        // For POC, we might continue but log the error
    }


    // --- Step 2: Send prompt to Gemini with tool definitions ---
    $chat = $geminiClient->generativeModel(ModelName::GEMINI_PRO)
                         ->startChat();

    $geminiResponse = $chat->sendMessage(new TextPart($userPrompt), [
        'tools' => $toolsDefinition
    ]);

    $toolCalls = $geminiResponse->candidates[0]->content->parts[0]->toolCalls ?? [];

    if ( ! empty($toolCalls)) {
        // --- Step 3: Gemini wants to call a tool! Execute it via WordPress MCP ---
        $toolResponsesForGemini = [];
        foreach ($toolCalls as $toolCall) {
            $toolName = $toolCall->function->name;
            $toolArgs = (array)$toolCall->function->args;

            // Make the actual call to the WordPress MCP server's tools/call endpoint
            try {
                $mcpCallResponse = $httpClient->post("{$wpMcpUrl}/tools/call", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $wpMcpJwtToken,
                        'Content-Type'  => 'application/json',
                    ],
                    'json'    => [
                        'name'      => $toolName,
                        'arguments' => $toolArgs,
                    ],
                ]);

                $mcpResult = json_decode($mcpCallResponse->getBody()->getContents(), true);

                $toolResponsesForGemini[] = [
                    'toolName' => $toolName,
                    // Gemini expects 'content' to be a string, so JSON encode the result
                    'content'  => json_encode($mcpResult)
                ];
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                error_log(
                    "Failed to call WordPress MCP tool '{$toolName}': " . $e->getMessage() . " - " . $e->getResponse(
                    )->getBody()->getContents()
                );
                $toolResponsesForGemini[] = [
                    'toolName' => $toolName,
                    'content'  => json_encode(['error' => 'Failed to execute tool', 'details' => $e->getMessage()]),
                ];
            }
        }

        // --- Step 4: Send tool results back to Gemini ---
        $finalGeminiResponse = $chat->sendMessage(
            new TextPart("Tool results: " . json_encode($toolResponsesForGemini)),
            [
                'toolResponses' => $toolResponsesForGemini // Pass the structured tool responses
            ]
        );

        echo json_encode(['response' => $finalGeminiResponse->text()]);
    } else {
        // No tool call, Gemini responded directly
        echo json_encode(['response' => $geminiResponse->text()]);
    }
} catch (Exception $e) {
    error_log("General API Error: " . $e->getMessage());
    echo json_encode(['error' => 'An internal server error occurred.']);
}