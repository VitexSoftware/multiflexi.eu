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

$oPage->addItem(new PageTop(_('Multi Flexi')));

$imageRow = new \Ease\TWB5\Row();
$imageRow->addTagClass('justify-content-md-center');
$imageRow->addColumn('4');
// $imageRow->addColumn('4', new \Ease\Html\DivTag(new \Ease\Html\ImgTag('images/openclipart/345630.svg', _('AI and Human Relationship'), ['class' => 'mx-auto d-block img-fluid'])), 'sm');
$imageRow->addColumn('4', new \Ease\Html\DivTag(_('MultiFlexi is a runtime environment for tasks not only on top of the economic systems AbraFlexi and Stormware Pohoda'), ['class' => 'text-center']));

$imageRow->addColumn('4');

$oPage->container->addItem($imageRow);

$oPage->container->addItem(new \Ease\Html\ImgTag('images/company-screenshot.png', _('Screenshot'), ['data-lightbox' => 'image-1', 'data-title' => _('Screenshot'), 'style' => 'width: 33%', 'class' => 'rounded mx-auto d-block', 'title' => _('Click to enlarge')]));

$oPage->addItem(new PageBottom());

$oPage->draw();
