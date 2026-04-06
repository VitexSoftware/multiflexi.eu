<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserPasswordLength extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user');
        $table->changeColumn('password', 'string', ['limit' => 255])
              ->update();
    }
}
