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

/**
 * Description of MainMenu.
 *
 * @author vitex
 */
class MainMenu extends \Ease\Html\NavTag
{
    public function __construct()
    {
        $logoLink = new \Ease\Html\ATag('index.php', new \Ease\Html\ImgTag('images/multiflexi-logo.svg', 'MultiFlexiHUB', ['width' => '30', 'height' => '24', 'class' => 'd-inline-block align-text-top']), ['class' => 'navbar-brand']);
        $logoLink->addItem('MultiFlexi');
        $container = new \Ease\TWB5\Container($logoLink);

        $container->addItem($this->navBarToggler());
        $container->addItem($this->navBarCollapse());
        parent::__construct($container, ['class' => 'navbar navbar-expand-lg navbar-dark mf-navbar sticky-top']);
    }

    public function navBarToggler()
    {
        return new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag(null, ['class' => 'navbar-toggler-icon']), [
            'class' => 'navbar-toggler',
            'type' => 'button',
            'data-bs-toggle' => 'collapse',
            'data-bs-target' => '#navbarNav',
            'aria-controls' => 'navbarNav',
            'aria-expanded' => 'false',
            'aria-label' => _('Toggle navigation'),
        ]);
    }

    public function navBarCollapse()
    {
        $oUser = \Ease\Shared::user();

        $navbarNav = new \Ease\Html\UlTag(null, ['class' => 'navbar-nav ms-auto flex-nowrap navbar-expand mb-2 mb-lg-0', 'style' => '--bs-scroll-height: 100px;']);

        $navbarNav->addItemSmart(new \Ease\Html\ATag('whatis.php', '<img height=20 src=images/multiflexi-logo.svg> '._('What is MultiFlexi?'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('https://demo.multiflexi.eu/login.php?login=demo&password=demo', '<img height=20 src=images/rocket.svg> '._('Demo'), ['class' => 'nav-link', 'target' => '_blank']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('apps.php', '<img height=20 src=images/apps.svg> '._('Apps'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('credentialtypes.php', '<img height=20 src=images/vault.svg> '._('Credential Types'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('install.php', '<img height=20 src=images/set.svg> '._('Install'), ['class' => 'nav-link']), ['class' => 'nav-item']);

        if ($oUser->isLogged()) {
            $navbarNav->addItemSmart(new \Ease\Html\ATag('myapps.php', '<img height=20 src=images/apps.svg> '._('My Apps'), ['class' => 'nav-link']), ['class' => 'nav-item']);
            $navbarNav->addItemSmart(new \Ease\Html\ATag('mycredentialtypes.php', '<img height=20 src=images/vault.svg> '._('My Credential Types'), ['class' => 'nav-link']), ['class' => 'nav-item']);
            $navbarNav->addItemSmart(new \Ease\Html\ATag('app.php', '➕ '._('Submit App'), ['class' => 'nav-link']), ['class' => 'nav-item']);
            $navbarNav->addItemSmart(new \Ease\Html\ATag('credentialtype.php', '➕ '._('Submit Credential Type'), ['class' => 'nav-link']), ['class' => 'nav-item']);
            $navbarNav->addItemSmart(new \Ease\Html\ATag('logout.php', '<img height=20 src=images/application-exit.svg> '._('Sign Off'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        } else {
            $navbarNav->addItemSmart(new \Ease\Html\ATag('createaccount.php', _('Sign On'), ['class' => 'nav-link']), ['class' => 'nav-item']);
            $navbarNav->addItemSmart(new \Ease\Html\ATag('login.php', _('Sign In'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        }

        // Add language selector — last item in navbar; CSS pushes it to the far right
        $navbarNav->addItemSmart(new \Ease\TWB5\Widgets\LangSelect('locale'), ['class' => 'nav-item']);

        return new \Ease\Html\DivTag($navbarNav, ['class' => 'collapse navbar-collapse', 'id' => 'navbarNav']);
    }
}
