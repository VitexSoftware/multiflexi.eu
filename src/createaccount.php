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

$process = false;

// Legal documents paths
$termsPath = 'src/assets/legal/terms-and-conditions.txt';
$gdprPath = 'src/assets/legal/gdpr-consent.txt';

$firstname = $oPage->getRequestValue('firstname');
$lastname = $oPage->getRequestValue('lastname');

if ($oPage->isPosted()) {
    $process = true;

    $emailAddress = addslashes(strtolower($oPage->getRequestValue('email_address')));
    $login = addslashes($oPage->getRequestValue('login'));
    $password = addslashes($oPage->getRequestValue('password'));
    $confirmation = addslashes($oPage->getRequestValue('confirmation'));
    $agreeTerms = $oPage->getRequestValue('agree_terms');
    $agreeGdpr = $oPage->getRequestValue('agree_gdpr');

    $error = false;

    if (!$agreeTerms) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(_('You must agree to the terms and conditions.'), 'warning');
    }

    if (!$agreeGdpr) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(_('You must consent to the processing of personal data according to GDPR.'), 'warning');
    }

    if (!filter_var($emailAddress, \FILTER_VALIDATE_EMAIL)) {
        \Ease\Shared::user()->addStatusMessage(_('invalid mail address'), 'warning');
    } else {
        $testuser = new \MultiFlexi\User();
        $testuser->setkeyColumn('email');
        $testuser->loadFromSQL(addslashes($emailAddress));

        if ($testuser->getUserName()) {
            $error = true;
            \Ease\Shared::user()->addStatusMessage(sprintf(
                _('Mail address %s is already registered'),
                $emailAddress,
            ), 'warning');
        }

        unset($testuser);
    }

    if (\strlen($password) < 5) {
        $error = true;
        \Ease\Shared::user()->addStatusMessage(_('password is too short'), 'warning');
    } elseif ($password !== $confirmation) {
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
            $login,
        ), 'warning');
    }

    if ($error === false) {
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
            if ($newUser->getUserID() === 1) {
                $newUser->setSettingValue('admin', true);
                \Ease\Shared::user()->addStatusMessage(_('Admin account created'), 'success');
                $newUser->setDataValue('enabled', true);
                $newUser->saveToSQL();
            } else {
                \Ease\Shared::user()->addStatusMessage(_('User account created'), 'success');
            }

            $newUser->loginSuccess();

            $userEmail = $newUser->getDataValue('email');
            $emailAddress = \is_string($userEmail) && $userEmail !== '' ? $userEmail : '';
            $email = $oPage->addItem(new \Ease\HtmlMailer(
                $emailAddress,
                _('Sign On info'),
            ));
            $email->setMailHeaders(['From' => \Ease\Shared::cfg('EMAIL_FROM')]);
            $email->addItem(new \Ease\Html\DivTag(sprintf(_('Your new %s account:')."\n", \Ease\Shared::appName())));
            $email->addItem(new \Ease\Html\DivTag(' Login: '.$newUser->getUserLogin()."\n"));
            $email->addItem(new \Ease\Html\DivTag(' Password: '.$_POST['password']."\n"));

            try {
                $email->send();
            } catch (\Ease\Exception $exc) {
            }

            $infoEmail = \Ease\Shared::cfg('SEND_INFO_TO');
            $infoEmailAddress = \is_string($infoEmail) && $infoEmail !== '' ? $infoEmail : '';
            $email = $oPage->addItem(new \Ease\HtmlMailer(
                $infoEmailAddress,
                sprintf(
                    _('New Sign On to %s: %s'),
                    \Ease\Shared::appName(),
                    $newUser->getUserLogin(),
                ),
            ));
            $email->setMailHeaders(['From' => \Ease\Shared::cfg('EMAIL_FROM')]);
            $email->addItem(new \Ease\Html\DivTag(_('New User').":\n"));
            $email->addItem(new \Ease\Html\DivTag(' Login: '.$newUser->getUserLogin()."\n"));

            try {
                $email->send();
            } catch (\Ease\Exception $exc) {
            }

            \Ease\Shared::user($newUser)->loginSuccess();

            $oPage->redirect('main.php');

            exit;
        }

        \Ease\Shared::user()->addStatusMessage(_('Administrator create failed'), 'error');
    }
}

$oPage->addItem(new PageTop(_('New Administrator')));

$regFace = $oPage->container->addItem(new \Ease\TWB5\Panel(_('Singn On')));

$regForm = $regFace->addItem(new ColumnsForm(new \MultiFlexi\User()));

if (\Ease\Shared::user()->getUserID()) {
    $regForm->addItem(new \Ease\Html\InputHiddenTag('parent', \Ease\Shared::user()->GetUserID()));
}

$regForm->addInput(new \Ease\Html\InputTextTag('login'), _('User name').' *');

$regForm->addInput(new \Ease\Html\InputTextTag('firstname', $firstname), _('Firstname'));
$regForm->addInput(new \Ease\Html\InputTextTag('lastname', $lastname), _('Lastname'));

$regForm->addInput(new \Ease\Html\InputPasswordTag('password'), _('Password').' *');
$regForm->addInput(new \Ease\Html\InputPasswordTag('confirmation'), _('Password confirmation').' *');

$regForm->addInput(new \Ease\Html\InputTextTag('email_address'), _('eMail address').' *');

// Terms and GDPR checkboxes
$termsText = file_exists($termsPath) ? nl2br(htmlspecialchars(file_get_contents($termsPath))) : _('Terms and conditions not available.');
$gdprText = file_exists($gdprPath) ? nl2br(htmlspecialchars(file_get_contents($gdprPath))) : _('GDPR consent not available.');

$agreeTermsChecked = isset($_POST['agree_terms']) && $_POST['agree_terms'] === '1';
$agreeGdprChecked = isset($_POST['agree_gdpr']) && $_POST['agree_gdpr'] === '1';

$regForm->addItem(new \Ease\Html\DivTag([
    new \Ease\Html\CheckboxTag('agree_terms', $agreeTermsChecked, '1', ['id' => 'agree_terms']),
    ' <label for="agree_terms"><b>'._('I agree to the terms and conditions').'</b></label>',
    '<br><small>'.$termsText.'</small>',
], ['style' => 'margin-top:15px;']));

$regForm->addItem(new \Ease\Html\DivTag([
    new \Ease\Html\CheckboxTag('agree_gdpr', $agreeGdprChecked, '1', ['id' => 'agree_gdpr']),
    ' <label for="agree_gdpr"><b>'._('I consent to the processing of personal data according to GDPR').'</b></label>',
    '<br><small>'.$gdprText.'</small>',
], ['style' => 'margin-top:15px;']));

$regForm->addItem(new \Ease\Html\DivTag(new \Ease\Html\InputSubmitTag(
    'Register',
    _('Register'),
    ['title' => _('finish registration'), 'class' => 'btn btn-success ', 'style' => 'margin: 20px; padding-left: 50px; padding-right: 50px;'],
)));

if (isset($_POST)) {
    $regForm->fillUp($_POST);
}

$oPage->addItem(new PageBottom());
$oPage->draw();
