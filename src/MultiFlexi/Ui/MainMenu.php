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
    public const DEMO_URL = 'https://demo.multiflexi.eu/login.php?login=demo&password=demo';

    public function __construct()
    {
        $logoLink = new \Ease\Html\ATag('index.php', '<img src="images/multiflexi-logo.svg" alt="" width="36" height="36" class="brand-logo"><span class="brand-name">Multi<b>Flexi</b></span>', ['class' => 'navbar-brand']);
        $container = new \Ease\TWB5\Container($logoLink);
        $container->setTagClass('container-fluid');

        $container->addItem($this->navBarToggler());
        $container->addItem($this->navBarCollapse());
        parent::__construct($container, ['class' => 'navbar navbar-expand-lg site-header fixed-top']);
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
        $current = basename($_SERVER['SCRIPT_NAME'] ?? '');
        $navbarNav = new \Ease\Html\UlTag(null, ['class' => 'navbar-nav ms-auto mb-2 mb-lg-0']);

        $item = static function (string $url, string $label, array $properties = []) use ($navbarNav, $current): void {
            $active = basename((string) parse_url($url, \PHP_URL_PATH)) === $current;
            $navbarNav->addItemSmart(
                new \Ease\Html\ATag($url, $label, array_merge(['class' => 'nav-link'], $active ? ['aria-current' => 'page'] : [], $properties)),
                ['class' => 'nav-item'.($active ? ' active' : '')],
            );
        };

        $item('whatis.php', _('What is MultiFlexi?'));
        $item('apps.php', _('Apps'));
        $item('credentialtypes.php', _('Credential Types'));
        $item('install.php', _('Install'));
        $item('/doc/', _('Documentation'));

        if ($oUser->isLogged()) {
            $account = [
                'myapps.php' => _('My Apps'),
                'mycredentialtypes.php' => _('My Credential Types'),
                'app.php' => '➕ '._('Submit App'),
                'credentialtype.php' => '➕ '._('Submit Credential Type'),
                'logout.php' => _('Sign Off'),
            ];
            $menu = '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">'._('My account').'</a><ul class="dropdown-menu dropdown-menu-end">';

            foreach ($account as $url => $label) {
                $menu .= '<li><a class="dropdown-item" href="'.$url.'">'.$label.'</a></li>';
            }

            $navbarNav->addItemSmart($menu.'</ul>', ['class' => 'nav-item dropdown']);
        }

        $controls = new \Ease\Html\DivTag(null, ['class' => 'header-controls']);
        // 17 languages – a dropdown instead of a row of pills.
        $current = (string) \Ease\Locale::$localeUsed;
        $options = '';

        foreach (\Ease\Locale::singleton()->availble() as $code => $name) {
            $lang = substr($code, 0, 2);
            $options .= '<li><a class="dropdown-item'.($code === $current ? ' active' : '').'" href="?'.htmlspecialchars(http_build_query(array_merge($_GET, ['locale' => $code]))).'" hreflang="'.$lang.'" lang="'.$lang.'">'
                .htmlspecialchars((string) $name).'<small>'.strtoupper($lang).'</small></a></li>';
        }

        $controls->addItem('<div class="dropdown lang-drop"><button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="'._('Language').'">'
            .'<i class="fa-solid fa-globe" aria-hidden="true"></i> '.strtoupper(substr($current, 0, 2)).'</button><ul class="dropdown-menu dropdown-menu-end">'.$options.'</ul></div>');
        $controls->addItem('<button type="button" class="icon-btn theme-toggle" aria-label="'._('Switch light / dark mode').'" title="'._('Switch light / dark mode').'">'
            .'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/></svg></button>');

        if (!$oUser->isLogged()) {
            $controls->addItem(new \Ease\Html\ATag('login.php', '<i class="fa-regular fa-user"></i>', ['class' => 'icon-btn', 'title' => _('Sign In'), 'aria-label' => _('Sign In')]));
        }

        $controls->addItem(new \Ease\Html\ATag(self::DEMO_URL, _('Try the demo'), ['class' => 'btn btn-glow btn-sm', 'target' => '_blank', 'rel' => 'noopener']));

        return new \Ease\Html\DivTag([$navbarNav, $controls], ['class' => 'collapse navbar-collapse', 'id' => 'navbarNav']);
    }
}
