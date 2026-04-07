<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ItemCoworkers extends AbstractMigration
{
    public function change(): void
    {
        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        $table = $this->table('item_coworker');
        $table
            ->addColumn('item_type', 'string', ['limit' => 32, 'null' => false, 'comment' => 'app or credential_prototype'])
            ->addColumn('item_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('user_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('granted_by', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('created_at', 'datetime', [])
            ->addIndex(['item_type', 'item_id', 'user_id'], ['unique' => true, 'name' => 'idx_coworker_unique'])
            ->addForeignKey('user_id', 'user', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION', 'constraint' => 'fk_coworker_user'])
            ->addForeignKey('granted_by', 'user', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION', 'constraint' => 'fk_coworker_granter'])
            ->create();
    }
}
