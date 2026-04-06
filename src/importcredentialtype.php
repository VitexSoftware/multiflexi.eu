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

$prototype = new \MultiFlexi\Hub\CredentialProtoType();

if ($oPage->isPosted() && isset($_FILES['jsonfile']) && $_FILES['jsonfile']['error'] === \UPLOAD_ERR_OK) {
    $tmpFile = $_FILES['jsonfile']['tmp_name'];
    $jsonContent = file_get_contents($tmpFile);
    $jsonData = json_decode($jsonContent, true);

    if (json_last_error() !== \JSON_ERROR_NONE) {
        $prototype->addStatusMessage(_('Invalid JSON file').': '.json_last_error_msg(), 'error');
        $oPage->redirect('credentialtype.php');
    } elseif ($prototype->importJson($jsonData)) {
        $prototype->addStatusMessage(_('Credential Type imported successfully'), 'success');
        $oPage->redirect('credentialtype.php?id='.$prototype->getMyKey());
    } else {
        $prototype->addStatusMessage(_('Error importing Credential Type'), 'error');
        $oPage->redirect('credentialtype.php');
    }
} else {
    $prototype->addStatusMessage(_('No file uploaded or upload error'), 'error');
    $oPage->redirect('credentialtype.php');
}
