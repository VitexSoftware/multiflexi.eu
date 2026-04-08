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
use Ease\TWB5\Panel;
use Ease\TWB5\Row;
use MultiFlexi\Application;

/**
 * Description of ApplicationPanel.
 *
 * @author vitex
 */
class ApplicationPanel extends Panel
{
    public Row $headRow;

    /**
     * @param Application $application
     * @param mixed       $content
     * @param mixed       $footer
     */
    public function __construct($application, $content = null, $footer = null)
    {
        $cid = $application->getMyKey();
        $uuid = $application->getDataValue('uuid');
        $this->headRow = new Row();
        $this->headRow->addColumn(2, [new AppLogo($application, ['style' => 'height: 60px']), '&nbsp;', $application->getRecordName()]);
        $this->headRow->addColumn(4, [new LinkButton('app.php?id='.$cid, '🧩&nbsp;'._('Application'), 'primary btn-lg')]);

        if ($cid && $uuid) {
            $installButton = new \Ease\Html\ButtonTag('📦&nbsp;'._('Install'), [
                'class' => 'btn btn-warning btn-lg',
                'type' => 'button',
                'id' => 'install-app-btn',
                'data-uuid' => $uuid,
            ]);
            $this->headRow->addColumn(4, $installButton);
        }

        parent::__construct($this->headRow, 'default', $content, $footer);

        if ($cid && $uuid) {
            $exportUrl = 'app.php?export='.$uuid;
            WebPage::singleton()->addJavaScript(<<<JS
$(document).ready(function() {
    var STORAGE_KEY = 'multiflexi_install_domain';

    $('#install-app-btn').on('click', function() {
        var savedDomain = '';
        try { savedDomain = localStorage.getItem(STORAGE_KEY) || ''; } catch(e) {}

        var domain = prompt('{self::jsEscape(_('Enter your MultiFlexi instance domain (e.g. multiflexi.example.com):'))}', savedDomain);
        if (!domain) return;

        domain = domain.replace(/^https?:\\/\\//, '').replace(/\\/+$/, '');
        try { localStorage.setItem(STORAGE_KEY, domain); } catch(e) {}

        var exportUrl = window.location.origin + window.location.pathname.replace(/[^/]*$/, '') + '{$exportUrl}';
        var installUrl = 'https://' + domain + '/app.php?action=import&app_json_url=' + encodeURIComponent(exportUrl);
        window.open(installUrl, '_blank');
    });
});
JS);
        }
    }

    private static function jsEscape(string $str): string
    {
        return addcslashes($str, "'\\");
    }
}
