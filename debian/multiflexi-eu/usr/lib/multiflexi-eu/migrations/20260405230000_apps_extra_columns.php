<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AppsExtraColumns extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('apps');
        $table
            ->addColumn('code', 'string', ['null' => true, 'limit' => 8, 'after' => 'version'])
            ->addColumn('topics', 'text', ['null' => true, 'after' => 'code'])
            ->addColumn('resultfile', 'string', ['null' => true, 'default' => '', 'limit' => 255, 'after' => 'topics'])
            ->addColumn('artifacts', 'string', ['null' => true, 'default' => '', 'limit' => 255, 'after' => 'resultfile'])
            ->addColumn('deffile', 'string', ['null' => true, 'limit' => 255, 'after' => 'artifacts'])
            ->addColumn('helmchart', 'string', ['null' => true, 'limit' => 255, 'after' => 'deffile'])
            ->addIndex(['code'], ['name' => 'code', 'unique' => true])
            ->addIndex(['deffile'], ['name' => 'deffile', 'unique' => true])
            ->update();
    }
}
