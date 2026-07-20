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

use Phinx\Migration\AbstractMigration;

final class AppProducesConsumes extends AbstractMigration
{
    public function change(): void
    {
        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        $appProduces = $this->table('app_produces');
        $appProduces
            ->addColumn('app_id', 'integer', array_merge(['null' => false, 'comment' => 'FK to apps'], $unsigned))
            ->addColumn('name', 'string', ['null' => false, 'limit' => 128, 'comment' => 'Produces output name (key in JSON)'])
            ->addColumn('format', 'string', ['null' => false, 'default' => 'json', 'limit' => 32, 'comment' => 'file|json|text|url|custom'])
            ->addColumn('description_json', 'text', ['null' => true, 'default' => null, 'comment' => 'Localized description as JSON object'])
            ->addColumn('patterns_json', 'text', ['null' => true, 'default' => null, 'comment' => 'File-matching patterns as JSON array'])
            ->addColumn('fields_json', 'text', ['null' => true, 'default' => null, 'comment' => 'Produced JSON field metadata as JSON object'])
            ->addIndex(['app_id', 'name'], ['unique' => true, 'name' => 'app_produces_app_name'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        $appConsumes = $this->table('app_consumes');
        $appConsumes
            ->addColumn('app_id', 'integer', array_merge(['null' => false, 'comment' => 'FK to apps'], $unsigned))
            ->addColumn('name', 'string', ['null' => false, 'limit' => 128, 'comment' => 'Consumes input name (key in JSON)'])
            ->addColumn('format', 'string', ['null' => false, 'default' => 'json', 'limit' => 32, 'comment' => 'file|json|text|url|custom'])
            ->addColumn('description_json', 'text', ['null' => true, 'default' => null, 'comment' => 'Localized description as JSON object'])
            ->addColumn('required', 'boolean', ['null' => false, 'default' => true, 'comment' => 'Whether this input is required'])
            ->addColumn('target_env_key', 'string', ['null' => true, 'default' => null, 'limit' => 128, 'comment' => 'Environment key that receives the whole-file path'])
            ->addColumn('fields_json', 'text', ['null' => true, 'default' => null, 'comment' => 'Per-item field bindings as JSON object'])
            ->addIndex(['app_id', 'name'], ['unique' => true, 'name' => 'app_consumes_app_name'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
