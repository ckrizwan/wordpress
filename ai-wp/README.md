# WordPress Local Project

A custom WordPress setup running on Ubuntu with Apache.

## 🔧 Installation

1. Clone the repo
2. Copy files to `/srv/www/wordpress`
3. Import the SQL database
4. Database file path: [Directory]/database/backup.sql
4. Configure `wp-config.php` with your DB credentials

## 🧪 Login Info

- URL: [http://localhost/wp-admin](http://localhost/wp-admin)
- Username: `admin`
- Password: `pass`

## Docker
    - Export Database:
        - `sudo mysqldump -u root -p wordpress > wordpress.sql`
    - Import database into docker container
        - `docker cp wordpress.sql ai-project-db-1:/wordpress.sql`
    - Enter the DB Container
        - `docker exec -it ai-project-db-1 bash`
    - Import SQL File Into wordpress Database
        - `mysql -u wpuser -p wordpress < /wordpress.sql`