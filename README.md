# 🧠 WordPress + React (Headless) with AI – Local Development Setup

This is a local development environment running a custom WordPress setup (as a headless CMS) with a React frontend. It uses Docker on Ubuntu with Apache to manage containers and services.

---

## 🚀 Project Structure

- **Backend**: WordPress (Docker, MySQL)
- **Frontend**: React (Headless, runs on localhost:3000)
- **AI Integration**: Custom logic for AI-powered features
- **Environment**: Localhost with Docker containers for isolated setup

---

## 🔧 Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/ckrizwan/wordpress.git
cd wordpress
```

---

### 2. Import WordPress Database into Docker

#### A. Copy SQL file into the DB container

```bash
docker cp wordpress.sql wordpress-db-1:/wordpress.sql
```

#### B. Access the WordPress DB container

```bash
docker exec -it wordpress-db-1 bash
```

#### C. Import the SQL file into the `wordpress` database

```bash
mysql -u wpuser -p wordpress < /wordpress.sql
```

> 🔐 When prompted for a password, enter:  
> `Wp@12345!`

---

## 🌐 Local Endpoints

| Service            | URL                                            |
|--------------------|-------------------------------------------------|
| WordPress Frontend | http://localhost:8080                          |
| WordPress Admin    | http://localhost:8080/wp-admin                |
| React Frontend     | http://localhost:3000                         |

### 🧪 WordPress Admin Credentials

- **Username**: `admin`  
- **Password**: `pass`

---

## 📂 Project Structure Example

```
.
├── backend/              # WordPress-related files (themes, plugins, config)
├── frontend/             # React app
├── wordpress.sql         # Exported WordPress DB
├── docker-compose.yml    # Docker configuration
└── README.md             # You're reading it!
```

## 🐳 Docker Manual Commands

### 📤 Export WordPress Database

```bash
sudo mysqldump -u root -p wordpress > wordpress.sql
```

### 📥 Import WordPress Database

```bash
# Copy SQL into container
docker cp wordpress.sql wordpress-db-1:/wordpress.sql

# Enter container
docker exec -it wordpress-db-1 bash

# Import into MySQL
mysql -u wpuser -p wordpress < /wordpress.sql
```

---

## 📌 Notes

- Ensure Docker is installed and running before starting the setup.
- The React app communicates with WordPress via REST API.

---