<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class User extends AbstractMigration
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
        $table = $this->table('user', ['collation' => 'utf8mb3_general_ci']);
        $table
            ->addColumn('enabled', 'integer', ['default' => '0'])
            ->addColumn('settings', 'text', ['null' => true])
            ->addColumn('email', 'string', ['limit' => 128])
            ->addColumn('firstname', 'string', ['null' => true, 'limit' => 32])
            ->addColumn('lastname', 'string', ['null' => true, 'limit' => 32])
            ->addColumn('password', 'string', ['limit' => 40])
            ->addColumn('login', 'string', ['limit' => 32])
            ->addColumn('DatCreate', 'datetime', [])
            ->addColumn('DatSave', 'datetime', ['null' => true])
            ->addIndex(['login', 'email'], ['name' => 'login', 'unique' => true])
            ->create();

    }
}
