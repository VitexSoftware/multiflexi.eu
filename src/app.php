<?php

/**
 * Multi Flexi - Customer instance editor.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017-2024 Vitex Software
 */

namespace MultiFlexi\Ui;

use DateTime;
use Ease\Html\ATag;
use Ease\Html\SmallTag;
use Ease\TWB5\LinkButton;
use Ease\TWB5\Row;
use Ease\TWB5\Table;
use Ease\TWB5\Tabs;
use Ease\ui\LiveAge;
use MultiFlexi\Application;
use MultiFlexi\Conffield;
use MultiFlexi\Job;
use MultiFlexi\Ui\AppEditorForm;
use MultiFlexi\Ui\PageBottom;
use MultiFlexi\Ui\PageTop;

require_once './init.php';
$oPage->onlyForLogged();
$action = \Ease\WebPage::getRequestValue('action');
$apps = new Application(WebPage::getRequestValue('id', 'int') + WebPage::getRequestValue('app', 'int'));
$instanceName = _($apps->getDataValue('name') ? $apps->getDataValue('name') : _('n/a'));

switch ($action) {
    case 'delete':
        $configurator = new \MultiFlexi\Configuration();
        $configurator->deleteFromSQL(['app_id' => $apps->getMyKey()]);

        $apps->deleteFromSQL();
        $apps->addStatusMessage(sprintf(_('Application %s removal'), $apps->getRecordName()), 'success');
        $oPage->redirect('apps.php');
        break;
    default:
        if ($oPage->isPosted()) {
            if ($apps->takeData($_POST) && !is_null($apps->saveToSQL())) {
                $apps->addStatusMessage(_('Application Saved'), 'success');
                //        $apps->prepareRemoteAbraFlexi();
                $oPage->redirect('?id=' . $apps->getMyKey());
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
$oPage->addItem(new PageTop($apps->getRecordName() ? trim(_('Application') . ' ' . $apps->getRecordName()) : $instanceName));
$instanceRow = new Row();
$instanceRow->addColumn(4, new AppEditorForm($apps));
//if (array_key_exists('company', $_SESSION) && is_null($_SESSION['company']) === false) {
//    $company = new Company($_SESSION['company']);
//    $panel[] = new LinkButton('id=' . $apps->getMyKey() . '&company=' . $_SESSION['company'], sprintf(_('Assign to %s'), $company->getRecordName()), 'success');
//}

$instanceRow->addColumn(4, is_null($apps->getMyKey()) ?
                new LinkButton('', _('Config fields'), 'inverse disabled  btn-block') :
                [
            new ConfigFieldsView(Conffield::getAppConfigs($apps->getMyKey())),
            new LinkButton('conffield.php?app_id=' . $apps->getMyKey(), _('Config fields editor'), 'secondary  btn-block')
        ]);

$instanceRow->addColumn(4, new AppLogo($apps));

$appTabs = new Tabs();
$appTabs->addTab(_('Configuration'), $instanceRow);
$appTabs->addTab(_('Export'), new AppJson($apps));

$oPage->container->addItem(new ApplicationPanel(
                $apps,
                $appTabs,
                ''
));

$oPage->addItem(new PageBottom());
$oPage->draw();
