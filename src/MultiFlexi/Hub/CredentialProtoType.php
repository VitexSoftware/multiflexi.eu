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

namespace MultiFlexi\Hub;

/**
 * CredentialProtoType model for the hub site.
 * Extends the vendor CredentialProtoType with DataTable columns and user ownership.
 */
class CredentialProtoType extends \MultiFlexi\CredentialProtoType
{
    /**
     * {@inheritDoc}
     */
    public static function columns($columns = []): array
    {
        return array_merge($columns, [
            'id' => ['name' => 'id', 'type' => 'int', 'hidden' => true, 'label' => _('Id')],
            'code' => ['name' => 'code', 'type' => 'text', 'label' => _('Code')],
            'name' => ['name' => 'name', 'type' => 'text', 'label' => _('Name'),
                'detailPage' => 'credentialtype.php', 'idColumn' => 'id'],
            'description' => ['name' => 'description', 'type' => 'text', 'label' => _('Description')],
            'version' => ['name' => 'version', 'type' => 'text', 'label' => _('Version')],
            'uuid' => ['name' => 'uuid', 'type' => 'text', 'hidden' => true, 'label' => _('UUID')],
            'user' => ['name' => 'user', 'type' => 'int', 'hidden' => true, 'label' => _('Owner')],
        ]);
    }

    public function getGetDataTableColumns()
    {
        return self::columns();
    }

    public function preTableCode($tableID)
    {
        return '';
    }

    public function foterCallback($tableID)
    {
        return '';
    }

    public function tableCode($tableID)
    {
        return '';
    }

    public function postTableCode($tableID): string
    {
        return '';
    }

    /**
     * Override takeData to stamp user ownership.
     *
     * @param mixed $data
     */
    public function takeData($data): int
    {
        if (\is_array($data)) {
            if (!\array_key_exists('user', $data) || empty($data['user'])) {
                $user = \Ease\Shared::user();

                if ($user && $user->getUserID()) {
                    $data['user'] = $user->getUserID();
                }
            }

            if ((!\array_key_exists('uuid', $data) || empty($data['uuid'])) && !$this->getMyKey()) {
                $data['uuid'] = \Ease\Functions::guidv4();
            }
        }

        return parent::takeData($data);
    }

    /**
     * Save to SQL with timestamp handling.
     *
     * @param null|mixed $data
     */
    public function saveToSQL($data = null)
    {
        if (null === $data) {
            $data = $this->getData();
        }

        if (!$this->getMyKey()) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return parent::saveToSQL($data);
    }

    public function columnDefs()
    {
        return json_encode($this->columns());
    }
}
