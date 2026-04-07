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

require_once '../vendor/autoload.php';

\Ease\Shared::init(['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'], '../.env');
$loggers = ['console', 'syslog', '\MultiFlexi\LogToSQL'];

if (\Ease\Shared::cfg('ZABBIX_SERVER') && \Ease\Shared::cfg('ZABBIX_HOST') && class_exists('\MultiFlexi\LogToZabbix')) {
    $loggers[] = '\MultiFlexi\LogToZabbix';
}

\define('EASE_LOGGER', implode('|', $loggers));
\define('APP_NAME', 'MultiFlexiEU json2credentialtype');

\Ease\Shared::user(new \Ease\Anonym());

if (\array_key_exists(1, $argv) && file_exists($argv[1])) {
    $prototype = new Hub\CredentialProtoType();

    if (\Ease\Shared::cfg('APP_DEBUG')) {
        $prototype->logBanner($argv[1]);
    }

    $jsonContent = file_get_contents($argv[1]);

    if (empty($jsonContent)) {
        $prototype->addStatusMessage(_('Credential prototype definition file is empty: ').$argv[1], 'error');

        exit(1);
    }

    $jsonData = json_decode($jsonContent, true);

    if (json_last_error() !== \JSON_ERROR_NONE) {
        $prototype->addStatusMessage(_('Invalid JSON: ').json_last_error_msg(), 'error');

        exit(1);
    }

    // Set user for CLI import (use user ID 1 or from argument)
    $userId = $argc >= 3 ? (int) $argv[2] : 1;
    $jsonData['user'] = $userId;

    if ($prototype->importJson($jsonData)) {
        $prototype->addStatusMessage(sprintf(_('Credential prototype %s imported successfully'), $prototype->getRecordName()), 'success');
    } else {
        $prototype->addStatusMessage(_('Error importing credential prototype'), 'error');

        exit(1);
    }
} else {
    echo 'usage: json2credentialtype.php credential-prototype.json'."\n";
}
