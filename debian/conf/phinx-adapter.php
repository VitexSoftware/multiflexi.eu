<?php

/**
 * MultiFlexi.eu - Phinx database adapter for Debian packaging.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2024-2026 Vitex Software
 */

include_once '/usr/share/php/multiflexi-eu/autoload.php';

$shared = \Ease\Shared::instanced();
if (file_exists('/etc/multiflexi-eu/.env')) {
    $shared->loadConfig('/etc/multiflexi-eu/.env', true);
}

$prefix = '/usr/lib/multiflexi-eu/';

$sqlOptions = [];

if (strstr(\Ease\Shared::cfg('DB_CONNECTION'), 'sqlite')) {
    $sqlOptions['database'] = \Ease\Shared::cfg('DB_DATABASE');
}

$engine = new \Ease\SQL\Engine(null, $sqlOptions);
$cfg = [
    'paths' => [
        'migrations' => [$prefix . 'migrations'],
        'seeds' => [$prefix . 'seeds']
    ],
    'environments' =>
    [
        'default_environment' => 'production',
        'production' => [
            'adapter' => \Ease\Shared::cfg('DB_CONNECTION'),
            'name' => $engine->database,
            'connection' => $engine->getPdo($sqlOptions)
        ],
    ]
];

return $cfg;
