<?php

$config = require base_path('vendor/barryvdh/laravel-dompdf/config/dompdf.php');

$deployedPublicPath = realpath(base_path('../stockscan_app'));
$defaultPublicPath = realpath(base_path('public'));
$resolvedPublicPath = $deployedPublicPath ?: $defaultPublicPath;

$config['public_path'] = $resolvedPublicPath ?: null;
$config['options']['chroot'] = array_values(array_filter([
    realpath(base_path()),
    $resolvedPublicPath,
]));

return $config;
