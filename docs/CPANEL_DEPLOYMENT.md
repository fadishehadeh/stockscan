# Fresh cPanel Deployment for `stockscan.greenproductionstudio.com`

## Target Layout
- cPanel subdomain URL: `https://stockscan.greenproductionstudio.com`
- cPanel subdomain document root: `public_html/greenproductionstudio.com/stockscan_app`
- Public web root folder: `public_html/greenproductionstudio.com/stockscan_app`
- Private Laravel app folder: `public_html/greenproductionstudio.com/stockscan_app_private`

Only the contents of Laravel's `public/` folder belong in the public document root.
Do not place `app/`, `config/`, `routes/`, `.env`, or `vendor/` inside the public folder.

## Generate The Fresh Package Locally
Run:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\prepare-cpanel-deploy.ps1
```

This creates:
- `dist/cpanel-fresh/public_html/greenproductionstudio.com/stockscan_app`
- `dist/cpanel-fresh/public_html/greenproductionstudio.com/stockscan_app_private`
- `dist/cpanel-fresh/stockscan_app.zip`
- `dist/cpanel-fresh/stockscan_app_private.zip`

The generated `index.php` is written without a UTF-8 BOM so it is safe to upload to cPanel.

## What To Upload
### Upload to `public_html/greenproductionstudio.com/stockscan_app_private`
- `app`
- `bootstrap`
- `config`
- `database`
- `resources`
- `routes`
- `storage`
- `vendor`
- `artisan`
- `composer.json`
- `composer.lock`
- `.env.example`

### Upload to `public_html/greenproductionstudio.com/stockscan_app`
- `index.php`
- `.htaccess`
- `build`
- `favicon.ico`
- `robots.txt`
- `CREATE_STORAGE_SYMLINK.txt`

## Required Manual Steps In cPanel
1. Repoint the subdomain document root to `public_html/greenproductionstudio.com/stockscan_app`.
2. Create/import the MySQL database through cPanel + phpMyAdmin.
3. Create `public_html/greenproductionstudio.com/stockscan_app_private/.env` from `.env.example`.
4. Confirm:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://stockscan.greenproductionstudio.com`
5. Ensure these folders are writable:
   - `stockscan_app_private/storage`
   - `stockscan_app_private/bootstrap/cache`
6. Create the storage symlink in the public folder:
   - `public_html/greenproductionstudio.com/stockscan_app/storage`
   - points to `../stockscan_app_private/storage/app/public`
7. Set the subdomain to PHP `8.3`.
8. From SSH or Terminal, run:

```bash
cd ~/public_html/greenproductionstudio.com/stockscan_app_private
php artisan optimize:clear
php artisan migrate --force
```

## Production `.env` Minimum
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stockscan.greenproductionstudio.com

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=true
```

## Verify After Upload
- `curl -L https://stockscan.greenproductionstudio.com` returns HTML, not BOM bytes
- Login page loads with styling
- `build/manifest.json` contains `resources/css/app.css` and `resources/js/app.js`
- `php artisan migrate:status` shows all migrations present
- Dashboard loads after owner login
- Product images load from `/storage/...`
- Alerts and reports display data
- Creating a product and uploading an image works
