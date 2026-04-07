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
use MultiFlexi\Conffield;
use MultiFlexi\Hub\Application;

require_once __DIR__.'/init.php';

// JSON export mode — public API, no auth required
$exportUuid = \Ease\WebPage::getRequestValue('export');

if ($exportUuid) {
    $exportApp = new Application();
    $exportApp->setKeyColumn('uuid');
    $exportApp->loadFromSQL($exportUuid);

    if ($exportApp->getMyKey()) {
        $appJson = $exportApp->getAppJson();
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        echo $appJson;
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Application not found']);
    }

    exit;
}

$action = \Ease\WebPage::getRequestValue('action');
$apps = new Application(WebPage::getRequestValue('id', 'int') + WebPage::getRequestValue('app', 'int'));
$instanceName = _($apps->getDataValue('name') ?: _('n/a'));

$loggedUser = \Ease\Shared::user();
$isOwner = $loggedUser->isLogged() && $apps->getMyKey() && (int) $apps->getDataValue('user') === (int) $loggedUser->getMyKey();
$isNewApp = !$apps->getMyKey() && $loggedUser->isLogged();

// Check coworker status
$isCoworker = false;

if ($loggedUser->isLogged() && $apps->getMyKey() && !$isOwner) {
    $pdo = $apps->getPdo();
    $stmt = $pdo->prepare('SELECT 1 FROM item_coworker WHERE item_type = ? AND item_id = ? AND user_id = ?');
    $stmt->execute(['app', $apps->getMyKey(), $loggedUser->getMyKey()]);
    $isCoworker = (bool) $stmt->fetch();
}

$canEdit = $isOwner || $isNewApp || $isCoworker;

switch ($action) {
    case 'delete':
        if (!$canEdit) {
            $apps->addStatusMessage(_('Only the application author can delete it'), 'warning');

            break;
        }

        $configurator = new \MultiFlexi\Configuration();
        $configurator->deleteFromSQL(['app_id' => $apps->getMyKey()]);

        $apps->deleteFromSQL();
        $apps->addStatusMessage(sprintf(_('Application %s removal'), $apps->getRecordName()), 'success');
        $oPage->redirect('apps.php');

        break;

    default:
        if ($oPage->isPosted()) {
            if (!$canEdit) {
                $apps->addStatusMessage(_('Only the application author can edit it'), 'warning');

                break;
            }

            if ($apps->takeData($_POST) && null !== $apps->saveToSQL()) {
                $apps->addStatusMessage(_('Application Saved'), 'success');
                $oPage->redirect('?id='.$apps->getMyKey());
            } else {
                $apps->addStatusMessage(_('Error saving Application'), 'error');
            }
        }

        break;
}

if (empty($instanceName) === false) {
    $instanceLink = '';
} else {
    $instanceName = _('New Application');
    $instanceLink = null;
}

$_SESSION['application'] = $apps->getMyKey();
$oPage->addItem(new PageTop($apps->getRecordName() ? trim(_('Application').' '.$apps->getRecordName()) : $instanceName));

$appTabs = new Tabs();

if ($canEdit) {
    $appTabs->addTab(_('Import'), new AppImportForm());

    $instanceRow = new Row();
    $instanceRow->addColumn(4, new AppEditorForm($apps));
    $instanceRow->addColumn(4, null === $apps->getMyKey() ?
                    new LinkButton('', _('Config fields'), 'inverse disabled  btn-block') :
                    new ConfigFieldsView(Conffield::getAppConfigs($apps)));
    $instanceRow->addColumn(4, new AppLogo($apps));

    $appTabs->addTab(_('Configuration'), $instanceRow);
} else {
    $instanceRow = new Row();
    $instanceRow->addColumn(8, null === $apps->getMyKey() ?
                    new \Ease\Html\PTag(_('No configuration available.')) :
                    new ConfigFieldsView(Conffield::getAppConfigs($apps)));
    $instanceRow->addColumn(4, new AppLogo($apps));

    $appTabs->addTab(_('Configuration'), $instanceRow);
}

$appTabs->addTab(_('Export'), new AppJson($apps));

if ($isOwner && $apps->getMyKey()) {
    $appTabs->addTab(_('Coworkers'), new CoworkersManager('app', (int) $apps->getMyKey()));
}

$oPage->container->addItem(new ApplicationPanel(
    $apps,
    $appTabs,
    '',
));

$oPage->addItem(new PageBottom());
$oPage->draw();
