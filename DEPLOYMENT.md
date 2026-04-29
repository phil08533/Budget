# 🚀 FutureWorth Deployment Guide

How to make FutureWorth live for other people to use.

## Option 1: Using a Web Hosting Service (Easiest)

### Recommended Hosts:
- **Bluehost** ($2.95/month) - Great for beginners
- **SiteGround** ($3.99/month) - Excellent support
- **HostGator** ($2.75/month) - Affordable
- **DreamHost** ($2.59/month) - Good performance

### Steps:
1. **Sign up** for a hosting account (pick one above)
2. **Get your domain name** (example: `futureworth.com`)
3. **Use File Manager/FTP** to upload all files:
   - login.php
   - index.php
   - auth.php
   - api.php
   - db.php
   - app.js
   - styles.css
   - schema.sql

4. **Create MySQL Database**:
   - Login to cPanel or hosting control panel
   - Create new MySQL database
   - Create MySQL user with password
   - Grant all privileges to that user

5. **Update Database Credentials**:
   - Edit `db.php`
   - Update `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`

6. **Import Database Schema**:
   - Go to phpMyAdmin (usually in cPanel)
   - Select your database
   - Click "Import"
   - Upload `schema.sql`
   - Click "Go"

7. **Visit Your Site**:
   - Go to `https://yourdomain.com/login.php`
   - Users can now register and use it!

---

## Option 2: Using Heroku (Free or Paid)

### Why Heroku:
- Easy to deploy
- Automatic SSL (HTTPS)
- No credit card required (free tier)
- Scales automatically

### Steps:

1. **Sign up** at https://heroku.com

2. **Install Heroku CLI**:
   ```bash
   # On Mac/Linux:
   brew tap heroku/brew && brew install heroku
   
   # On Windows:
   # Download from https://devcenter.heroku.com/articles/heroku-cli
   ```

3. **Create Procfile** (save in your project root):
   ```
   web: vendor/bin/heroku-php-nginx -C nginx.conf public/
   ```

4. **Create public/.htaccess**:
   ```
   <IfModule mod_rewrite.c>
     RewriteEngine On
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteRule ^ index.php [QSA,L]
   </IfModule>
   ```

5. **Create composer.json** (save in project root):
   ```json
   {
     "require": {
       "php": "^7.4"
     }
   }
   ```

6. **Initialize Git**:
   ```bash
   git init
   git add .
   git commit -m "Deploy to Heroku"
   ```

7. **Deploy**:
   ```bash
   heroku login
   heroku create your-app-name
   git push heroku main
   ```

8. **Setup Database**:
   ```bash
   heroku addons:create cleardb:ignite
   heroku config:get CLEARDB_DATABASE_URL
   ```

9. **Visit Your Site**:
   - https://your-app-name.herokuapp.com/login.php

---

## Option 3: DigitalOcean App Platform (Recommended)

### Why DigitalOcean:
- Affordable ($5-12/month)
- Great performance
- Simple deployment
- Good documentation

### Steps:

1. **Sign up** at https://digitalocean.com ($5 credit code: WELCOME25)

2. **Create App from Git**:
   - Push your code to GitHub
   - Go to DigitalOcean Dashboard
   - Click "Apps"
   - Click "Create App"
   - Connect your GitHub repository

3. **Configure App**:
   - Set runtime to "PHP 8.0"
   - Set HTTP port to 8080

4. **Add MySQL Database**:
   - Click "Create Database"
   - Select MySQL
   - Note the connection details

5. **Update Database Credentials**:
   - Add environment variables in App settings:
     - `DB_HOST`
     - `DB_USER`
     - `DB_PASS`
     - `DB_NAME`

6. **Deploy**:
   - Click "Deploy"
   - Wait 2-3 minutes
   - Your app is live!

---

## Option 4: Using AWS (Advanced)

### Services Needed:
- **EC2** - Virtual server
- **RDS** - MySQL database
- **Route 53** - Domain management

### Basic Steps:
1. Create EC2 instance (Ubuntu 20.04)
2. Install PHP 8.0, Apache, MySQL client
3. Clone your repository
4. Create RDS MySQL instance
5. Update db.php with RDS endpoint
6. Configure domain in Route 53
7. Setup SSL with Let's Encrypt

**Estimated Cost**: $5-20/month

---

## Option 5: Docker + Any Host (Advanced)

### What You Need:
1. Create `Dockerfile`:
   ```dockerfile
   FROM php:8.0-apache
   RUN docker-php-ext-install pdo pdo_mysql
   COPY . /var/www/html
   EXPOSE 80
   ```

2. Deploy to:
   - Google Cloud Run
   - AWS ECS
   - DigitalOcean Container Registry

---

## Quick Checklist Before Going Live

- [ ] Database credentials updated in `db.php`
- [ ] MySQL database created and schema imported
- [ ] All files uploaded/deployed
- [ ] SSL certificate installed (HTTPS enabled)
- [ ] Test login/register works
- [ ] Test adding income/expenses
- [ ] Test saving scenarios
- [ ] Email verification setup (optional)
- [ ] Backup strategy in place
- [ ] Domain name registered and pointing to server

---

## Recommended Setup for Beginners

**Best Option: Bluehost + Custom Domain**

1. Sign up at Bluehost ($2.95/month)
2. Register domain ($12/year)
3. Use their cPanel to upload files and create database
4. Update `db.php` with database details
5. Import `schema.sql` via phpMyAdmin
6. Go live!

**Total Cost**: ~$40/year

---

## Recommended Setup for More Control

**Better Option: DigitalOcean App Platform**

1. Sign up at DigitalOcean
2. Push code to GitHub
3. Create app from GitHub
4. Configure database
5. Deploy with one click
6. Register custom domain

**Total Cost**: ~$5/month ($60/year)

---

## Security Checklist

- [ ] HTTPS enabled (SSL certificate)
- [ ] Database backed up regularly
- [ ] Strong database passwords
- [ ] PHP error reporting disabled in production
- [ ] Sessions stored securely
- [ ] Input validation on all forms
- [ ] SQL injection protection (prepared statements) ✅ Already done
- [ ] XSS protection (HTML escaping) ✅ Already done
- [ ] CSRF tokens (optional)

---

## After Going Live

1. **Monitor Performance**:
   - Check page load times
   - Monitor database queries
   - Set up error logging

2. **Regular Backups**:
   - Daily database backups
   - Weekly file backups

3. **Update Security**:
   - Keep PHP updated
   - Update dependencies
   - Monitor security advisories

4. **User Support**:
   - Help users with login issues
   - Monitor bug reports
   - Add new features based on feedback

---

## Getting Help

- **Hosting Support**: Contact your hosting provider's support
- **PHP Issues**: Check error logs in your hosting control panel
- **Database Issues**: Use phpMyAdmin to debug
- **SSL Issues**: Use free SSL from Let's Encrypt

---

**Your app is secure, scalable, and ready to share with the world!** 🚀
