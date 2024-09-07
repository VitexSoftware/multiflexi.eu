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

require_once './init.php';

$oPage->addItem(new PageTop(_('Multi Flexi')));
$oPage->addCss('pre: {margin: 10px, padding: 10px, color: green; background-color: black;}');

$oPage->container->addItem(new \Ease\Html\H4Tag('1.) '._('Prepare your system')));
$oPage->container->addItem(new \Ease\Html\PreTag('sudo apt update'));
$oPage->container->addItem(new \Ease\Html\PreTag('sudo apt install lsb-release apt-transport-https bzip2 ca-certificates curl'));

$prepareRow = new \Ease\TWB5\Tabs();
$prepareRow->addTab(
    _('Stable'),
    [
        new \Ease\Html\PreTag('curl -sSLo /tmp/multiflexi-archive-keyring.deb https://repo.multiflexi.eu/multiflexi-archive-keyring.deb'),
        new \Ease\Html\PreTag('dpkg -i /tmp/multiflexi-archive-keyring.deb'),
        new \Ease\Html\PreTag('sh -c \'echo "deb [signed-by=/usr/share/keyrings/repo.multiflexi.eu.gpg] https://repo.multiflexi.eu/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/multiflexi.list\''),
    ],
);
$prepareRow->addTab(
    _('Development'),
    [
        new \Ease\Html\PreTag('wget -qO- https://repo.vitexsoftware.com/keyring.gpg | sudo tee /etc/apt/trusted.gpg.d/vitexsoftware.gpg'),
        new \Ease\Html\PreTag('echo "deb [signed-by=/etc/apt/trusted.gpg.d/vitexsoftware.gpg]  https://repo.vitexsoftware.com  $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list'),
    ],
);

$oPage->container->addItem(new \Ease\Html\H4Tag('2.) '._('Choose stability')));
$oPage->container->addItem($prepareRow);

$oPage->container->addItem(new \Ease\Html\H4Tag('3.) '._('Update Sources')));
$oPage->container->addItem(new \Ease\Html\PreTag('sudo apt update'));

$oPage->container->addItem(new \Ease\Html\H3Tag('4.) '._('Install for chosen database')));

$installRow = new \Ease\TWB5\Tabs();
$installRow->addTab(_('MySQL'), new \Ease\Html\PreTag('sudo apt install multiflexi-mysql'));
$installRow->addTab(_('SQLite'), new \Ease\Html\PreTag('sudo apt install multiflexi-sqlite'));

$oPage->container->addItem($installRow);

$oPage->container->addItem(new \Ease\Html\H3Tag('5.) '._('Check for applications Availble')));
$oPage->container->addItem(new \Ease\Html\PreTag('apt search multiflexi'));

$oPage->addItem(new PageBottom());

$oPage->draw();
