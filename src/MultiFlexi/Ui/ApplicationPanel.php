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
        $this->headRow = new Row();
        $this->headRow->addColumn(2, [new AppLogo($application, ['style' => 'height: 60px']), '&nbsp;', $application->getRecordName()]);
        $this->headRow->addColumn(4, [new LinkButton('app.php?id='.$cid, '🧩&nbsp;'._('Application'), 'primary btn-lg')]);

        parent::__construct($this->headRow, 'default', $content, $footer);
    }
}
