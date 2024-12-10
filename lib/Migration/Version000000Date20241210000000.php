<?php

namespace OCA\MailProvision\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20241210000000 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('mailprovision_accounts')) {
            $table = $schema->createTable('mailprovision_accounts');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('email', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
            $table->addColumn('username', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
            $table->addColumn('password', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
            $table->addColumn('imap_host', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
            $table->addColumn('imap_port', 'integer', [
                'notnull' => true,
                'default' => 993
            ]);
            $table->addColumn('smtp_host', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
            $table->addColumn('smtp_port', 'integer', [
                'notnull' => true,
                'default' => 587
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('updated_at', 'datetime', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id'], 'mp_accounts_pkey');
            $table->addIndex(['user_id'], 'mp_user_id_idx');
        }

        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     */
    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
        // Hier können zusätzliche Operationen nach der Schemaänderung durchgeführt werden, falls erforderlich
    }
}