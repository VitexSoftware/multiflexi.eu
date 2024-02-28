<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Log extends AbstractMigration
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
        $table = $this->table('log');
        $table
                ->addColumn('apps_id', 'integer', ['null' => true, 'comment' => 'application used'])
                ->addColumn('user_id', 'integer', ['null' => true, 'comment' => 'signed user'])
                ->addColumn('severity', 'string', ['comment' => 'message type'])
                ->addColumn('venue', 'string', ['comment' => 'message producer'])
                ->addColumn('message', 'text', ['comment' => 'main text'])
                ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['apps_id'], ['unique' => true])
                ->addIndex('user_id')
                ->create();

                if ($this->adapter->getAdapterType() != 'sqlite') {
                    $table
                        ->changeColumn('id', 'integer', ['identity' => true, 'signed' => false])
                        ->save();
                }

    }
}
