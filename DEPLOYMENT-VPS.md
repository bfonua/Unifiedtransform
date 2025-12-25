# DigitalOcean Deployment Guide (Traditional LEMP Stack)

Complete step-by-step guide to deploy TCTIMS on DigitalOcean using traditional LEMP stack (Linux, Nginx, MySQL, PHP). Code is pulled directly from the public GitHub repository.

**Repository:** https://github.com/bfonua/Unifiedtransform

---

## 📋 Prerequisites

- DigitalOcean account ([Sign up here](https://www.digitalocean.com/))
- Domain name (optional but recommended)
- SSH client (Terminal, Git Bash, or PuTTY)
- Credit card for DigitalOcean billing
- Basic command line knowledge

---

## Step 1: Create DigitalOcean Droplet

### 1.1 Login to DigitalOcean

1. Go to [DigitalOcean Dashboard](https://cloud.digitalocean.com/)
2. Click **Create** → **Droplets**

### 1.2 Configure Droplet

**Choose an image:**

- Select **Ubuntu 22.04 (LTS) x64**

**Choose Size:**

- **Basic Plan** → **Regular Intel with SSD**
- **$12/month** (2GB RAM / 1 CPU / 50GB SSD / 2TB transfer) - Recommended minimum
- Or **$24/month** (4GB RAM / 2 CPUs) for better performance

**Choose a datacenter region:**

- Select closest to your users (e.g., New York, Singapore, London, San Francisco)

**Auth6ntication:**

- **Recommended:** SSH keys

  - Click **New SSH Key**
  - On your local computer:

    ```bash
    # Generate SSH key if you don't have one
    ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

    # Display public key (Windows Git Bash/Linux/Mac)
    cat ~/.ssh/id_rsa.pub

    # Windows PowerShell
    type $env:USERPROFILE\.ssh\id_rsa.pub
    ```

  - Copy the public key and paste into DigitalOcean

- **Alternative:** Password (less secure, will be emailed to you)

**Finalize Details:**

- Hostname: `tctims-production`
- Tags: `tctims`, `production`
- Backups: ✅ Enable (optional, +$2.40/month)

**Click "Create Droplet"** - Wait 1-2 minutes

### 1.3 Note Your Droplet IP

Copy the IP address shown (e.g., `159.89.123.45`)

### 1.4 SSH into Droplet

```bash
ssh root@your-droplet-ip
```

If using password, enter the password from your email.

### 1.5 Update System

```bash
apt update && apt upgrade -y
```

### 1.4 Create Non-Root User (Security)

```bash
# Create user
adduser tctadmin

# Add to sudo group
usermod -aG sudo tctadmin

# Switch to new user
su - tctadmin
```

---

## Step 2: Install LEMP Stack

### 2.1 Install Nginx

```bash
sudo apt install nginx -y

# Start and enable
sudo systemctl start nginx
sudo systemctl enable nginx

# Verify
sudo systemctl status nginx
```

### 2.2 Install MySQL

```bash
sudo apt install mysql-server -y

# Secure installation
sudo mysql_secure_installation
```

**Configuration prompts:**

- Set root password: **Yes** (use strong password)
- Remove anonymous users: **Yes**
- Disallow root login remotely: **Yes**
- Remove test database: **Yes**
- Reload privilege tables: **Yes**

**Create database:**

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE tctims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tctims_user'@'localhost' IDENTIFIED BY 'your-strong-password';
GRANT ALL PRIVILEGES ON tctims.* TO 'tctims_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2.3 Install PHP 7.4

```bash
# Add PHP repository
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 7.4 and extensions
sudo apt install php7.4-fpm php7.4-mysql php7.4-mbstring php7.4-xml php7.4-bcmath \
    php7.4-json php7.4-zip php7.4-gd php7.4-curl php7.4-tokenizer -y

# Verify
php -v
```

### 2.4 Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify
composer --version
```

### 2.5 Install Git

```bash
sudo apt install git -y
git --version
```

---

## Step 3: Clone Repository from GitHub

### 3.1 Clone Public Repository

Since the repository is public, we can clone directly via HTTPS (no authentication needed):

```bash
# Create web directory
sudo mkdir -p /var/www
cd /var/www

# Clone the public repository
sudo git clone https://github.com/bfonua/Unifiedtransform.git tctims

# Set ownership
sudo chown -R $USER:www-data /var/www/tctims

# Navigate to project
cd tctims

# Verify clone
ls -la
```

You should see all project files including `artisan`, `composer.json`, `public/`, etc.

---

## Step 4: Configure Application

### 4.1 Setup Environment File

```bash
# Copy example
cp .env.production.example .env

# Edit configuration
nano .env
```

**Update these critical values:**

```env
APP_NAME="Your School Name"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tctims
DB_USERNAME=tctims_user
DB_PASSWORD=your-strong-password
```

**Note:** Email configuration not included (not currently in use)

Press `Ctrl+X`, then `Y`, then `Enter` to save.

### 4.2 Run Deployment Script

```bash
# Make executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

This will:

- Install Composer dependencies
- Generate application key
- Run database migrations
- Cache configuration
- Set permissions

---

## Step 5: Configure Nginx

### 5.1 Create Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/tctims
```

**Paste this configuration:**

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/tctims/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Replace `yourdomain.com` with your actual domain (or use your server IP temporarily).

### 5.2 Enable Site

```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/tctims /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## Step 6: Setup SSL Certificate

### 6.1 Point Domain to Server

In your domain registrar, add an A record:

- **Type:** A
- **Name:** @
- **Value:** your-server-ip
- **TTL:** 300

Wait 5-60 minutes for DNS propagation.

### 6.2 Install SSL Certificate

```bash
# Make script executable
chmod +x ssl-setup.sh

# Run SSL setup
sudo ./ssl-setup.sh yourdomain.com
```

This automatically:

- Installs Certbot
- Obtains Let's Encrypt certificate
- Configures HTTPS redirect
- Sets up auto-renewal

---

## Step 7: Configure Firewall

```bash
# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

## Step 8: Create Admin User

### 8.1 Using Artisan Tinker

```bash
cd /var/www/tctims
php artisan tinker
```

```php
$user = new App\User();
$user->name = 'Administrator';
$user->email = 'admin@yourschool.com';
$user->password = bcrypt('SecurePassword123!');
$user->role = 'admin';
$user->active = 1;
$user->student_code = 'ADMIN001';
$user->save();

echo "Admin user created!";
exit
```

### 8.2 Login

Visit: `https://yourdomain.com`

**Login with:**

- Email: `admin@yourschool.com`
- Password: `SecurePassword123!`

**⚠️ Change password immediately after first login!**

---

## Step 9: Setup Git Deployment Workflow

### 9.1 Future Deployments

Whenever you push code to GitHub:

```bash
# SSH into server
ssh tctadmin@your-server-ip

# Navigate to project
cd /var/www/tctims

# Run deployment script
./deploy.sh
```

The script automatically:

- Pulls latest code from Git
- Installs dependencies
- Runs migrations
- Clears caches
- Restarts services

### 9.2 Automated Deployments (Optional)

Setup GitHub webhook to auto-deploy on push:

**On your server:**

```bash
# Create webhook handler
sudo nano /var/www/deploy-webhook.php
```

```php
<?php
// Simple deployment webhook
$secret = 'your-webhook-secret';

if ($_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '' === 'sha1=' . hash_hmac('sha1', file_get_contents('php://input'), $secret)) {
    shell_exec('cd /var/www/tctims && ./deploy.sh >> /var/www/deploy.log 2>&1 &');
    echo "Deployment triggered\n";
} else {
    http_response_code(403);
    echo "Forbidden\n";
}
```

**On GitHub:**

1. Go to your repo → Settings → Webhooks → Add webhook
2. Payload URL: `https://yourdomain.com/deploy-webhook.php`
3. Content type: `application/json`
4. Secret: `your-webhook-secret`
5. Select: Just the push event
6. Save

---

## 🔧 Maintenance Commands

### View Logs

```bash
# Laravel logs
tail -f /var/www/tctims/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php7.4-fpm.log
```

### Restart Services

```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php7.4-fpm

# Restart MySQL
sudo systemctl restart mysql
```

### Database Backup

```bash
# Manual backup
sudo mysqldump -u tctims_user -p tctims > ~/backup-$(date +%Y%m%d).sql

# Automated daily backup (add to crontab)
sudo crontab -e
```

Add this line:

```
0 2 * * * mysqldump -u tctims_user -pYOUR_PASSWORD tctims > /home/tctadmin/backups/tctims-$(date +\%Y\%m\%d).sql
```

### Update Application

```bash
cd /var/www/tctims
./deploy.sh
```

### Clear Caches Manually

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 🚨 Troubleshooting

### 502 Bad Gateway

```bash
# Check PHP-FPM status
sudo systemctl status php7.4-fpm

# Restart PHP-FPM
sudo systemctl restart php7.4-fpm

# Check Nginx error log
sudo tail -100 /var/log/nginx/error.log
```

### Permission Errors

```bash
cd /var/www/tctims
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache public
```

### Database Connection Error

```bash
# Verify database credentials in .env
cat .env | grep DB_

# Test MySQL connection
mysql -u tctims_user -p tctims

# Check MySQL status
sudo systemctl status mysql
```

### Git Pull Errors

```bash
# Discard local changes
git reset --hard HEAD

# Pull again
git pull origin master
```

### Application Key Missing

```bash
php artisan key:generate
```

---

## 💰 Cost Breakdown

**Monthly Costs (DigitalOcean):**

- **Droplet (2GB):** $12/month
- **Droplet (4GB):** $24/month (recommended for better performance)
- **Backups:** $2.40-4.80/month (20% of droplet cost, optional)
- **Domain:** ~$1/month ($12/year from registrar)

**Total: $13-29/month**

**Free:**

- SSL Certificate (Let's Encrypt)
- Bandwidth (2TB included)

---

## 🆚 Comparison: DigitalOcean LEMP vs Forge

| Feature          | DigitalOcean LEMP (This Guide) | Laravel Forge          |
| ---------------- | ------------------------------ | ---------------------- |
| Setup Time       | 1-2 hours                      | 30 minutes             |
| Difficulty       | Medium                         | Easy                   |
| Docker Knowledge | ❌ Not needed                  | ❌ Not needed          |
| Git Deployment   | ✅ Manual pull (./deploy.sh)   | ✅ Auto-deploy on push |
| Cost             | $13-29/month                   | $25-37/month           |
| Control          | Full control                   | Managed                |
| Updates          | Manual                         | Automatic              |
| Best For         | Budget-conscious, hands-on     | Easy management        |

---

## 🔒 Security Checklist

- [ ] Non-root user created
- [ ] SSH key authentication enabled
- [ ] Firewall configured (UFW)
- [ ] SSL certificate installed
- [ ] APP_DEBUG=false in .env
- [ ] Strong database password
- [ ] Strong admin password
- [ ] File permissions correct (775 storage)
- [ ] Regular backups scheduled
- [ ] System updates automated

---

## 🎯 Next Steps

1. ✅ Application deployed
2. ✅ SSL configured
3. ✅ Admin user created
4. 📱 Import student data
5. � Create teacher accounts
6. 📊 Configure fee structures
7. 🏫 Add academic sessions
8. 🔐 Setup automated backups
9. 📈 Configure monitoring (optional)

---

## 📞 Support Resources

- **TCTIMS Repository:** https://github.com/bfonua/Unifiedtransform
- **Laravel Documentation:** https://laravel.com/docs/5.6
- **DigitalOcean Community:** https://www.digitalocean.com/community/tutorials
- **DigitalOcean Support:** Available in dashboard for paid customers
- **Nginx Documentation:** https://nginx.org/en/docs/
- **Let's Encrypt:** https://letsencrypt.org/docs/

---

## 🎉 Congratulations!

You've successfully deployed TCTIMS on DigitalOcean using traditional LEMP stack!

**Your deployment features:**

- ✅ Direct Git pull deployments from public repo
- ✅ No Docker complexity
- ✅ Full server control
- ✅ Automatic SSL
- ✅ Production-optimized
- ✅ Cost-effective ($13-29/month)
- ✅ DigitalOcean's reliable infrastructure

Focus on managing your school, not managing containers! 🚀
