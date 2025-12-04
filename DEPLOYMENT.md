# 🚀 Production Deployment Guide

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Queue Setup](#queue-setup)
5. [Cron Jobs](#cron-jobs)
6. [SSL Configuration](#ssl-configuration)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring & Logging](#monitoring--logging)
9. [Backup Strategy](#backup-strategy)
10. [Troubleshooting](#troubleshooting)

---

## Server Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Database**: MySQL 8.0+ / PostgreSQL 12+
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Composer**: 2.x
- **Redis**: 6.x or higher (for caching & queues)
- **Web Server**: Nginx 1.18+ / Apache 2.4+
- **RAM**: 2GB minimum, 4GB recommended
- **Storage**: 10GB minimum

### PHP Extensions Required
```bash
php -m | grep -E "bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|curl|gd|zip"
```

Required extensions:
- bcmath
- ctype
- fileinfo
- JSON
- mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD
- Zip
- Redis

---

## Installation Steps

### 1. Clone Repository
```bash
cd /var/www
git clone <your-repository-url> multimart-commerce
cd multimart-commerce
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build assets
npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env.production
nano .env.production
```

Update the following variables:
```env
APP_NAME="MultimartCommerce"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Generate Application Key
```bash
php artisan key:generate --env=production
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate --force --env=production
php artisan db:seed --class=InitialDataSeeder --env=production
```

### 6. Set Permissions
```bash
chown -R www-data:www-data /var/www/multimart-commerce
chmod -R 755 /var/www/multimart-commerce
chmod -R 775 /var/www/multimart-commerce/storage
chmod -R 775 /var/www/multimart-commerce/bootstrap/cache
```

---

## Configuration

### Nginx Configuration
Create `/etc/nginx/sites-available/multimart-commerce`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/multimart-commerce/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    # Increase upload size
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:
```bash
ln -s /etc/nginx/sites-available/multimart-commerce /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

---

## Queue Setup

### Supervisor Configuration
Create `/etc/supervisor/conf.d/multimart-worker.conf`:

```ini
[program:multimart-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/multimart-commerce/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/multimart-commerce/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start multimart-worker:*
```

---

## Cron Jobs

Add to crontab (`crontab -e -u www-data`):

```cron
* * * * * cd /var/www/multimart-commerce && php artisan schedule:run >> /dev/null 2>&1
```

---

## SSL Configuration

### Let's Encrypt SSL
```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Auto-renewal:
```bash
certbot renew --dry-run
```

---

## Performance Optimization

### 1. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. OPcache Configuration
Edit `/etc/php/8.1/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.interned_strings_buffer=16
```

### 3. Redis Configuration
Edit `/etc/redis/redis.conf`:

```conf
maxmemory 512mb
maxmemory-policy allkeys-lru
```

---

## Monitoring & Logging

### Laravel Telescope (Development Only)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Log Rotation
Create `/etc/logrotate.d/multimart`:

```
/var/www/multimart-commerce/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## Backup Strategy

### Database Backup Script
Create `/usr/local/bin/backup-multimart.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/backups/multimart"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="your_database"
DB_USER="your_username"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/multimart-commerce/storage/app/public

# Remove backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Make executable and add to cron:
```bash
chmod +x /usr/local/bin/backup-multimart.sh
crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-multimart.sh
```

---

## Troubleshooting

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Check Queue Status
```bash
supervisorctl status multimart-worker:*
php artisan queue:failed
```

### Monitor Logs
```bash
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Performance Check
```bash
php artisan optimize
composer dump-autoload --optimize
```

---

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong database password
- [ ] SSL certificate installed
- [ ] Firewall configured (UFW)
- [ ] SSH key authentication only
- [ ] Regular security updates
- [ ] File permissions correct (755/775)
- [ ] .env file secured (600)
- [ ] CORS configured properly
- [ ] Rate limiting enabled
- [ ] CSRF protection enabled
- [ ] XSS protection headers
- [ ] SQL injection prevention (Eloquent ORM)

---

## Post-Deployment Verification

1. Check homepage loads: `https://yourdomain.com`
2. Test user registration and login
3. Place a test order
4. Verify email notifications
5. Check admin panel access
6. Test vendor dashboard
7. Verify queue workers running
8. Check cron job execution
9. Test coupon application
10. Verify category filtering

---

**Note**: Replace placeholders like `yourdomain.com`, `your_database`, etc. with actual values.
