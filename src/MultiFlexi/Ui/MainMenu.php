<?php

declare(strict_types=1);

/**
 * MutliFlexi.eu Main Menu
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2024 Vitex Software
 */

namespace MultiFlexi\Ui;

/**
 * Description of MainMenu
 *
 * @author vitex
 */
class MainMenu extends \Ease\Html\NavTag
{
    public function __construct()
    {
        $logoLink = new \Ease\Html\ATag('index.php', new \Ease\Html\ImgTag('images/multiflexi-logo.svg', 'MultiFlexi', ['width' => "30", 'height' => "24", 'class' => "d-inline-block align-text-top"]), ['class' => 'navbar-brand']);
        $logoLink->addItem('MultiFlexi');
        $container = new \Ease\TWB5\Container($logoLink);
        $container->addItem($this->navBarToggler());
        $container->addItem($this->navBarCollapse());
        parent::__construct($container, ['class' => 'navbar navbar-expand-lg navbar-light bg-light']);
    }

    public function navBarToggler()
    {
        return new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag(null, ['class' => 'navbar-toggler-icon']), [
            'class' => "navbar-toggler",
            'type' => "button",
            'data-bs-toggle' => "collapse",
            'data-bs-target' => "#navbarNav",
            'aria-controls' => "navbarNav",
            'aria-expanded' => "false",
            'aria-label' => _("Toggle navigation")
        ]);
    }

    public function navBarCollapse()
    {
        $navbarNav = new \Ease\Html\UlTag(null, ['class' => 'navbar-nav me-auto mb-2 mb-lg-0', 'style' => "--bs-scroll-height: 100px;"]);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('https://demo.multiflexi.eu/', _('Demo Site'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('apps.php', _('Applications'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('myapps.php', _('My Apps'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('app.php', _('Submit'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('logout.php', _('Logout'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('app.php', _('Submit'), ['class' => 'nav-link disabled']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('createaccount.php', _('Sign In'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('login.php', _('Sign On'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        return new \Ease\Html\DivTag($navbarNav, ['class' => "collapse navbar-collapse", 'id' => "navbarNav"]);
    }
}
