<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CredentialPrototypes extends AbstractMigration
{
    public function change(): void
    {
        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        // credential_prototype
        $table = $this->table('credential_prototype');
        $table
            ->addColumn('uuid', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('code', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('version', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('url', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('logo', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('user', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('created_at', 'datetime', [])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['uuid'], ['unique' => true, 'name' => 'idx_credential_prototype_uuid'])
            ->addIndex(['code'], ['unique' => true, 'name' => 'idx_credential_prototype_code'])
            ->addIndex(['uuid', 'version'], ['name' => 'idx_credential_prototype_version', 'unique' => false])
            ->addForeignKey('user', 'user', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION', 'constraint' => 'cp2u-user_must_exist'])
            ->create();

        // credential_prototype_field
        $fieldTable = $this->table('credential_prototype_field');
        $fieldTable
            ->addColumn('credential_prototype_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('keyword', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('hint', 'string', ['limit' => 256, 'null' => true])
            ->addColumn('default_value', 'string', ['limit' => 256, 'null' => true])
            ->addColumn('required', 'boolean', ['default' => false, 'null' => false])
            ->addColumn('options', 'text', ['null' => true])
            ->addIndex(['credential_prototype_id', 'keyword'], ['unique' => true, 'name' => 'idx_credprototype_field_unique'])
            ->addForeignKey('credential_prototype_id', 'credential_prototype', 'id', ['constraint' => 'fk_credprototype_field', 'delete' => 'CASCADE'])
            ->create();

        // credential_prototype_translations
        $transTable = $this->table('credential_prototype_translations');
        $transTable
            ->addColumn('credential_prototype_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('lang', 'string', ['limit' => 5, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addIndex(['credential_prototype_id', 'lang'], ['unique' => true, 'name' => 'idx_credprototype_lang'])
            ->addForeignKey('credential_prototype_id', 'credential_prototype', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // credential_prototype_field_translations
        $fieldTransTable = $this->table('credential_prototype_field_translations');
        $fieldTransTable
            ->addColumn('credential_prototype_field_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('lang', 'string', ['limit' => 5, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('hint', 'text', ['null' => true])
            ->addIndex(['credential_prototype_field_id', 'lang'], ['unique' => true, 'name' => 'idx_credprototype_field_lang'])
            ->addForeignKey('credential_prototype_field_id', 'credential_prototype_field', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
