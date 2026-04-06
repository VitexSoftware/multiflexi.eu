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
use Ease\TWB5\Row;
use Ease\TWB5\Tabs;
use MultiFlexi\Hub\CredentialProtoType;

require_once __DIR__ . '/init.php';
$oPage->onlyForLogged();
$action = \Ease\WebPage::getRequestValue('action');
$prototype = new CredentialProtoType(WebPage::getRequestValue('id', 'int'));
$instanceName = $prototype->getDataValue('name') ?: _('n/a');

switch ($action) {
    case 'delete':
        $prototype->deleteFromSQL();
        $prototype->addStatusMessage(sprintf(_('Credential Type %s removed'), $prototype->getRecordName()), 'success');
        $oPage->redirect('credentialtypes.php');

        break;

    default:
        if ($oPage->isPosted()) {
            if ($prototype->takeData($_POST) && null !== $prototype->saveToSQL()) {
                $prototype->addStatusMessage(_('Credential Type Saved'), 'success');
                $oPage->redirect('?id=' . $prototype->getMyKey());
            } else {
                $prototype->addStatusMessage(_('Error saving Credential Type'), 'error');
            }
        }

        break;
}

if (empty($instanceName) === false) {
    $instanceLink = '';
} else {
    $instanceName = _('New Credential Type');
    $instanceLink = null;
}

$oPage->addItem(new PageTop($prototype->getRecordName() ? trim(_('Credential Type') . ' ' . $prototype->getRecordName()) : $instanceName));
$instanceRow = new Row();
$instanceRow->addColumn(4, new CredentialProtoTypeEditorForm($prototype));

$instanceRow->addColumn(4, null === $prototype->getMyKey() ?
    new LinkButton('', _('Fields'), 'inverse disabled btn-block') :
    new CredentialProtoTypeFieldsView($prototype));

$instanceRow->addColumn(4, [
    new \Ease\Html\DivTag(
        $prototype->getDataValue('url')
            ? new \Ease\Html\ATag($prototype->getDataValue('url'), $prototype->getDataValue('url'), ['target' => '_blank'])
            : '',
    ),
]);

$credTabs = new Tabs();
$credTabs->addTab(_('Configuration'), $instanceRow);

if ($prototype->getMyKey()) {
    $credTabs->addTab(_('Export'), new CredentialProtoTypeJson($prototype));
}

$oPage->container->addItem(new CredentialProtoTypePanel(
    $prototype,
    $credTabs,
    '',
));

$oPage->addItem(new PageBottom());
$oPage->draw();
