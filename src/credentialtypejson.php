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
$prototype = new \MultiFlexi\CredentialProtoType($oPage->getRequestValue('id', 'int'));

$jsonData = $prototype->exportJson();
$jsonString = json_encode($jsonData, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);
$fileName = strtolower(trim(preg_replace('#\W+#', '_', (string) $prototype->getRecordName()), '_')) . '.credentialtype.json';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . \strlen($jsonString));

echo $jsonString;
