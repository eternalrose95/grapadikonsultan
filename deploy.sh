#!/bin/bash
# ===========================================
# Laravel cPanel Deployment Script
# Run this script on your cPanel server after first deployment
# ===========================================

set -e

echo "=== Laravel cPanel Deployment Setup ==="

# Navigate to project directory
# Change this to your actual path
DEPLOY_PATH="${1:-/home/yourusername/public_html}"
cd "$DEPLOY_PATH"

echo "Working directory: $(pwd)"

# Check if .env exists, if not copy from production template
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        cp .env.production .env
        echo "✓ Created .env from .env.production template"
        echo "⚠ IMPORTANT: Edit .env with your actual database credentials!"
    else
        echo "✗ No .env file found. Please create one manually."
        exit 1
    fi
fi

# Generate application key if not set
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    php artisan key:generate --force
    echo "✓ Generated application key"
else
    echo "✓ Application key already set"
fi

# Create storage directories if they don't exist
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo "✓ Set directory permissions"

# Create storage link
php artisan storage:link --force
echo "✓ Created storage link"

# Run migrations
php artisan migrate --force
echo "✓ Ran database migrations"

# Clear and cache configuration
php artisan config:clear
php artisan config:cache
echo "✓ Cached configuration"

# Cache routes
php artisan route:cache
echo "✓ Cached routes"

# Cache views
php artisan view:cache
echo "✓ Cached views"

# Clear application cache
php artisan cache:clear
echo "✓ Cleared application cache"

echo ""
echo "=== Deployment Setup Complete ==="
echo ""
echo "Next steps:"
echo "1. Edit .env with your database credentials"
echo "2. Run 'php artisan migrate' if you updated .env"
echo "3. Access your site at your domain"
echo ""
