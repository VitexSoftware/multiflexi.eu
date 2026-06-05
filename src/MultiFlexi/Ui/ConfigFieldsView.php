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
 * Display application configuration fields in a table.
 */
class ConfigFieldsView extends \Ease\Html\TableTag
{
    /**
     * @param \MultiFlexi\ConfigFields $configFields
     */
    public function __construct($configFields, array $properties = [])
    {
        parent::__construct(null, array_merge(['class' => 'table table-striped table-sm'], $properties));

        $this->addRowHeaderColumns([
            _('Key'),
            _('Type'),
            _('Required'),
            _('Description'),
        ]);

        foreach ($configFields as $field) {
            $this->addRowColumns([
                new \Ease\Html\PairTag('code', [], $field->getCode()),
                $field->getType(),
                $field->isRequired() ? '✔' : '',
                $field->getDescription(),
            ]);
        }
    }
}
