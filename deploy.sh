#!/bin/bash

# TCTIMS Deployment Script for DigitalOcean (Traditional LEMP Stack)
# Pulls latest code from GitHub public repository and deploys
# Repository: https://github.com/bfonua/Unifiedtransform

set -e

echo "🚀 Starting TCTIMS Deployment..."

# Check if we're in a git repository
if [ ! -d .git ]; then
    echo "❌ Error: Not a git repository!"
    echo "Clone the repository first:"
    echo "  git clone https://github.com/bfonua/Unifiedtransform.git tctims"
    exit 1
fi

# 1. Pull latest code from Git
echo "📥 Pulling latest code from Git..."
git pull origin master

# 2. Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.production.example .env
    echo "⚠️  IMPORTANT: Edit .env file with your production settings!"
    echo "Edit the file now, then press Enter to continue..."
    read
fi

# 3. Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# 4. Generate application key (if not set)
if grep -q "APP_KEY=$" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# 5. Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 6. Seed database (optional - comment out if not needed)
# echo "🌱 Seeding database..."
# php artisan db:seed --force

# 7. Clear and cache config
echo "🔄 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set permissions
echo "🔐 Setting permissions..."
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache public

# 9. Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# 10. Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
sudo systemctl restart php7.4-fpm

# 11. Reload Nginx
echo "🔄 Reloading Nginx..."
sudo systemctl reload nginx

echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "📌 Next steps:"
echo "1. Visit your domain to verify the application"
echo "2. Setup SSL with: sudo ./ssl-setup.sh yourdomain.com"
echo "3. Create admin user (see deployment guide)"
echo ""
echo "🔧 Useful commands:"
echo "  View Laravel logs: tail -f storage/logs/laravel.log"
echo "  View Nginx logs: sudo tail -f /var/log/nginx/error.log"
echo "  Restart services: sudo systemctl restart php7.4-fpm nginx"
echo ""
