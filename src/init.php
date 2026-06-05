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

namespace MultiFlexi;

use Ease\Shared;
use MultiFlexi\Ui\WebPage;

\define('APP_NAME', 'MultiFlexiEU');

// Load composer autoloader
$autoloadPaths = [
    __DIR__.'/../vendor/autoload.php',      // Development
    '/usr/share/php/multiflexi-eu/autoload.php', // Debian deployment
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;

        break;
    }
}

session_start();
\Ease\Shared::init(
    ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'],
    \dirname(__DIR__).'/.env',
);
\Ease\Locale::singleton(null, '../i18n', 'multiflexieu');
// Workaround: Ease\Locale sets LANGUAGUE (typo) instead of LANGUAGE;
// gettext on some systems needs the correct LANGUAGE env var to find .mo files.
\putenv('LANGUAGE='.(\Ease\Locale::$localeUsed ?? 'en_US'));
$loggers = ['syslog', '\MultiFlexi\LogToSQL'];

\define('EASE_LOGGER', implode('|', $loggers));

Shared::user(null, '\MultiFlexi\User');

/**
 * @global WebPage $oPage
 */
$oPage = new WebPage();
