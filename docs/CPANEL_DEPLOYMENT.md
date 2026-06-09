# cPanel Deployment for `stockscan.greenproductionstudio.com`

## Folder Structure
- Private Laravel app: `~/stockscan_app`
- Public web root: `~/stockscan.greenproductionstudio.com`

Only upload the contents of Laravel's local `public/` folder into the subdomain root.
Do not upload the full Laravel app into the public web root.

## Files Generated Locally
Run:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\prepare-cpanel-deploy.ps1
```

This creates:
- `dist/cpanel/stockscan_app`
- `dist/cpanel/stockscan.greenproductionstudio.com`
- `dist/cpanel/stockscan_app.zip`
- `dist/cpanel/stockscan.greenproductionstudio.com.zip`

## What To Upload
### Upload to `~/stockscan_app`
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
- `.env`

### Upload to `~/stockscan.greenproductionstudio.com`
- `index.php`
- `.htaccess`
- `build`
- `favicon.ico`
- `robots.txt`
- other public assets

## Required Manual Steps In cPanel
1. Create/import the MySQL database through cPanel + phpMyAdmin.
2. Put production values into `~/stockscan_app/.env`.
3. Confirm `APP_URL=https://stockscan.greenproductionstudio.com`
4. Ensure:
   - `storage/` is writable
   - `bootstrap/cache/` is writable
5. Create the storage symlink:
   - `~/stockscan.greenproductionstudio.com/storage`
   - points to `../stockscan_app/storage/app/public`
6. Set the subdomain to PHP `8.3`.

## Production `.env` Minimum
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stockscan.greenproductionstudio.com

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_ENCRYPT=true
```

## Verify After Upload
- Login page loads with styling
- Dashboard loads after owner login
- Product images load from `/storage/...`
- Alerts and reports display data
- Creating a product and uploading an image works
