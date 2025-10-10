# 🚀 Production Deployment Guide - Environment Variables

## ✅ SECURE CONFIGURATION IMPLEMENTED

Your Seblak Predator application now uses **environment variables** for ALL sensitive configuration:

- ✅ **Database credentials** - No longer hardcoded
- ✅ **JWT secrets** - Secure 64-character keys
- ✅ **Email configuration** - SMTP settings in environment
- ✅ **Security settings** - All configurable via .env

## 📁 Environment Files Structure

```
seblak-predator/
├── .env                 # 🔒 Local/Development (NOT in Git)
├── .env.example         # 📋 Template (safe to commit)
├── .env.production      # 🚀 Production template
├── config/env.php       # 🔧 Environment loader
├── config/database.php  # 🗄️ Secure DB connection
└── config/config.php    # ⚙️ Uses environment variables
```

## 🛡️ Development vs Production

### Development (.env)
```env
# Development Environment
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Local Database
DB_HOST=localhost
DB_NAME=seblak_app
DB_USER=root
DB_PASSWORD=

# Development JWT (auto-generated)
JWT_SECRET_KEY=481a308c37cea2d2740c941492da06a7ac1e70ec2e77549fb93687b749c06c01
```

### Production (Server Environment Variables)
```bash
# Production Environment Variables
export APP_ENV=production
export APP_DEBUG=false
export APP_URL=https://seblakpredator.com

# Production Database
export DB_HOST=your-production-db-host
export DB_NAME=seblak_app_prod
export DB_USER=your-db-user
export DB_PASSWORD=your-secure-db-password

# Production JWT (generate new secure keys)
export JWT_SECRET_KEY=your-production-jwt-secret-64-chars
```

## 🌐 Server Configuration Examples

### 1. Apache (.htaccess)
```apache
# Add to your .htaccess or virtual host
SetEnv APP_ENV "production"
SetEnv APP_DEBUG "false"
SetEnv DB_HOST "your-db-host"
SetEnv DB_NAME "seblak_app_prod"
SetEnv DB_USER "your-db-user"
SetEnv DB_PASSWORD "your-secure-password"
SetEnv JWT_SECRET_KEY "your-production-jwt-secret"
```

### 2. Nginx + PHP-FPM
```nginx
# Add to your Nginx server block
location ~ \.php$ {
    fastcgi_param APP_ENV "production";
    fastcgi_param APP_DEBUG "false";
    fastcgi_param DB_HOST "your-db-host";
    fastcgi_param DB_NAME "seblak_app_prod";
    fastcgi_param DB_USER "your-db-user";
    fastcgi_param DB_PASSWORD "your-secure-password";
    fastcgi_param JWT_SECRET_KEY "your-production-jwt-secret";
    
    # Standard PHP-FPM config
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### 3. Docker Deployment
```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=db
      - DB_NAME=seblak_app
      - DB_USER=seblak_user
      - DB_PASSWORD=${DB_PASSWORD}
      - JWT_SECRET_KEY=${JWT_SECRET_KEY}
    depends_on:
      - db
      
  db:
    image: mysql:8.0
    environment:
      - MYSQL_DATABASE=seblak_app
      - MYSQL_USER=seblak_user
      - MYSQL_PASSWORD=${DB_PASSWORD}
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
```

### 4. Shared Hosting (cPanel)
```bash
# Add to cPanel Environment Variables or .htaccess
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_NAME=username_seblak_app
DB_USER=username_seblak
DB_PASSWORD=your-cpanel-db-password
JWT_SECRET_KEY=your-generated-jwt-secret
```

## 🔑 Secure Key Generation for Production

### Generate Production Keys
```bash
# On your production server
php generate_keys.php

# Copy the output and set as environment variables
# NEVER store production keys in files!
```

### Production Key Requirements
- **JWT Secret**: Minimum 64 characters, cryptographically secure
- **Session Key**: 32 characters, unique per environment  
- **App Key**: 32 characters, for general encryption
- **Database Password**: Strong password with special characters

## 🔒 Security Best Practices

### ✅ DO
- Generate unique keys for each environment
- Use server environment variables in production
- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use HTTPS in production (`APP_URL=https://...`)
- Store secrets in password manager or vault
- Rotate keys regularly (every 3-6 months)
- Use strong database passwords
- Restrict database access by IP

### ❌ DON'T
- Commit `.env` files to Git
- Use development keys in production
- Store production secrets in files
- Enable debug mode in production
- Use default or weak passwords
- Share secrets in team chat or email
- Hard-code any credentials

## 🔧 Deployment Checklist

### Pre-Deployment
- [ ] Generate production keys with `php generate_keys.php`
- [ ] Set up production database with secure credentials
- [ ] Configure server environment variables
- [ ] Test environment loading with `php test_environment.php`
- [ ] Verify `.env` is in `.gitignore`

### Production Deployment
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database connection
- [ ] Set secure JWT secret key
- [ ] Configure email settings for production
- [ ] Enable HTTPS (`APP_URL=https://...`)
- [ ] Test application functionality

### Post-Deployment
- [ ] Verify no debug information is displayed
- [ ] Test login and JWT token generation
- [ ] Check database connectivity
- [ ] Verify email functionality
- [ ] Monitor error logs
- [ ] Set up monitoring and alerts

## 🐛 Troubleshooting

### Common Issues

**Error: "Database connection failed"**
```bash
# Check environment variables
echo $DB_HOST
echo $DB_NAME
echo $DB_USER

# Verify database server is running
mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME
```

**Error: "JWT_SECRET_KEY must be set in production"**
```bash
# Set the environment variable
export JWT_SECRET_KEY="your-64-character-secret-key"

# Or add to server configuration
```

**Error: "Permission denied" or file access issues**
```bash
# Check file permissions
chmod 644 .env
chown www-data:www-data .env

# Ensure PHP can read environment files
```

### Environment Testing
```bash
# Test environment loading
php -r "
require_once 'config/env.php';
echo 'Environment: ' . EnvLoader::get('APP_ENV') . PHP_EOL;
echo 'Debug: ' . (EnvLoader::get('APP_DEBUG') ? 'ON' : 'OFF') . PHP_EOL;
echo 'Database: ' . EnvLoader::get('DB_NAME') . PHP_EOL;
"
```

## 📊 Security Audit Results

### Before Environment Variables
- ❌ Database credentials hardcoded in source
- ❌ JWT secrets visible in repository  
- ❌ Configuration mixed with application code
- ❌ Same credentials for all environments

### After Environment Variables  
- ✅ All secrets stored in environment variables
- ✅ No sensitive data in source code
- ✅ Secure production deployment
- ✅ Easy environment-specific configuration
- ✅ Industry-standard security practices

## 🎯 Summary

Your Seblak Predator application now follows **industry-standard security practices**:

1. **🔒 Secure Storage**: All secrets in environment variables
2. **🚫 No Hardcoding**: No credentials in source code  
3. **🌍 Environment-Specific**: Different configs per environment
4. **🔄 Easy Rotation**: Simple key rotation process
5. **🚀 Production-Ready**: Secure deployment configuration

**Your application is now PRODUCTION-READY with enterprise-grade security! 🛡️**