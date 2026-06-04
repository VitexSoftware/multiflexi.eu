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

require_once __DIR__.'/init.php';

$action = \Ease\WebPage::getRequestValue('action');
$prototype = new CredentialProtoType(WebPage::getRequestValue('id', 'int'));
$instanceName = $prototype->getDataValue('name') ?: _('n/a');

$loggedUser = \Ease\Shared::user();
$isOwner = $loggedUser->isLogged() && $prototype->getMyKey() && (int) $prototype->getDataValue('user') === (int) $loggedUser->getMyKey();
$isNew = !$prototype->getMyKey() && $loggedUser->isLogged();

// Check coworker status
$isCoworker = false;

if ($loggedUser->isLogged() && $prototype->getMyKey() && !$isOwner) {
    $pdo = $prototype->getPdo();
    $stmt = $pdo->prepare('SELECT 1 FROM item_coworker WHERE item_type = ? AND item_id = ? AND user_id = ?');
    $stmt->execute(['credential_prototype', $prototype->getMyKey(), $loggedUser->getMyKey()]);
    $isCoworker = (bool) $stmt->fetch();
}

$canEdit = $isOwner || $isNew || $isCoworker;

switch ($action) {
    case 'delete':
        if (!$isOwner) {
            $prototype->addStatusMessage(_('Only the owner can delete this credential type'), 'warning');

            break;
        }

        $prototype->deleteFromSQL();
        $prototype->addStatusMessage(sprintf(_('Credential Type %s removed'), $prototype->getRecordName()), 'success');
        $oPage->redirect('credentialtypes.php');

        break;

    default:
        if ($oPage->isPosted()) {
            if (!$canEdit) {
                $prototype->addStatusMessage(_('You do not have permission to edit this credential type'), 'warning');

                break;
            }

            if ($prototype->takeData($_POST) && null !== $prototype->saveToSQL()) {
                $prototype->addStatusMessage(_('Credential Type Saved'), 'success');
                $oPage->redirect('?id='.$prototype->getMyKey());
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

$oPage->addItem(new PageTop($prototype->getRecordName() ? trim(_('Credential Type').' '.$prototype->getRecordName()) : $instanceName));

$credTabs = new Tabs();

if ($canEdit) {
    $credTabs->addTab(_('Import'), new CredentialProtoTypeImportForm());

    $instanceRow = new Row();
    $instanceRow->addColumn(4, new CredentialProtoTypeEditorForm($prototype));

    $instanceRow->addColumn(4, null === $prototype->getMyKey() ?
        new LinkButton('', _('Fields'), 'inverse disabled w-100') :
        new CredentialProtoTypeFieldsView($prototype));

    $instanceRow->addColumn(4, [
        new \Ease\Html\DivTag($prototype->getDataValue('url') ? new \Ease\Html\ATag($prototype->getDataValue('url'), $prototype->getDataValue('url'), ['target' => '_blank']) : ''),
    ]);

    $credTabs->addTab(_('Configuration'), $instanceRow);
} else {
    $instanceRow = new Row();
    $instanceRow->addColumn(8, null === $prototype->getMyKey() ?
        new \Ease\Html\PTag(_('No configuration available.')) :
        new CredentialProtoTypeFieldsView($prototype));

    $instanceRow->addColumn(4, [
        new \Ease\Html\DivTag($prototype->getDataValue('url') ? new \Ease\Html\ATag($prototype->getDataValue('url'), $prototype->getDataValue('url'), ['target' => '_blank']) : ''),
    ]);

    $credTabs->addTab(_('Configuration'), $instanceRow);
}

if ($prototype->getMyKey()) {
    $credTabs->addTab(_('Export'), new CredentialProtoTypeJson($prototype));
}

if ($isOwner && $prototype->getMyKey()) {
    $credTabs->addTab(_('Coworkers'), new CoworkersManager('credential_prototype', (int) $prototype->getMyKey()));
}

$oPage->container->addItem(new CredentialProtoTypePanel(
    $prototype,
    $credTabs,
    '',
));

$oPage->addItem(new PageBottom());
$oPage->draw();
