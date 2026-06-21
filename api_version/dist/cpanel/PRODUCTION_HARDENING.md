# StockScan Production Hardening

## Shared Hosting Checklist
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Set `APP_URL` to the live HTTPS URL
- Configure `DB_*` to the production MySQL database
- Configure `MAIL_*` so low-stock email notifications can be delivered
- Keep `SESSION_DRIVER=database` and run migrations before going live
- Set `SESSION_SECURE_COOKIE=true`
- Set `SESSION_SAME_SITE=strict` unless you have a specific cross-site need
- Set `SESSION_ENCRYPT=true`
- Ensure `storage/` and `bootstrap/cache/` are writable
- Point the web root to Laravel `public/`
- Force HTTPS at the hosting layer and use secure session cookies where supported

## Database Backup
- Recommended manual backup command:

```bash
mysqldump -u YOUR_DB_USER -p YOUR_DB_NAME > stockscan-backup.sql
```

- Recommended restore command:

```bash
mysql -u YOUR_DB_USER -p YOUR_DB_NAME < stockscan-backup.sql
```

- Export backups before major imports, deployments, or schema changes
- Keep at least one off-host copy of the SQL dump

## Operational Notes
- The barcode scanner stays local and does not connect directly to PHP
- Low-stock emails only send to active users with an email address
- If mail is not configured, in-app alerts still work
- Product uploads are restricted to standard raster image formats and should remain under `storage/app/public/products`
- Do not allow the web server to execute uploaded files from storage paths
- Test login throttling, CSV import, and alert emails after deployment
