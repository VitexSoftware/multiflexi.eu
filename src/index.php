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

$oPage->addItem(new PageTop(_('MultiFlexi Hub')));

$oPage->body->addTagClass('home');
$oPage->head->addItem('<meta name="description" content="'._('MultiFlexi runs, schedules and watches your integrations on top of AbraFlexi and Pohoda – for dozens of companies, with isolated credentials and a full history.').'">');

$home = $oPage->container->addItem(new \Ease\Html\DivTag(null, ['class' => 'vs-home']));
$home->addItem(Landing::hero());
$home->addItem('<div class="spectrum"></div>');
$home->addItem(Landing::paths());
$home->addItem(Landing::audience());
$home->addItem(Landing::catalog());
$home->addItem(Landing::interfaces());
$home->addItem('<div class="spectrum"></div>');
$home->addItem(Landing::pricing());
$home->addItem(Landing::about());
$home->addItem(Landing::cta());

$oPage->addItem(new PageBottom());

$oPage->draw();
