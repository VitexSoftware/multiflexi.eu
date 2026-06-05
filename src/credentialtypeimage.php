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

header('Cache-Control: max-age=31536000');
header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');

$uuid = (string) WebPage::getRequestValue('uuid');
$logo = basename((string) WebPage::getRequestValue('logo'));

if ($uuid) {
    $prototype = new \MultiFlexi\Hub\CredentialProtoType();
    $row = $prototype->listingQuery()
        ->select('logo', true)
        ->where('uuid', $uuid)
        ->limit(1)
        ->fetch();

    if ($row && !empty($row['logo'])) {
        $logo = basename((string) $row['logo']);
    }
}

$imagePath = null;
$imageDirectories = [
    __DIR__.'/images',                    // Development: src/images/
    '/usr/share/multiflexi/images',       // Deb packages: app-specific SVGs
];

if ($logo) {
    $imagePath = findCredentialPrototypeImage($logo, $imageDirectories);
}
if (!$imagePath && $uuid) {
    $imagePath = findCredentialPrototypeImage($uuid.'.svg', $imageDirectories);
}

if (!$imagePath) {
    $imagePath = __DIR__.'/images/password.png';
}

header('Content-Type: '.credentialPrototypeImageContentType($imagePath));
readfile($imagePath);

function findCredentialPrototypeImage(string $imageName, array $directories): ?string
{
    $safeName = basename($imageName);

    if ($safeName === '') {
        return null;
    }

    foreach ($directories as $directory) {
        $candidate = $directory.'/'.$safeName;

        if (is_file($candidate)) {
            return $candidate;
        }
    }

    $wantedStem = pathinfo($safeName, \PATHINFO_FILENAME);

    foreach ($directories as $directory) {
        foreach (glob($directory.'/*') ?: [] as $candidate) {
            if (!is_file($candidate)) {
                continue;
            }

            if (strcasecmp(basename($candidate), $safeName) === 0) {
                return $candidate;
            }

            if (strcasecmp(pathinfo($candidate, \PATHINFO_FILENAME), $wantedStem) === 0) {
                return $candidate;
            }
        }
    }

    return null;
}

function credentialPrototypeImageContentType(string $imagePath): string
{
    return match (strtolower(pathinfo($imagePath, \PATHINFO_EXTENSION))) {
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        default => 'application/octet-stream',
    };
}
