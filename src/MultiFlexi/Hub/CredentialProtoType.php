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
            'logo' => ['name' => 'logo', 'type' => 'display', 'label' => _('Logo'), 'searchable' => false, 'orderable' => false],
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
    public function getAllForDataTable($conditions = []): array
    {
        $data = [];
        $tableColumns = self::columns();
        $dtColumns = \array_key_exists('columns', $conditions) ? $conditions['columns'] : array_values($tableColumns);
        unset($conditions['columns'], $conditions['_'], $conditions['class']);

        $query = $this->listingQuery();
        $recordsTotal = \count($query);

        if ($recordsTotal && !empty($conditions['search']['value'])) {
            $like = $this->getPdo()->quote('%'.strtolower((string) $conditions['search']['value']).'%');

            foreach ($tableColumns as $colProps) {
                $searchable = \array_key_exists('searchable', $colProps) ? (bool) $colProps['searchable'] : true;

                if ($searchable && \array_key_exists('name', $colProps)) {
                    $query->whereOr('LOWER('.$this->getMyTable().'.`'.$colProps['name'].'`) LIKE '.$like);
                }
            }

            unset($conditions['search']);
        }

        foreach ($conditions as $condName => $condValue) {
            switch ($condName) {
                case 'draw':
                case 'start':
                case 'length':
                case 'order':
                case 'search':
                    break;

                default:
                    if (\array_key_exists($condName, $tableColumns)) {
                        $query->where($this->getMyTable().'.'.$condName, $condValue);
                    }

                    break;
            }
        }

        $recordsFiltered = \count($query);

        if (\array_key_exists('order', $conditions)) {
            foreach ($conditions['order'] as $order) {
                $columnIndex = (int) ($order['column'] ?? 0);
                $columnName = $dtColumns[$columnIndex]['data'] ?? null;
                $direction = strtoupper((string) ($order['dir'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

                if ($columnName && \array_key_exists($columnName, $tableColumns)) {
                    $query->orderBy($this->getMyTable().'.'.$columnName.' '.$direction);
                }
            }
        }

        if (\array_key_exists('length', $conditions) && (int) $conditions['length'] >= 0) {
            $query->limit((int) $conditions['length']);
        }

        $query->offset(\array_key_exists('start', $conditions) ? (int) $conditions['start'] : 0);

        foreach ($query as $dataRow) {
            $dataRow['DT_RowId'] = 'row_'.$dataRow['id'];
            $dataRow['logo'] = self::logoImageHtml($dataRow);

            if (isset($dataRow['name'])) {
                $dataRow['name'] = '<a href="credentialtype.php?id='.(int) $dataRow['id'].'" class="link-primary">'.
                    htmlspecialchars((string) $dataRow['name'], \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8').'</a>';
            }

            $data[] = $dataRow;
        }

        return [
            'draw' => \array_key_exists('draw', $conditions) ? (int) $conditions['draw'] : 0,
            'recordsTotal' => (int) $recordsTotal,
            'recordsFiltered' => (int) $recordsFiltered,
            'data' => $data,
        ];
    }

    public static function logoUrl(array $dataRow): string
    {
        $params = [];

        if (!empty($dataRow['uuid'])) {
            $params['uuid'] = (string) $dataRow['uuid'];
        }

        if (!empty($dataRow['logo'])) {
            $params['logo'] = (string) $dataRow['logo'];
        }

        return 'credentialtypeimage.php'.($params ? '?'.http_build_query($params) : '');
    }

    public function getLogoUrl(): string
    {
        return self::logoUrl($this->getData());
    }

    private static function logoImageHtml(array $dataRow): string
    {
        $src = htmlspecialchars(self::logoUrl($dataRow), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $alt = htmlspecialchars((string) ($dataRow['name'] ?? ''), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

        return '<img src="'.$src.'" alt="'.$alt.'" class="img-thumbnail" style="max-width: 32px; max-height: 32px;">';
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
        return '';
    }
}
