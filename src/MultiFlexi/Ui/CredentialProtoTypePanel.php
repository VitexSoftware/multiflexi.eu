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

use Ease\Html\ATag;
use Ease\Html\DivTag;
use Ease\TWB5\Badge;
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
        $logoUrl = method_exists($prototype, 'getLogoUrl') ? $prototype->getLogoUrl() : 'credentialtypeimage.php?uuid='.rawurlencode((string) $prototype->getDataValue('uuid'));
        $logo = new \Ease\Html\ImgTag($logoUrl, $prototype->getRecordName(), [
            'class' => 'img-thumbnail me-2',
            'style' => 'max-width: 48px; max-height: 48px;',
        ]);
        $this->headRow = new Row();
        $this->headRow->addColumn(2, [$logo, $prototype->getRecordName()]);
        $this->headRow->addColumn(4, [new LinkButton('credentialtype.php?id='.$cid, '🔑&nbsp;'._('Credential Type'), 'primary btn-lg')]);

        // Homepage link
        $metaCol = [];
        $homepage = $prototype->getDataValue('homepage');

        if (!empty($homepage)) {
            $metaCol[] = new DivTag(
                new ATag($homepage, $homepage, ['target' => '_blank', 'class' => 'text-decoration-none']),
                ['class' => 'mb-2'],
            );
        }

        // Tags badges
        $tags = $prototype->getDataValue('tags');

        if (!empty($tags)) {
            $tagsList = array_map('trim', explode(',', $tags));
            $badgesDiv = new DivTag(null, ['class' => 'd-flex flex-wrap gap-1']);

            foreach ($tagsList as $tag) {
                if (!empty($tag)) {
                    $badgesDiv->addItem(new Badge($tag, 'secondary'));
                }
            }

            $metaCol[] = $badgesDiv;
        }

        if (!empty($metaCol)) {
            $this->headRow->addColumn(6, $metaCol);
        }

        parent::__construct($this->headRow, 'default', $content, $footer);
    }
}
