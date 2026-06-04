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

$success = false;

$emailTo = $oPage->getPostValue('Email');

if (empty($emailTo)) {
    \Ease\Shared::user()->addStatusMessage(_('Please enter your email.'));
} else {
    $userEmail = addslashes($emailTo);

    $controlUser = new \MultiFlexi\User();
    $controlData = $controlUser->getColumnsFromSql(
        [$controlUser->getkeyColumn()],
        ['email' => $userEmail],
    );

    if (empty($controlData)) {
        \Ease\Shared::user()->addStatusMessage(sprintf(
            _('unknow email address %s'),
            '<strong>'.$_REQUEST['Email'].'</strong>',
        ), 'warning');
    } else {
        $controlUser->loadFromSQL((int) $controlData[0][$controlUser->getkeyColumn()]);
        $userLogin = $controlUser->getUserLogin();
        $newPassword = \Ease\Functions::randomString(8);

        $controlUser->passwordChange($newPassword);

        $email = $oPage->addItem(new \Ease\HtmlMailer(
            $userEmail,
            \Ease\Shared::appName().' -'.sprintf(
                _('New password for %s'),
                $_SERVER['SERVER_NAME'],
            ),
        ));

        $email->setMailHeaders(['From' => \Ease\Shared::cfg('EMAIL_FROM')]);
        $email->addItem(_('Sign On informations was changed').":\n");

        $email->addItem(_('Username').': '.$userLogin."\n");
        $email->addItem(_('Password').': '.$newPassword."\n");

        $email->send();

        \Ease\Shared::user()->addStatusMessage(sprintf(
            _('Your new password was sent to %s'),
            '<strong>'.$emailTo.'</strong>',
        ));
        $success = true;
    }
}

$oPage->addItem(new PageTop(_('Password Recovery')));

$face = new \Ease\Html\DivTag(null, ['id' => 'LoginFace']);
$card = new \Ease\Html\DivTag(null, ['class' => 'mf-login-card']);

$card->addItem(new \Ease\Html\DivTag(
    new \Ease\Html\ImgTag('images/multiflexi-logo.svg', 'MultiFlexi', ['style' => 'height: 80px']),
    ['class' => 'mf-login-logo'],
));
$card->addItem(new \Ease\Html\H2Tag('🔑 '._('Password Recovery')));

if (!$success) {
    $card->addItem(new \Ease\Html\DivTag(
        _('Forgot your password? Enter the e-mail address you used during registration and we will send you a new one.'),
        ['class' => 'alert alert-info mb-3', 'role' => 'alert'],
    ));
    $card->addItem(new \Ease\TWB5\FormGroup(
        _('Email address'),
        new \Ease\Html\InputTextTag('Email', $emailTo, ['type' => 'email', 'class' => 'form-control form-control-lg', 'placeholder' => _('your@email.com'), 'autofocus' => 'autofocus']),
    ));
    $card->addItem(new \Ease\Html\DivTag(
        new \Ease\TWB5\SubmitButton(_('Send New Password'), 'success btn-lg w-100'),
        ['class' => 'd-grid mt-3'],
    ));

    $mailForm = new \Ease\TWB5\Form(['name' => 'PasswordRecovery', 'method' => 'POST'], [], $card);

    if ($oPage->isPosted()) {
        $mailForm->fillUp($_POST);
    }

    $face->addItem($mailForm);
} else {
    $card->addItem(new \Ease\Html\DivTag(
        '✅ '._('Check your inbox — your new password has been sent.'),
        ['class' => 'alert alert-success mb-3'],
    ));
    $card->addItem(new \Ease\Html\DivTag(
        new \Ease\TWB5\LinkButton('login.php', '🚪 '._('Back to Sign In'), 'primary btn-lg w-100'),
        ['class' => 'd-grid'],
    ));
    $face->addItem($card);
}

$oPage->container->addItem($face);

$oPage->addItem(new PageBottom());

$oPage->draw();
