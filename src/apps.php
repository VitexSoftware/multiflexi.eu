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

use Ease\TWB5\LinkButton;
use Ease\TWB5\Panel;
use Ease\TWB5\Table;
use MultiFlexi\Application;

require_once './init.php';
// $oPage->onlyForLogged();
$oPage->addItem(new PageTop(_('Applications')));

$apps = new Application();

$allAppData = $apps->listingQuery()->select(['uuid', 'image', 'name', 'description', 'homepage', 'version' /* ,'labels' */], true);

$fbtable = new Table();
$fbtable->addRowHeaderColumns(['', _('Name'), _('Description'), _('HomePage'), _('Version')]);

foreach ($allAppData as $appData) {
    $uuid = $appData['uuid'];
    unset($appData['uuid']);
    $appData['image'] = new \Ease\Html\ImgTag($appData['image'], _('Icon'), ['height' => 40]);
    $appData['name'] = _($appData['name']);
    $appData['description'] = _($appData['description']);
    $appData['homepage'] = new \Ease\Html\ATag($appData['homepage'], $appData['homepage']);
    $fbtable->addRowColumns($appData);
}

$oPage->container->addItem(new Panel(_('Availble Applications'), 'default', $fbtable, new LinkButton('app.php', _('Register new'))));

$oPage->addItem(new PageBottom());

$oPage->draw();
