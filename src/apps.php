<?php

/**
 * MultiFlexi.eu - Index of Applications.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2024 Vitex Software
 */

namespace MultiFlexi\Ui;

use Ease\TWB5\LinkButton;
use Ease\TWB5\Panel;
use Ease\TWB5\Table;
use MultiFlexi\Application;

require_once './init.php';
#$oPage->onlyForLogged();
$oPage->addItem(new PageTop(_('Applications')));

$servers = new Application();

$allAppData = $servers->getAll();

$fbtable = new Table();
$fbtable->addRowHeaderColumns(['', _('Name'), _('Description'), _('Executable'), _('Created'), _('Modified'), _('HomePage'), _('Requirements'), _('Container Image'), _('Version'), _('uuid')]);

foreach ($allAppData as $appData) {
    unset($appData['id']);
    unset($appData['enabled']);
    unset($appData['code']);
    $appData['image'] = new \Ease\Html\ImgTag($appData['image'], _('Icon'), ['height' => 40]);
    $executablePath = Application::findBinaryInPath($appData['executable']);
    $appData['executable'] = empty($executablePath) ? '<span title="' . _('Command not found') . '">⁉</span> ' . $appData['executable'] : $executablePath;

    if (empty($appData['setup']) === false) {
        $initPath = Application::findBinaryInPath($appData['setup']);
        $appData['setup'] = (empty($initPath) ? '<span title="' . _('Command not found') . '">⁉</span> ' . $appData['setup'] : $initPath);
    }

    $appData['homepage'] = new \Ease\Html\ATag($appData['homepage'], $appData['homepage']);
    $appData['name'] = _($appData['name']);
    $appData['description'] = _($appData['description']);
    unset($appData['setup']);
    unset($appData['cmdparams']);
    unset($appData['deploy']);
    $fbtable->addRowColumns($appData);
}

$oPage->container->addItem(new Panel(_('Availble Applications'), 'default', $fbtable, new LinkButton('app.php', _('Register new'))));

$oPage->addItem(new PageBottom());

$oPage->draw();
