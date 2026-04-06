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

use Ease\Html\InputHiddenTag;
use Ease\Html\InputTextTag;
use Ease\Html\TextareaTag;
use Ease\TWB5\SubmitButton;

/**
 * Form for editing CredentialProtoType (TWB5 version for hub site).
 */
class CredentialProtoTypeEditorForm extends EngineForm
{
    public function afterAdd(): void
    {
        $this->addInput(new InputTextTag('code'), _('Code'));
        $this->addInput(new InputTextTag('name'), _('Credential Type Name'));
        $this->addInput(new InputTextTag('version'), _('Version'));
        $this->addInput(new InputTextTag('url'), _('URL'));
        $this->addInput(new InputTextTag('logo'), _('Logo URL'));
        $this->addInput(new TextareaTag('description'), _('Description'));
        $this->addItem(new SubmitButton(_('Save'), 'success'));

        if (null !== $this->engine->getDataValue('id')) {
            $this->addItem(new InputHiddenTag('id'));
        }

        if ($this->engine->getDataCount()) {
            $this->fillUp($this->engine->getData());
        }
    }
}
