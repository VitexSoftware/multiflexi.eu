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
 * Page Bottom.
 *
 * @author     Vitex <vitex@hippy.cz>
 */
class PageBottom extends \Ease\Html\FooterTag
{
    public const string BUILD = '';

    public function finalize(): void
    {
        if ($this->finalized()) {
            return;
        }

        $this->setTagID('footer');
        $this->addTagClass('footer');

        $columns = [
            _('Product') => [
                'whatis.php' => _('What is MultiFlexi?'),
                'apps.php' => _('Apps'),
                'credentialtypes.php' => _('Credential Types'),
                'install.php' => _('Install'),
                MainMenu::DEMO_URL => _('Demo'),
                '/doc/' => _('Documentation'),
            ],
            _('Community') => [
                'https://github.com/VitexSoftware/MultiFlexi' => 'GitHub – MultiFlexi',
                'https://github.com/VitexSoftware/multiflexi.eu' => 'GitHub – multiflexi.eu',
                'https://www.linkedin.com/in/vitexsoftware/' => 'LinkedIn',
                'https://f.cz/@vitexsoftware' => 'Mastodon',
            ],
            'Vitex Software' => [
                'https://vitexsoftware.cz/' => 'vitexsoftware.cz',
                'https://vitexsoftware.cz/automatizace.php' => _('Accounting automation'),
                'https://vitexsoftware.cz/kontakt.php' => _('Contact'),
            ],
        ];

        $cols = '';

        foreach ($columns as $heading => $links) {
            $cols .= '<div><h4>'.$heading.'</h4><ul>';

            foreach ($links as $url => $label) {
                $cols .= '<li><a href="'.htmlspecialchars($url).'">'.$label.'</a></li>';
            }

            $cols .= '</ul></div>';
        }

        $motto = _('A better world starts with good software');
        $version = htmlspecialchars((string) \Ease\Shared::appVersion());
        $copyright = sprintf(_('MultiFlexi.eu %s · © 2024–2026 %s'), $version, '<a href="https://vitexsoftware.cz/">Vitex Software</a>');
        $openSource = _('Open source under the MIT license');

        $this->addItem(<<<HTML
<svg class="topo" viewBox="0 0 1440 200" preserveAspectRatio="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="1"><path d="M0 150C200 120 320 170 520 140S860 90 1060 130 1340 170 1440 140"/><path d="M0 170C220 145 330 190 540 160S880 115 1080 150 1350 190 1440 165"/><path d="M0 125C190 95 310 145 500 118S840 65 1040 105 1330 145 1440 115"/><path d="M0 100C180 72 300 120 480 95S820 42 1020 82 1320 120 1440 92"/></g></svg>
<div class="foot-wrap">
  <div class="foot-top">
    <a class="brand" href="index.php"><img src="images/multiflexi-logo.svg" alt="" width="36" height="36"><span class="brand-name">Multi<b>Flexi</b></span></a>
    <span class="motto hand">{$motto} ☮</span>
    <div class="social">
      <a class="icon-btn" href="https://github.com/VitexSoftware/MultiFlexi" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
      <a class="icon-btn" href="https://www.linkedin.com/in/vitexsoftware/" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
      <a class="icon-btn" rel="me" href="https://f.cz/@vitexsoftware" aria-label="Mastodon"><i class="fa-brands fa-mastodon"></i></a>
      <a class="icon-btn" href="mailto:info@vitexsoftware.cz" aria-label="E-mail"><i class="fa-regular fa-envelope"></i></a>
    </div>
  </div>
  <div class="foot-cols mf-cols">{$cols}</div>
  <div class="foot-bottom"><span>{$copyright}</span><span>{$openSource}</span></div>
</div>
HTML);

        parent::finalize();
    }
}
