<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AppSupportTables extends AbstractMigration
{
    public function change(): void
    {
        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        // conffield - application configuration fields
        $conffield = $this->table('conffield');
        $conffield
            ->addColumn('app_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('keyname', 'string', ['null' => true, 'limit' => 64])
            ->addColumn('type', 'string', ['null' => true, 'limit' => 32])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 1024])
            ->addColumn('hint', 'string', ['null' => false, 'default' => '', 'limit' => 1024])
            ->addColumn('note', 'string', ['null' => false, 'default' => '', 'limit' => 1024])
            ->addColumn('defval', 'string', ['null' => true, 'limit' => 256])
            ->addColumn('required', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('secret', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('multiline', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('expiring', 'boolean', ['null' => false, 'default' => 0])
            ->addIndex(['app_id', 'keyname'], ['unique' => true, 'name' => 'conffield_app_key'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // app_translations
        $appTranslations = $this->table('app_translations');
        $appTranslations
            ->addColumn('app_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('lang', 'string', ['null' => false, 'limit' => 5])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('title', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addIndex(['app_id', 'lang'], ['unique' => true, 'name' => 'app_translations_lang'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // app_artifacts
        $appArtifacts = $this->table('app_artifacts');
        $appArtifacts
            ->addColumn('app_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('path', 'string', ['null' => false, 'limit' => 512])
            ->addColumn('type', 'string', ['null' => false, 'limit' => 128])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['app_id'], ['name' => 'app_artifacts_app_id'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // app_artifact_translations
        $appArtifactTranslations = $this->table('app_artifact_translations');
        $appArtifactTranslations
            ->addColumn('app_artifact_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('lang', 'string', ['null' => false, 'limit' => 5])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('hint', 'text', ['null' => true])
            ->addIndex(['app_artifact_id', 'lang'], ['unique' => true, 'name' => 'app_artifact_trans_lang'])
            ->addForeignKey('app_artifact_id', 'app_artifacts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // app_exit_codes
        $appExitCodes = $this->table('app_exit_codes');
        $appExitCodes
            ->addColumn('app_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('exit_code', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('lang', 'string', ['null' => false, 'limit' => 5])
            ->addColumn('description', 'text', ['null' => false])
            ->addColumn('severity', 'string', ['null' => false, 'default' => 'error', 'limit' => 20])
            ->addColumn('retry', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('DatCreate', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('DatSave', 'datetime', ['null' => true])
            ->addIndex(['app_id', 'exit_code', 'lang'], ['unique' => true, 'name' => 'app_exit_codes_unique'])
            ->addForeignKey('app_id', 'apps', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
