<?php

declare(strict_types=1);

/**
 * This file is part of the MultiFlexi package
 *
 * https://multiflexi.eu/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MultiFlexi\Ui;

// Prevent PHP session from overriding our cache headers
session_cache_limiter('');

require_once __DIR__.'/init.php';

$uuid = WebPage::getRequestValue('uuid');
$contentType = 'image/svg+xml';
$imageData = null;

// Shared image search paths (development source tree first, then deb-installed locations)
$imageDirectories = [
    __DIR__.'/images',                    // Development: src/images/
    '/usr/share/multiflexi/images',       // Deb packages: app-specific SVGs
];

foreach ($imageDirectories as $dir) {
    $candidate = $dir.'/'.$uuid.'.svg';

    if (is_file($candidate)) {
        $imageData = file_get_contents($candidate);

        break;
    }
}

if ($imageData === null) {
    $imageData = file_get_contents(__DIR__.'/images/apps.svg');
}

$etag = '"'.md5($imageData).'"';

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
    http_response_code(304);

    exit;
}

header('Content-Type: '.$contentType);
header('Cache-Control: public, max-age=86400');
header('ETag: '.$etag);

echo $imageData;
