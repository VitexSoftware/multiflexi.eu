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

require_once __DIR__.'/init.php';

header('Cache-Control: max-age=31536000'); // Cache for 1 year
header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT'); // Expires in 1 year

$app = new \MultiFlexi\Application();

$uuid = WebPage::getRequestValue('uuid');

$svgPath = '/usr/share/multiflexi/images/'.$uuid.'.svg';

if (file_exists($svgPath)) {
    header('Content-Type: image/svg+xml');
    readfile($svgPath);
} else {
    $image = $app->listingQuery()->select('image', true)->where('uuid', $uuid)->limit(1)->fetch('image');

    if ($image) {
        // Extract content/type from data URI
        [$contentType, $base64Data] = explode(',', $image);
        [, $contentType] = explode(':', $contentType);

        // Convert base64 data to original format
        $imageData = base64_decode($base64Data, true);

        // Set proper content-type header
        header('Content-Type: '.str_replace(';base64', '', $contentType));

        // Send image data to the browser

        echo $imageData;
    } else {
        header('Content-Type: image/svg+xml');
        readfile('images/apps.svg');
    }
}
