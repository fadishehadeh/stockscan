param(
    [string]$ProjectRoot = (Get-Location).Path,
    [string]$OutputRoot = "dist\cpanel-fresh",
    [string]$SiteParentFolder = "greenproductionstudio.com",
    [string]$PublicFolderName = "stockscan_app",
    [string]$PrivateFolderName = "stockscan_app_private"
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

function Write-Utf8NoBomFile {
    param(
        [string]$Path,
        [string]$Content
    )

    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($Path, $Content, $utf8NoBom)
}

$root = (Resolve-Path $ProjectRoot).Path
$distRoot = Join-Path $root $OutputRoot
$publicBaseRoot = Join-Path $distRoot "public_html"
$siteRoot = Join-Path $publicBaseRoot $SiteParentFolder
$publicRoot = Join-Path $siteRoot $PublicFolderName
$privateRoot = Join-Path $siteRoot $PrivateFolderName

if (Test-Path $distRoot) {
    Remove-Item -LiteralPath $distRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $publicRoot -Force | Out-Null
New-Item -ItemType Directory -Path $privateRoot -Force | Out-Null

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
    $target = Join-Path $privateRoot $dir
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
        Copy-Item -LiteralPath $source -Destination (Join-Path $privateRoot $file) -Force
    }
}

Copy-FilteredTree -Source (Join-Path $root "public") -Destination $publicRoot -ExcludeDirs @("storage")

$indexPath = Join-Path $publicRoot "index.php"
$indexContent = @"
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(`$maintenance = __DIR__.'/../$PrivateFolderName/storage/framework/maintenance.php')) {
    require `$maintenance;
}

require __DIR__.'/../$PrivateFolderName/vendor/autoload.php';

/** @var Application `$app */
`$app = require_once __DIR__.'/../$PrivateFolderName/bootstrap/app.php';

`$app->handleRequest(Request::capture());
"@

Write-Utf8NoBomFile -Path $indexPath -Content $indexContent

$storageInstructions = @"
Create a symlink in this folder after upload:

storage -> ../$PrivateFolderName/storage/app/public

The cPanel subdomain document root should point to:
public_html/$SiteParentFolder/$PublicFolderName

Do not place the private Laravel app inside the public web root.
"@

Write-Utf8NoBomFile -Path (Join-Path $publicRoot "CREATE_STORAGE_SYMLINK.txt") -Content $storageInstructions

Copy-Item -LiteralPath (Join-Path $root "docs\PRODUCTION_HARDENING.md") -Destination (Join-Path $distRoot "PRODUCTION_HARDENING.md") -Force
Copy-Item -LiteralPath (Join-Path $root "docs\CPANEL_DEPLOYMENT.md") -Destination (Join-Path $distRoot "CPANEL_DEPLOYMENT.md") -Force

$privateZip = Join-Path $distRoot "$PrivateFolderName.zip"
$publicZip = Join-Path $distRoot "$PublicFolderName.zip"

Compress-Archive -Path (Join-Path $privateRoot '*') -DestinationPath $privateZip -Force
Compress-Archive -Path (Join-Path $publicRoot '*') -DestinationPath $publicZip -Force

Write-Host "Prepared fresh cPanel deployment package:"
Write-Host "  Public root:  $publicRoot"
Write-Host "  Private app:  $privateRoot"
Write-Host "  Zip files:    $publicZip, $privateZip"
