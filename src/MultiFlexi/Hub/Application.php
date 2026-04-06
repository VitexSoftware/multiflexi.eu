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
 * Application model for the hub site.
 * Extends the vendor Application with DataTable columns and user ownership.
 */
class Application extends \MultiFlexi\Application
{
    /**
     * {@inheritDoc}
     */
    public function columns($columns = []): array
    {
        return parent::columns([
            'id' => ['name' => 'id', 'type' => 'int', 'hidden' => true, 'label' => _('Id')],
            'image' => ['name' => 'image', 'type' => 'text', 'hidden' => true, 'label' => _('Image')],
            'name' => ['name' => 'name', 'type' => 'text', 'label' => _('Name'),
                'detailPage' => 'app.php', 'idColumn' => 'id'],
            'description' => ['name' => 'description', 'type' => 'text', 'label' => _('Description')],
            'homepage' => ['name' => 'homepage', 'type' => 'text', 'label' => _('Homepage')],
            'version' => ['name' => 'version', 'type' => 'text', 'label' => _('Version')],
            'uuid' => ['name' => 'uuid', 'type' => 'text', 'hidden' => true, 'label' => _('UUID')],
            'user' => ['name' => 'user', 'type' => 'int', 'hidden' => true, 'label' => _('Owner')],
        ]);
    }

    /**
     * Override to stamp current user as owner for new records.
     */
    #[\Override]
    public function takeData(array $data): int
    {
        if (!\array_key_exists('user', $data) || empty($data['user'])) {
            $user = \Ease\Shared::user();

            if ($user && $user->getUserID()) {
                $data['user'] = $user->getUserID();
            }
        }

        return parent::takeData($data);
    }

    /**
     * Complete data row for DataTable display - add image thumbnail.
     */
    public function completeDataRow(array $dataRowRaw): array|string
    {
        if (!empty($dataRowRaw['uuid'])) {
            $dataRowRaw['name'] = '<a href="app.php?id='.$dataRowRaw['id'].'">'.
                '<img src="appimage.php?uuid='.$dataRowRaw['uuid'].'" height="24" class="me-2">'.
                htmlspecialchars((string) $dataRowRaw['name']).'</a>';
        }

        return parent::completeDataRow($dataRowRaw);
    }
}
