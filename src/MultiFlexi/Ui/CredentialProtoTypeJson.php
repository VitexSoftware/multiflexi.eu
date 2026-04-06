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
 * Display credential prototype as formatted JSON with download button.
 */
class CredentialProtoTypeJson extends \Ease\Html\DivTag
{
    public function __construct(\MultiFlexi\CredentialProtoType $prototype, $properties = [])
    {
        $jsonData = $prototype->exportJson();
        $jsonString = json_encode($jsonData, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);
        parent::__construct(new \Ease\Html\PreTag($jsonString), $properties);
        $this->addTagClass('ui-monospace');

        if ($prototype->getMyKey()) {
            $this->addItem(new \Ease\TWB5\LinkButton(
                'credentialtypejson.php?id='.$prototype->getMyKey(),
                _('Download').' '.$prototype->getDataValue('code').'.credentialtype.json',
                'info',
            ));
        }
    }
}
