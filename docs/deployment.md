# AWS Production Deployment Guide

## Jajal Medical Case Management System

This guide outlines an enterprise, HIPAA-conscious architecture for deploying the Medical Case Management System on Amazon Web Services (AWS).

---

## 1. High-Level AWS Architecture

```
                    +---------------------------+
                    |     Route 53 (DNS)        |
                    +-------------+-------------+
                                  |
                                  v
                    +---------------------------+
                    | AWS Certificate Manager   |
                    | (SSL/TLS HTTPS Cert)      |
                    +-------------+-------------+
                                  |
                                  v
                    +---------------------------+
                    |  Application Load         |
                    |  Balancer (ALB)           |
                    |  Ports 80 -> 443 Redirect |
                    +-------------+-------------+
                                  |
                 +----------------+----------------+
                 | (Public Subnet)                 |
                 v                                 v
    +-------------------------+       +-------------------------+
    | EC2 Instance 1 (VPC)    |       | EC2 Instance 2 (VPC)    |
    | Nginx + PHP 8.3-FPM     |       | Nginx + PHP 8.3-FPM     |
    | Laravel App Code        |       | Laravel App Code        |
    +------------+------------+       +------------+------------+
                 |                                 |
                 +----------------+----------------+
                                  |
                           (Private Subnet)
                                  v
                    +---------------------------+
                    | Amazon RDS for MySQL 8.0  |
                    | Multi-AZ Deployment       |
                    | KMS Encrypted At Rest     |
                    +---------------------------+
```

---

## 2. Infrastructure Components

### A. Virtual Private Cloud (VPC)
- **VPC CIDR**: `10.0.0.0/16`
- **Subnets**:
  - 2x Public Subnets (for ALB and NAT Gateways): `10.0.1.0/24`, `10.0.2.0/24`
  - 2x Private Subnets (for EC2 Compute): `10.0.10.0/24`, `10.0.20.0/24`
  - 2x Isolated Subnets (for RDS MySQL): `10.0.30.0/24`, `10.0.40.0/24`

### B. Security Groups
1. **`alb-sg`**:
   - Inbound: `80` (HTTP from 0.0.0.0/0), `443` (HTTPS from 0.0.0.0/0).
   - Outbound: All traffic to `app-sg`.
2. **`app-sg` (EC2 Instances)**:
   - Inbound: `80` and `443` only from `alb-sg`.
   - Inbound: `22` (SSH) only from Bastion Host or AWS Systems Manager (SSM) Session Manager.
   - Outbound: MySQL `3306` to `rds-sg`, HTTPS `443` to AWS endpoints via NAT Gateway.
3. **`rds-sg` (Database)**:
   - Inbound: `3306` (MySQL) strictly from `app-sg`.
   - Outbound: Deny all.

---

## 3. Server Configuration (Nginx + PHP-FPM)

### Nginx Virtual Host Configuration
`/etc/nginx/sites-available/jajal-medical.conf`:
```nginx
server {
    listen 80;
    server_name cases.jajalmedical.internal;
    root /var/www/jajal-medical/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Environment Secrets & Deployment Steps

1. Store production secrets in **AWS Secrets Manager** or **AWS Systems Manager Parameter Store**:
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`
2. Automated deployment script (via GitHub Actions or AWS CodeDeploy):
   ```bash
   cd /var/www/jajal-medical
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan migrate --force
   sudo systemctl restart php8.3-fpm
   ```
