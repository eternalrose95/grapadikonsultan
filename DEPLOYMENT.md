# CI/CD Deployment Guide: GitHub to cPanel (FTP Only)

## Overview
Panduan untuk deploy Laravel app dari GitHub ke cPanel hosting menggunakan GitHub Actions via FTP.

---

## Step 1: Setup GitHub Secrets

Buka repository GitHub: **Settings > Secrets and variables > Actions**

Tambahkan secrets berikut:

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `FTP_SERVER` | FTP server hostname | `ftp.yourdomain.com` atau `yourdomain.com` |
| `FTP_USERNAME` | FTP username dari cPanel | `username@yourdomain.com` |
| `FTP_PASSWORD` | FTP password | `your_ftp_password` |
| `FTP_SERVER_DIR` | Target directory | `/public_html/` |

> **Tip:** FTP credentials bisa dilihat di cPanel > FTP Accounts

---

## Step 2: Setup cPanel (Pertama Kali)

### 2.1 Create MySQL Database
1. Login ke cPanel
2. Buka **MySQL Databases**
3. Create database: `cpuser_agensi`
4. Create user dengan password kuat
5. Add user ke database dengan **ALL PRIVILEGES**

### 2.2 Upload .env File
Setelah deploy pertama, buat file `.env` di cPanel:

1. Buka **File Manager** di cPanel
2. Navigate ke `public_html`
3. Copy isi dari `.env.production` 
4. Buat file baru `.env` dan paste
5. Edit credentials:

```env
APP_KEY=   # akan di-generate
APP_URL=https://yourdomain.com

DB_DATABASE=cpuser_agensi
DB_USERNAME=cpuser_dbuser  
DB_PASSWORD=your_secure_password

UNSPLASH_ACCESS_KEY=your_key_here
```

### 2.3 Run Post-Deployment Commands
Di **cPanel Terminal** (atau File Manager > Terminal):

```bash
cd public_html

# Generate app key (sekali saja)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 3: Deploy

### Automatic Deployment
Push ke branch `main`:
```bash
git add .
git commit -m "Update feature"
git push origin main
```

Deploy otomatis berjalan ~2-3 menit.

### Manual Deployment
1. Buka GitHub repository
2. Go to **Actions** tab
3. Click **Deploy to cPanel (FTP Only)**
4. Click **Run workflow**

---

## Step 4: Setelah Setiap Deploy

Jalankan di cPanel Terminal:
```bash
cd public_html
php artisan migrate --force
php artisan config:cache
php artisan route:cache  
php artisan view:cache
```

Atau gunakan script yang sudah disediakan:
```bash
bash deploy.sh /home/username/public_html
```

---

## Troubleshooting

### Error: 500 Internal Server Error
```bash
# Check permissions
chmod -R 755 storage bootstrap/cache

# Check logs
cat storage/logs/laravel.log
```

### Error: Class not found
```bash
composer dump-autoload
php artisan config:cache
```

### Error: SQLSTATE Connection refused
- Cek DB credentials di `.env`
- Pastikan user punya akses ke database

### Error: The stream or file could not be opened
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Storage images not showing
```bash
php artisan storage:link
```

---

## Quick Reference

| Action | Command |
|--------|---------|
| Clear all cache | `php artisan optimize:clear` |
| Re-cache all | `php artisan optimize` |
| Check routes | `php artisan route:list` |
| Run migrations | `php artisan migrate --force` |
| Rollback migration | `php artisan migrate:rollback` |

---

## Security Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong database password
- [ ] HTTPS enabled (SSL certificate)
- [ ] `.env` protected (check di browser: yourdomain.com/.env harus 403/404)
