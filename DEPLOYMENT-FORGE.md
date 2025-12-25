# Laravel Forge Deployment Guide

Complete step-by-step guide to deploy TCTIMS on Laravel Forge (Easiest Method - No Docker Required).

---

## 📋 What is Laravel Forge?

Laravel Forge is a managed server hosting platform that:

- ✅ **Automatically configures servers** (no Docker needed)
- ✅ **One-click deployments** from GitHub/GitLab/Bitbucket
- ✅ **Free SSL certificates** (Let's Encrypt)
- ✅ **Zero-downtime deployments**
- ✅ **Built-in monitoring & backups**
- ✅ **Queue management & scheduled tasks**
- ✅ **Perfect for Laravel applications**

**Cost:** $12/month (Forge) + $12-24/month (Server) = **$24-36/month total**

---

## Prerequisites

- Laravel Forge account ([Sign up here](https://forge.laravel.com/))
- DigitalOcean/AWS/Linode account
- GitHub/GitLab/Bitbucket account (for your code)
- Domain name (optional but recommended)

---

## Step 1: Setup Git Repository

### 1.1 Push Your Code to GitHub

**If you haven't already:**

```bash
# Initialize git (if not already done)
cd /path/to/your/tctims
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - TCTIMS ready for deployment"

# Create repository on GitHub
# Go to https://github.com/new
# Create a new repository named: Unifiedtransform

# Add remote and push
git remote add origin https://github.com/bfonua/Unifiedtransform.git
git branch -M master
git push -u origin master
```

### 1.2 Verify Repository

- Go to your GitHub repository
- Ensure all files are there
- Note: `.env` file won't be pushed (it's in .gitignore - this is correct!)

---

## Step 2: Sign Up for Laravel Forge

### 2.1 Create Forge Account

1. Go to [Laravel Forge](https://forge.laravel.com/)
2. Click **Get Started**
3. Choose plan: **Hobby Plan ($12/month)** is perfect
4. Enter payment details
5. Verify your email

### 2.2 Connect Source Control

1. After login, go to **Account** → **Source Control**
2. Click **Connect GitHub**
3. Authorize Forge to access your repositories
4. Select your username/organization

---

## Step 3: Connect Server Provider

### 3.1 Connect DigitalOcean to Forge

1. In Forge: **Account** → **Server Providers**
2. Click **DigitalOcean**
3. You'll need a DigitalOcean API token:
   - Go to [DigitalOcean API Tokens](https://cloud.digitalocean.com/account/api/tokens)
   - Click **Generate New Token**
   - Name: `Laravel Forge`
   - Check: ✅ Read & Write
   - Click **Generate Token**
   - **Copy the token** (you can't see it again!)
4. Paste token into Forge
5. Click **Connect**

**Alternative Providers:**

- **AWS:** Similar process with AWS credentials
- **Linode:** Use Linode API token
- **Vultr:** Use Vultr API key

---

## Step 4: Create a Server

### 4.1 Create New Server in Forge

1. Click **Servers** → **Create Server**
2. Choose your provider: **DigitalOcean**

### 4.2 Configure Server

**Credentials:**

- Name: `TCTIMS Production`

**Server Size:**

- **2GB** ($12/month) - Minimum for production
- **4GB** ($24/month) - Recommended for better performance

**Region:**

- Choose closest to your users (e.g., New York, Singapore, London)

**PHP Version:**

- Select: **PHP 7.4**
- ⚠️ **NOT PHP 8.0+** (Project requires PHP 7.x - see composer.json)
- PHP 7.4 is the latest stable PHP 7 version with security support

**Database:**

- Database Type: **MySQL 8.0**
- Database Name: `tctims`

**Server Type:**

- Select: **App Server**

**Advanced Options (expand):**

- ✅ Enable Weekly Backups (Recommended: +$2.40/month)
- OPcache: ✅ Enabled (already checked)

### 4.3 Create Server

- Click **Create Server**
- **Wait 5-10 minutes** for provisioning
- Forge will:
  - Create DigitalOcean droplet
  - Install PHP, Nginx, MySQL
  - Configure firewall
  - Install Composer
  - Set up SSL (later)

---

## Step 5: Create a Site

### 5.1 Add New Site

Once server status shows **Active**:

1. Click on your server name
2. Click **Sites** tab → **New Site**

### 5.2 Configure Site

**Root Domain:**

- If you have a domain: `yourdomain.com`
- No domain yet: Use server IP `159.89.123.45.nip.io`
  - Forge will show the server IP
  - Example: `159.89.123.45.nip.io`

**Aliases (optional):**

- Add: `www.yourdomain.com` (if using custom domain)

**Project Type:**

- Select: **General PHP / Laravel**

**Web Directory:**

- Leave as: `/public`

**PHP Version:**

- Select: **php74**

**Create Wildcard Sub-Domains:**

- Leave unchecked

### 5.3 Create Site

- Click **Add Site**
- Wait 1-2 minutes for site creation

---

## Step 6: Deploy Your Application

### 6.1 Connect Repository

1. Click on your site (e.g., `yourdomain.com`)
2. Scroll to **Git Repository** section
3. Click **Install Repository**

**Repository Settings:**

- Provider: **GitHub**
- Repository: `bfonua/Unifiedtransform`
- Branch: `master`
- ✅ **Install Composer Dependencies**

Click **Install Repository**

### 6.2 Configure Environment Variables

1. In your site, click **Environment** tab
2. Replace default `.env` with your configuration:

```env
APP_NAME="Your School Name"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tctims
DB_USERNAME=forge
DB_PASSWORD=AUTOMATICALLY_SET_BY_FORGE

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

**Note:**

- `DB_PASSWORD` is already set by Forge automatically
- Don't change database credentials unless you know what you're doing
- Email configuration removed (not currently in use)

3. Click **Save**

### 6.3 Configure Deployment Script

1. Click **Deploy Script** tab
2. Update the deployment script to:

```bash
cd /home/forge/yourdomain.com

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader --no-dev

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
    $FORGE_PHP artisan storage:link
fi
```

3. Click **Save**

### 6.4 Generate Application Key

1. Click **Commands** icon (terminal icon)
2. Run command:

```bash
php artisan key:generate
```

3. Click **Run Command**

### 6.5 Initial Deployment

1. Go back to your site
2. Click **Deploy Now** button
3. Watch the deployment log
4. Wait 2-5 minutes for completion

---

## Step 7: Configure Database

### 7.1 Run Migrations

1. Click **Commands**
2. Run:

```bash
php artisan migrate --force
```

### 7.2 (Optional) Seed Database

If you have seeders:

```bash
php artisan db:seed --force
```

---

## Step 8: Setup SSL Certificate

### 8.1 Point Domain to Server (If Using Custom Domain)

1. Go to your domain registrar (Namecheap, GoDaddy, etc.)
2. Add A record:
   - Type: `A`
   - Name: `@`
   - Value: `your-server-ip` (shown in Forge)
   - TTL: `300`
3. Add www record:
   - Type: `A`
   - Name: `www`
   - Value: `your-server-ip`
   - TTL: `300`

**Wait 5-60 minutes** for DNS propagation

### 8.2 Enable SSL in Forge

1. In your site, click **SSL** tab
2. Choose: **LetsEncrypt**
3. Domains: Ensure `yourdomain.com` and `www.yourdomain.com` are listed
4. Click **Obtain Certificate**
5. Wait 1-2 minutes

**Automatic:**

- ✅ Certificate installed
- ✅ Auto-renewal configured
- ✅ HTTPS forced

### 8.3 Test Your Site

Visit: `https://yourdomain.com`

You should see a secure padlock! 🔒

---

## Step 9: Create Admin User

### 9.1 Using Forge SSH

1. In Forge, click your server
2. Click **SSH Keys** tab
3. Copy the command shown (e.g., `ssh forge@your-ip`)
4. Run in your terminal:

```bash
ssh forge@your-server-ip
```

### 9.2 Navigate to Site

```bash
cd yourdomain.com
```

### 9.3 Create Admin User

```bash
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

Type `exit` to close SSH.

### 9.4 Login

- Visit: `https://yourdomain.com`
- Login with:
  - Email: `admin@yourschool.com`
  - Password: `SecurePassword123!`

**⚠️ Change this password immediately!**

---

## Step 10: Configure Scheduled Tasks (Cron Jobs)

### 10.1 Enable Scheduler

1. In Forge, go to your server
2. Click **Scheduler** tab
3. Click **New Scheduled Job**
4. Command: `php artisan schedule:run`
5. Frequency: **Every Minute**
6. User: `forge`
7. Click **Schedule Job**

This enables Laravel's task scheduler for automated tasks.

---

## Step 11: Configure Queue Workers (Optional)

If your app uses queues:

1. In Forge, go to your server
2. Click **Daemons** tab
3. Click **New Daemon**
4. Command: `php artisan queue:work --sleep=3 --tries=3`
5. Directory: `/home/forge/yourdomain.com`
6. User: `forge`
7. Click **Create Daemon**

---

## 🎯 Automatic Deployments

### Enable Auto-Deploy from Git

1. In your site, scroll to **Git Repository**
2. Toggle: ✅ **Quick Deploy**
3. Now, every time you push to master:
   - Forge automatically pulls changes
   - Runs migrations
   - Clears caches
   - Zero downtime!

### Manual Deployment

Click **Deploy Now** button anytime

---

## 🔧 Maintenance & Management

### View Logs

**In Forge:**

1. Click your site
2. Click **Logs** tab
3. Choose log type:
   - Laravel logs
   - Nginx access logs
   - Nginx error logs

**Via SSH:**

```bash
ssh forge@your-server-ip
cd yourdomain.com
tail -f storage/logs/laravel.log
```

### Update Application

**Method 1: Auto-deploy (Recommended)**

1. Commit and push changes to GitHub:

```bash
git add .
git commit -m "Update feature"
git push origin master
```

2. Forge automatically deploys!

**Method 2: Manual Deploy**

1. Push changes to GitHub
2. In Forge, click **Deploy Now**

### Run Artisan Commands

1. In Forge, click your site
2. Click **Commands** (terminal icon)
3. Enter command:

```bash
php artisan cache:clear
php artisan config:cache
```

4. Click **Run Command**

### Database Backups

**Automatic (Recommended):**

1. Server settings → **Backups** tab
2. Configure:
   - Frequency: Daily
   - Time: 2:00 AM
   - Retention: 7 days
3. Click **Schedule Backup**

**Manual Backup:**

1. Backups tab → **Backup Now**

**Download Backup:**

- Click backup in list
- Click **Download**

---

## 🚨 Troubleshooting

### Application Not Loading

1. Check site status in Forge (should be green)
2. View logs: Site → **Logs** → **Laravel**
3. Check deployment log: Click latest deployment

### 500 Error

```bash
# SSH into server
ssh forge@your-server-ip
cd yourdomain.com

# Check logs
tail -100 storage/logs/laravel.log

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ensure permissions
chmod -R 755 storage bootstrap/cache
```

### Database Connection Error

1. Check `.env` file: Site → **Environment**
2. Verify database exists: Server → **Database** tab
3. Test connection:

```bash
php artisan tinker
DB::connection()->getPdo();
```

### SSL Certificate Not Working

1. Verify domain points to server IP
2. Check SSL status: Site → **SSL** tab
3. Try re-issuing certificate:
   - Delete certificate
   - Obtain new one

### Deployment Failed

1. Check deployment log
2. Common issues:
   - Composer dependencies error → Update `composer.json`
   - Migration error → Check migration files
   - Permission error → Check file permissions

---

## 💰 Cost Breakdown

**Monthly Costs:**

- **Laravel Forge:** $12/month
- **DigitalOcean Droplet (2GB):** $12/month
- **DigitalOcean Droplet (4GB):** $24/month (recommended)
- **Backups:** $2.40/month (optional, 20% of droplet)
- **Domain:** ~$1/month ($12/year)

**Total: $25-37/month**

**Included Free:**

- SSL Certificate (Let's Encrypt)
- Automatic deployments
- Server monitoring
- Log management
- Security updates

---

## 🆚 Forge vs Manual Docker Setup

| Feature     | Laravel Forge | Manual Docker   |
| ----------- | ------------- | --------------- |
| Setup Time  | 30 minutes    | 2-4 hours       |
| Difficulty  | Beginner      | Intermediate    |
| Deployments | One-click     | Manual commands |
| Monitoring  | Built-in      | Setup yourself  |
| Backups     | Automated     | Setup yourself  |
| SSL         | One-click     | Manual setup    |
| Updates     | Automatic     | Manual          |
| Cost        | $24-36/mo     | $12-24/mo       |
| Support     | Forge Support | Community       |

**Recommendation:** Use Forge unless:

- You need extreme customization
- Budget is very tight
- You enjoy server management

---

## 🎯 Next Steps

1. ✅ Application deployed
2. ✅ SSL configured
3. ✅ Admin user created
4. ✅ Auto-deployments enabled
5. 📱 Import student data
6. � Create teacher accounts
7. 📊 Configure fee structures
8. 🏫 Add academic sessions
9. 🔐 Update all passwords
10. 📅 Schedule automated backups

---

## 📞 Support Resources

- **Forge Documentation:** https://forge.laravel.com/docs
- **Forge Support:** support@laravel.com
- **Community:** https://laracasts.com/discuss
- **Video Tutorials:** https://laracasts.com

---

## 🔒 Security Checklist

- [ ] SSL/HTTPS enabled
- [ ] Strong admin password
- [ ] Firewall enabled (automatic in Forge)
- [ ] Database backups scheduled
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Two-factor authentication (for Forge account)
- [ ] IP whitelist for SSH (optional)
- [ ] Regular security updates (automatic in Forge)

---

## 🎉 Congratulations!

You've successfully deployed TCTIMS using Laravel Forge!

**Benefits you now have:**

- ✅ Production-ready server
- ✅ Automatic HTTPS
- ✅ One-click deployments
- ✅ Automated backups
- ✅ Server monitoring
- ✅ Professional setup

Focus on your school management, not server management! 🚀
