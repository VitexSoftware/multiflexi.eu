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

require_once __DIR__ . '/init.php';
$oPage->onlyForLogged();

$oPage->addItem(new PageTop(_('My Applications')));

$apps = new \MultiFlexi\Application();
$apps->filter = ['user' => \Ease\Shared::user()->getUserID()];

$oPage->container->addItem(new DBDataTable($apps));
$oPage->container->addItem(new \Ease\TWB5\LinkButton('app.php', '➕ ' . _('Submit new application'), 'success'));

$oPage->addItem(new PageBottom());

$oPage->draw();
