<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

function headless_project_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}
add_action( 'wp_enqueue_scripts', 'headless_project_style' );



/**
 * User Registeration endpoint
 */

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/register', array(
        'methods' => 'POST',
        'callback' => 'hp_custom_user_registration',
        'permission_callback' => '__return_true'
    ));
});

function hp_custom_user_registration($request) {
    $email = sanitize_email($request['email']);
    $password = $request['password'];

    $username = explode('@', $email)[0]; // email ka pehla part username banega
    $username = sanitize_user($username);

    // Agar same username already exist karta hai, to random suffix add kar do
    $base_username = $username;
    $i = 1;
    while (username_exists($username)) {
        $username = $base_username . $i;
        $i++;
    }

    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email already registered', array('status' => 400));
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return new WP_Error('user_error', 'User could not be created', array('status' => 500));
    }

    return array(
        'success' => true,
        'user_id' => $user_id,
        'username' => $username
    );
}





// Block unauthenticated access to posts endpoints only
add_filter('rest_authentication_errors', function ($result) {
    // Get current route
    $route = $GLOBALS['wp']->query_vars['rest_route'] ?? '';
    
    // Check if this is a posts-related endpoint
    if (strpos($route, '/wp/v2/posts') === 0) {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                'You must be authenticated to access this endpoint.',
                ['status' => 401]
            );
        }
    }
    
    return $result;
});

// Filter posts: show only own posts unless admin
add_filter('rest_post_query', function ($args, $request) {
    if (is_user_logged_in() && !current_user_can('administrator')) {
        $args['author'] = get_current_user_id();
    }
    return $args;
}, 10, 2);