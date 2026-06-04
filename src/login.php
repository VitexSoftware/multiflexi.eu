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

use Ease\Html\DivTag;
use Ease\Html\ImgTag;
use Ease\Html\InputPasswordTag;
use Ease\Html\InputTextTag;
use Ease\Shared;
use Ease\TWB5\Form;
use Ease\TWB5\LinkButton;
use Ease\TWB5\SubmitButton;

require_once __DIR__.'/init.php';

$shared = Shared::singleton();

$login = $oPage->getRequestValue('login');

if ($login) {
    //    try {
    //        \Ease\Shared::user() = Shared::user(new User());
    //    } catch (PDOException $e) {
    //        echo 'Caught exception: ', $e->getMessage(), "\n";
    //    }
    if (\Ease\Shared::user()->tryToLogin($_POST)) {
        $oPage->redirect('main.php');
        session_write_close();

        exit;
    }
}

$oPage->addItem(new PageTop(_('Sign In')));

$loginFace = new DivTag(null, ['id' => 'LoginFace']);

$loginCard = new DivTag(null, ['class' => 'mf-login-card']);
$loginCard->addItem(new DivTag(
    new ImgTag('images/multiflexi-logo.svg', _('Logo'), ['style' => 'height: 80px']),
    ['class' => 'mf-login-logo'],
));
$loginCard->addItem(new \Ease\Html\H2Tag(_('Sign in to MultiFlexi')));

$loginCard->addItem(new \Ease\TWB5\FormGroup(
    _('Username'),
    new InputTextTag('login', $login, ['class' => 'form-control form-control-lg', 'autofocus' => 'autofocus']),
));
$loginCard->addItem(new \Ease\TWB5\FormGroup(
    _('Password'),
    new InputPasswordTag('password', '', ['class' => 'form-control form-control-lg']),
));
$loginCard->addItem(new DivTag(
    new SubmitButton('🚪&nbsp;'._('Sign in'), 'success btn-lg w-100', ['id' => 'signin']),
    ['class' => 'd-grid mt-3'],
));
$loginCard->addItem(new DivTag(
    new LinkButton('passwordrecovery.php', '🔑&nbsp;'._('Password recovery'), 'link btn-sm w-100'),
    ['class' => 'text-center mt-2'],
));

$loginFace->addItem(new Form(['method' => 'POST', 'action' => 'login.php'], [], $loginCard));
$oPage->container->addItem($loginFace);

$oPage->addItem(new PageBottom());

$oPage->draw();
