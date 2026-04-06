<?php

/**
 * MultiFlexi.eu Debian autoloader.
 */

require_once '/usr/share/php/EaseCore/autoload.php';
require_once '/usr/share/php/EaseFluentPDO/autoload.php';
require_once '/usr/share/php/EaseHtml/autoload.php';
require_once '/usr/share/php/EaseTWB5/autoload.php';
require_once '/usr/share/php/EaseHtmlWidgets/autoload.php';
require_once '/usr/share/php/EaseBootstrap5Widgets/autoload.php';
require_once '/usr/share/php/MultiFlexi/autoload.php';
require_once '/usr/share/php/Rcubitto/JsonPretty/JsonPretty.php';

// Local classes
spl_autoload_register(function ($class) {
    $prefix = 'MultiFlexi\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = '/usr/lib/multiflexi-eu/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
