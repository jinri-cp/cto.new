# PHP Short URL System

A simple and secure personal short URL system built with native PHP 8 and MySQL, featuring a Bootstrap admin interface with authentication and CRUD operations.

## Features

- 🚀 Fast URL shortening and redirection
- 🔐 Secure admin dashboard with login authentication
- 📝 Full CRUD operations for short URLs
- 🔍 Search and pagination functionality
- 🛡️ CSRF protection and input validation
- ⏰ Optional URL expiry dates
- 📊 Active/disabled URL status management
- 🎯 Custom or auto-generated short codes
- 📱 Responsive Bootstrap 5 interface

## Requirements

- PHP 8.0+
- MySQL 5.7+ / 8.0+
- Web server (Apache with mod_rewrite or Nginx)

## Installation

### 1. Clone and Setup

```bash
git clone <repository-url>
cd shorturl
```

### 2. Configure Environment

Copy the environment template:
```bash
cp .env.example .env
```

Edit `.env` with your database credentials:
```env
DB_HOST=localhost
DB_NAME=shorturl
DB_USER=root
DB_PASS=your_password

APP_URL=http://localhost
SESSION_NAME=shorturl_session
CSRF_TOKEN_NAME=csrf_token
```

### 3. Database Setup

Run the migration script to create tables:
```bash
php scripts/migrate.php
```

Create an admin user:
```bash
php scripts/seed_admin.php admin password123
```

Or interactively:
```bash
php scripts/seed_admin.php
# Follow prompts to enter username and password
```

### 4. Web Server Configuration

#### Apache

Point your DocumentRoot to the `public/` directory. The included `.htaccess` file handles URL rewriting.

#### Nginx

Add this to your Nginx configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/shorturl/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Usage

### Admin Dashboard

1. Access the admin panel at `http://your-domain.com/admin`
2. Login with your admin credentials
3. Create, edit, delete, and manage short URLs

### Creating Short URLs

- **Auto-generated**: Leave the custom code field empty
- **Custom code**: Enter 4-10 characters (letters, numbers, underscore, dash)
- **Optional settings**: Set expiry date and active/disabled status

### URL Redirection

Access short URLs directly: `http://your-domain.com/{short_code}`

- Active URLs redirect with 301 status
- Disabled/expired URLs show an error page
- Non-existent URLs return 404

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Output escaping and security headers
- **Login Throttling**: Simple session-based rate limiting
- **Input Validation**: URL format, short code patterns, date validation

## Directory Structure

```
shorturl/
├── public/
│   ├── index.php          # Main router and entry point
│   └── .htaccess          # Apache URL rewriting
├── app/
│   ├── Controllers/       # Request handlers
│   ├── Models/           # Database models
│   ├── Services/         # Business logic
│   └── Support/          # Helper classes
├── views/admin/          # Admin interface templates
├── config/               # Application configuration
├── scripts/              # Database utilities
└── .env.example          # Environment template
```

## API Endpoints

### Public
- `GET /{code}` - Redirect to long URL

### Admin (Authentication Required)
- `GET /admin/login` - Login form
- `POST /admin/login` - Process login
- `POST /admin/logout` - Logout
- `GET /admin/urls` - List short URLs (with search & pagination)
- `GET/POST /admin/urls/create` - Create new short URL
- `GET/POST /admin/urls/{id}/edit` - Edit short URL
- `POST /admin/urls/{id}/delete` - Delete short URL

## Database Schema

### users
- `id` - Primary key
- `username` - Unique admin username
- `password_hash` - Bcrypt hash
- `created_at` - Registration timestamp

### short_urls
- `id` - Primary key
- `code` - Unique short code (4-12 chars)
- `long_url` - Destination URL
- `is_active` - Boolean status flag
- `expire_at` - Optional expiry timestamp
- `created_at/updated_at` - Timestamps

## Development

### Adding New Features

1. Controllers in `app/Controllers/`
2. Business logic in `app/Services/`
3. Database operations in `app/Models/`
4. Templates in `views/`

### Configuration

Edit files in `config/`:
- `app.php` - Application settings
- `database.php` - Database connection

## Troubleshooting

### Common Issues

1. **404 Errors**: Check web server URL rewriting configuration
2. **Database Connection**: Verify `.env` credentials and MySQL access
3. **Session Issues**: Ensure `session_start()` is called before output
4. **Permission Errors**: Check web server write permissions if needed

### Debug Mode

Enable error reporting by uncommenting in `public/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## License

This project is open source and available under the [MIT License](LICENSE).