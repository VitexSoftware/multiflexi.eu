<?php

/**
 * Multi Flexi - Mian page.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2024 Vitex Software
 */

namespace MultiFlexi\Ui;

use MultiFlexi\Ui\PageBottom;
use MultiFlexi\Ui\PageTop;

require_once './init.php';

$oPage->addItem(new PageTop(_('Multi Flexi')));


$oPage->addItem(new PageBottom());

$oPage->draw();
