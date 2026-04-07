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
$oPage->onlyForLogged();

$apps = new \MultiFlexi\Hub\Application();

if ($oPage->isPosted() && isset($_FILES['jsonfile']) && $_FILES['jsonfile']['error'] === \UPLOAD_ERR_OK) {
    $tmpFile = $_FILES['jsonfile']['tmp_name'];

    $result = $apps->importAppJson($tmpFile);

    if ($apps->getMyKey()) {
        $apps->setDataValue('enabled', 1);
        $apps->updateToSQL();
        $apps->addStatusMessage(_('Application imported successfully'), 'success');
        $oPage->redirect('app.php?id='.$apps->getMyKey());
    } else {
        $apps->addStatusMessage(_('Error importing application'), 'error');
        $oPage->redirect('app.php');
    }
} else {
    $apps->addStatusMessage(_('No file uploaded or upload error'), 'error');
    $oPage->redirect('app.php');
}
