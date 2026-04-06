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

use Ease\Html\DivTag;
use Ease\Html\InputFileTag;
use Ease\TWB5\Form;
use Ease\TWB5\SubmitButton;

class AppImportForm extends DivTag
{
    public function __construct(array $properties = [])
    {
        parent::__construct(null, $properties);
        $form = new Form(['method' => 'post', 'enctype' => 'multipart/form-data', 'action' => 'importjson.php']);
        $form->addInput(new InputFileTag('jsonfile', null, ['accept' => '.json,application/json']), _('Application JSON file'));
        $form->addItem(new SubmitButton(_('Import'), 'primary'));
        $this->addItem($form);
    }
}
