<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CredentialPrototypeHomepageTags extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('credential_prototype');
        $table
            ->addColumn('homepage', 'string', ['limit' => 255, 'null' => true, 'after' => 'url'])
            ->addColumn('tags', 'string', ['limit' => 255, 'null' => true, 'after' => 'homepage'])
            ->update();
    }
}
