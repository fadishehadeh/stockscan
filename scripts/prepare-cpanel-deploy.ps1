param(
    [string]$ProjectRoot = (Get-Location).Path,
    [string]$OutputRoot = "dist\cpanel",
    [string]$AppFolderName = "stockscan_app",
    [string]$PublicFolderName = "stockscan.greenproductionstudio.com"
)

$ErrorActionPreference = "Stop"

function Copy-FilteredTree {
    param(
        [string]$Source,
        [string]$Destination,
        [string[]]$ExcludeDirs = @(),
        [string[]]$ExcludeFiles = @()
    )

    Get-ChildItem -LiteralPath $Source -Force | ForEach-Object {
        if ($ExcludeDirs -contains $_.Name -and $_.PSIsContainer) {
            return
        }

        if ($ExcludeFiles -contains $_.Name -and -not $_.PSIsContainer) {
            return
        }

        $target = Join-Path $Destination $_.Name

        if ($_.PSIsContainer) {
            New-Item -ItemType Directory -Path $target -Force | Out-Null
            Copy-FilteredTree -Source $_.FullName -Destination $target -ExcludeDirs $ExcludeDirs -ExcludeFiles $ExcludeFiles
            return
        }

        Copy-Item -LiteralPath $_.FullName -Destination $target -Force
    }
}

$root = (Resolve-Path $ProjectRoot).Path
$distRoot = Join-Path $root $OutputRoot
$appRoot = Join-Path $distRoot $AppFolderName
$publicRoot = Join-Path $distRoot $PublicFolderName

if (Test-Path $distRoot) {
    Remove-Item -LiteralPath $distRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $appRoot -Force | Out-Null
New-Item -ItemType Directory -Path $publicRoot -Force | Out-Null

$privateDirs = @(
    "app",
    "bootstrap",
    "config",
    "database",
    "resources",
    "routes",
    "storage",
    "vendor"
)

foreach ($dir in $privateDirs) {
    $source = Join-Path $root $dir
    $target = Join-Path $appRoot $dir
    Copy-Item -LiteralPath $source -Destination $target -Recurse -Force
}

$privateFiles = @(
    ".env.example",
    ".htaccess",
    "artisan",
    "composer.json",
    "composer.lock",
    "package.json",
    "package-lock.json",
    "phpunit.xml",
    "README.md",
    "vite.config.js"
)

foreach ($file in $privateFiles) {
    $source = Join-Path $root $file
    if (Test-Path $source) {
        Copy-Item -LiteralPath $source -Destination (Join-Path $appRoot $file) -Force
    }
}

Copy-FilteredTree -Source (Join-Path $root "public") -Destination $publicRoot -ExcludeDirs @("storage")

$indexPath = Join-Path $publicRoot "index.php"
$indexContent = @"
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(`$maintenance = __DIR__.'/../$AppFolderName/storage/framework/maintenance.php')) {
    require `$maintenance;
}

require __DIR__.'/../$AppFolderName/vendor/autoload.php';

/** @var Application `$app */
`$app = require_once __DIR__.'/../$AppFolderName/bootstrap/app.php';

`$app->handleRequest(Request::capture());
"@

Set-Content -LiteralPath $indexPath -Value $indexContent -Encoding UTF8

$storageInstructions = @"
Create a symlink in this folder after upload:

storage -> ../$AppFolderName/storage/app/public

If cPanel File Manager cannot create the symlink directly, ask hosting support to create it for the subdomain root.
"@

Set-Content -LiteralPath (Join-Path $publicRoot "CREATE_STORAGE_SYMLINK.txt") -Value $storageInstructions -Encoding UTF8

Copy-Item -LiteralPath (Join-Path $root "docs\PRODUCTION_HARDENING.md") -Destination (Join-Path $distRoot "PRODUCTION_HARDENING.md") -Force
Copy-Item -LiteralPath (Join-Path $root "docs\CPANEL_DEPLOYMENT.md") -Destination (Join-Path $distRoot "CPANEL_DEPLOYMENT.md") -Force

$appZip = Join-Path $distRoot "$AppFolderName.zip"
$publicZip = Join-Path $distRoot "$PublicFolderName.zip"

Compress-Archive -Path (Join-Path $appRoot '*') -DestinationPath $appZip -Force
Compress-Archive -Path (Join-Path $publicRoot '*') -DestinationPath $publicZip -Force

Write-Host "Prepared cPanel deployment package:"
Write-Host "  Private app: $appRoot"
Write-Host "  Public root: $publicRoot"
Write-Host "  Zip files:   $appZip, $publicZip"
