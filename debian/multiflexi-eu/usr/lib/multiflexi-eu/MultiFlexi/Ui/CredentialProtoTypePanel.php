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
use MultiFlexi\CredentialProtoType;

/**
 * Panel for displaying credential prototype details.
 */
class CredentialProtoTypePanel extends Panel
{
    public Row $headRow;

    /**
     * @param CredentialProtoType $prototype
     * @param mixed               $content
     * @param mixed               $footer
     */
    public function __construct($prototype, $content = null, $footer = null)
    {
        $cid = $prototype->getMyKey();
        $this->headRow = new Row();
        $this->headRow->addColumn(2, ['🔑&nbsp;', $prototype->getRecordName()]);
        $this->headRow->addColumn(4, [new LinkButton('credentialtype.php?id=' . $cid, '🔑&nbsp;' . _('Credential Type'), 'primary btn-lg')]);

        parent::__construct($this->headRow, 'default', $content, $footer);
    }
}
