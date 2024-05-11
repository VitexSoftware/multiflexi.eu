<?php

/**
 * Multi Flexi - Create new account.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2024 Vitex Software
 */

namespace MultiFlexi\Ui;

require_once './init.php';

$process = false;

$firstname = $oPage->getRequestValue('firstname');
$lastname = $oPage->getRequestValue('lastname');

if ($oPage->isPosted()) {
    $process = true;

    $emailAddress = addslashes(strtolower($oPage->getRequestValue('email_address')));

    $login = addslashes($oPage->getRequestValue('login'));
    $password = addslashes($oPage->getRequestValue('password'));

    $confirmation = addslashes($oPage->getRequestValue('confirmation'));

    $error = false;

    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        \Ease\Shared::user()->addStatusMessage(_('invalid mail address'), 'warning');
    } else {
        $testuser = new \MultiFlexi\User();
        $testuser->setkeyColumn('email');
        $testuser->loadFromSQL(addSlashes($emailAddress));
        if ($testuser->getUserName()) {
            $error = true;
            \Ease\Shared::user()->addStatusMessage(sprintf(
                            _('Mail address %s is already registered'),
                            $emailAddress
                    ), 'warning');
        }
        unset($testuser);
    }

    if (strlen($password) < 5) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(_('password is too short'), 'warning');
    } elseif ($password != $confirmation) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(_('Password control does not match'), 'warning');
    }

    $testuser = new \MultiFlexi\User();
    $testuser->setkeyColumn('login');
    $testuser->loadFromSQL(addslashes($login));

    if ($testuser->getMyKey()) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(sprintf(
                        _('Username %s is used. Please choose another one'),
                        $login
                ), 'warning');
    }

    if ($error == false) {
        $newUser = new \MultiFlexi\User();

        if (
                $newUser->dbsync([
                    'email' => $emailAddress,
                    'login' => $login,
                    $newUser->passwordColumn => $newUser->encryptPassword($password),
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                ])
        ) {
            if ($newUser->getUserID() == 1) {
                $newUser->setSettingValue('admin', true);
                \Ease\Shared::user()->addStatusMessage(_('Admin account created'), 'success');
                $newUser->setDataValue('enabled', true);
                $newUser->saveToSQL();
            } else {
                \Ease\Shared::user()->addStatusMessage(_('User account created'), 'success');
            }

            $newUser->loginSuccess();

            $email = $oPage->addItem(new \Ease\HtmlMailer(
                            $newUser->getDataValue('email'),
                            _('Sign On info')
            ));
            $email->setMailHeaders(['From' => \Ease\Shared::cfg('EMAIL_FROM')]);
            $email->addItem(new \Ease\Html\DivTag(sprintf(_("Your new %s account:") . "\n", \Ease\Shared::appName())));
            $email->addItem(new \Ease\Html\DivTag(' Login: ' . $newUser->getUserLogin() . "\n"));
            $email->addItem(new \Ease\Html\DivTag(' Password: ' . $_POST['password'] . "\n"));
            try {
                $email->send();
            } catch (\Ease\Exception $exc) {
                
            }

            $email = $oPage->addItem(new \Ease\HtmlMailer(
                            \Ease\Shared::cfg('SEND_INFO_TO'),
                            sprintf(
                                    _('New Sign On to %s: %s'),
                                    \Ease\Shared::appName(),
                                    $newUser->getUserLogin()
                            )
            ));
            $email->setMailHeaders(['From' => \Ease\Shared::cfg('EMAIL_FROM')]);
            $email->addItem(new \Ease\Html\DivTag(_("New User") . ":\n"));
            $email->addItem(new \Ease\Html\DivTag(' Login: ' . $newUser->getUserLogin() . "\n"));
            try {
                $email->send();
            } catch (\Ease\Exception $exc) {
                
            }

            \Ease\Shared::user($newUser)->loginSuccess();

            $oPage->redirect('main.php');
            exit;
        } else {
            \Ease\Shared::user()->addStatusMessage(_('Administrator create failed'), 'error');
        }
    }
}

$oPage->addItem(new PageTop(_('New Administrator')));

$regFace = $oPage->container->addItem(new \Ease\TWB5\Panel(_('Singn On')));

$regForm = $regFace->addItem(new ColumnsForm(new \MultiFlexi\User()));
if (\Ease\Shared::user()->getUserID()) {
    $regForm->addItem(new \Ease\Html\InputHiddenTag('parent', \Ease\Shared::user()->GetUserID()));
}

$regForm->addInput(new \Ease\Html\InputTextTag('login'), _('User name') . ' *');

$regForm->addInput(new \Ease\Html\InputTextTag('firstname', $firstname), _('Firstname'));
$regForm->addInput(new \Ease\Html\InputTextTag('lastname', $lastname), _('Lastname'));

$regForm->addInput(new \Ease\Html\InputPasswordTag('password'), _('Password') . ' *');
$regForm->addInput(new \Ease\Html\InputPasswordTag('confirmation'), _('Password confirmation') . ' *');
$regForm->addInput(new \Ease\Html\InputTextTag('email_address'), _('eMail address') . ' *');

$regForm->addItem(new \Ease\Html\DivTag(new \Ease\Html\InputSubmitTag(
                        'Register',
                        _('Register'),
                        ['title' => _('finish registration'), 'class' => 'btn btn-success ', 'style' => 'margin: 20px; padding-left: 50px; padding-right: 50px;']
        )));

if (isset($_POST)) {
    $regForm->fillUp($_POST);
}

$oPage->addItem(new PageBottom());
$oPage->draw();
