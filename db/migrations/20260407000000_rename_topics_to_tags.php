<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RenameTopicsToTags extends AbstractMigration
{
    public function up(): void
    {
        $this->table('apps')
            ->renameColumn('topics', 'tags')
            ->update();
    }

    public function down(): void
    {
        $this->table('apps')
            ->renameColumn('tags', 'topics')
            ->update();
    }
}
