#!/bin/bash

# SSL Setup with Let's Encrypt for TCTIMS
# This script sets up free SSL certificates using Certbot

set -e

echo "🔒 SSL Certificate Setup"
echo ""

# Check if domain is provided
if [ -z "$1" ]; then
    echo "Usage: sudo ./ssl-setup.sh yourdomain.com"
    echo "Example: sudo ./ssl-setup.sh school.example.com"
    exit 1
fi

DOMAIN=$1
EMAIL="admin@$DOMAIN"  # Change if needed

echo "Domain: $DOMAIN"
echo "Email: $EMAIL"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

# 1. Install Certbot
echo "📦 Installing Certbot..."
apt-get update
apt-get install -y certbot python3-certbot-nginx

# 2. Obtain certificate using nginx plugin
echo "🔐 Obtaining SSL certificate..."
certbot --nginx \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    --redirect \
    -d $DOMAIN \
    -d www.$DOMAIN

# 3. Test nginx configuration
echo "✅ Testing Nginx configuration..."
nginx -t

# 4. Reload nginx
echo "🔄 Reloading Nginx..."
systemctl reload nginx

echo ""
echo "✅ SSL certificate installed successfully!"
echo ""
echo "📌 Your site is now secured with HTTPS!"
echo "   Visit: https://$DOMAIN"
echo ""
echo "🔄 Certificate will auto-renew before expiration"
echo "   Test renewal: sudo certbot renew --dry-run"
echo ""
# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name $DOMAIN;
    return 301 https://\$host\$request_uri;
}

# HTTPS Server
server {
    listen 443 ssl http2;
    server_name $DOMAIN;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Application
    index index.php index.html;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /var/www/public;
    
    # PHP Handler
    location ~ \.php\$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
    
    # URL Rewriting
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
        gzip_static on;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# 5. Update docker-compose to mount certificates
echo "📋 Updating docker-compose..."
# Certificates are already mounted via /etc/letsencrypt in volumes section

# 6. Start nginx
echo "▶️  Starting nginx..."
docker-compose up -d webserver

# 7. Set up auto-renewal
echo "⏰ Setting up auto-renewal..."
cat > /etc/cron.d/certbot-renew <<EOF
0 3 * * * root certbot renew --quiet --deploy-hook "docker-compose -f $(pwd)/docker-compose.yml restart webserver"
EOF

echo ""
echo "✅ SSL Setup completed!"
echo ""
echo "🌐 Your site should now be accessible at: https://$DOMAIN"
echo "🔄 Certificates will auto-renew every 60 days"
echo ""
