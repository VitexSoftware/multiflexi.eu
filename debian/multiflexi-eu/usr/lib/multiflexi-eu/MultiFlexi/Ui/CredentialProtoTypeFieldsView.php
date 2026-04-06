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
 * Display fields of a credential prototype in a table.
 */
class CredentialProtoTypeFieldsView extends \Ease\Html\TableTag
{
    /**
     * @param \MultiFlexi\CredentialProtoType $prototype
     */
    public function __construct($prototype, array $properties = [])
    {
        parent::__construct(null, array_merge(['class' => 'table table-striped table-sm'], $properties));

        $this->addRowHeaderColumns([
            _('Keyword'),
            _('Type'),
            _('Name'),
            _('Required'),
            _('Description'),
        ]);

        if ($prototype->getMyKey()) {
            $fielder = new \MultiFlexi\CredentialProtoTypeField();
            $fields = $fielder->listFields($prototype->getMyKey());

            foreach ($fields as $field) {
                $this->addRowColumns([
                    new \Ease\Html\CodeTag($field['keyword']),
                    $field['type'],
                    $field['name'],
                    $field['required'] ? '✔' : '',
                    $field['description'],
                ]);
            }

            if (empty($fields)) {
                $this->addRowColumns([new \Ease\Html\TdTag(_('No fields defined'), ['colspan' => 5, 'class' => 'text-muted'])]);
            }
        }
    }
}
