<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Apps extends AbstractMigration
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('apps', ['collation' => 'utf8mb3_general_ci']);
        $table
                ->addColumn('enabled', 'integer', ['default' => '0'])
                ->addColumn('image', 'text', [])
                ->addColumn('name', 'string', ['default' => '', 'limit' => 64])
                ->addColumn('description', 'string', ['null' => true, 'limit' => 255, 'comment' => 'App Description'])
                ->addColumn('executable', 'string', ['limit' => 255, 'comment' => '/usr/bin/runme'])
                ->addColumn('DatCreate', 'datetime', [])
                ->addColumn('DatUpdate', 'datetime', ['null' => true])
                ->addColumn('setup', 'string', ['null' => true, 'limit' => 256])
                ->addColumn('cmdparams', 'string', ['null' => true, 'limit' => 256])
                ->addColumn('deploy', 'string', ['limit' => 255, 'comment' => 'deploy command'])
                ->addColumn('homepage', 'string', ['limit' => 255, 'comment' => 'command online source'])
                ->addColumn('requirements', 'string', ['limit' => 255, 'comment' => 'MultiFlexi Modules required by Application'])
                ->addColumn('ociimage', 'string', ['null' => true, 'default' => '', 'limit' => 255, 'comment' => 'Container Image'])
                ->addColumn('version', 'string', ['null' => true, 'default' => '', 'limit' => 255, 'comment' => 'application version'])
                ->addColumn('user', 'integer', ['length'=>11,'signed' => false])
                ->addColumn('uuid', 'string', ['null' => true, 'limit' => 36])
                ->addIndex(['name'], ['name' => 'nazev', 'unique' => true])
                ->addIndex(['uuid'], ['name' => 'uuid', 'unique' => true])
                ->addForeignKey('user', 'user', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION', 'constraint' => 'a2u-user_must_exist'])
                ->create();

        if ($this->adapter->getAdapterType() == 'mysql') {
            $table
                    ->changeColumn('image', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
                    ->save();
        }
    }
}
