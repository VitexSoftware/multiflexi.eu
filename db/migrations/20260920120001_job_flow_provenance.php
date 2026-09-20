<?php

declare(strict_types=1);

/**
 * This file is part of the MultiFlexi package
 *
 * https://multiflexi.eu/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Link jobs to flow runs/steps and optional source job for chain provenance.
 */
final class JobFlowProvenance extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('job');
        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        if (!$table->hasColumn('source_job_id')) {
            $table->addColumn('source_job_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'Upstream job that caused this job (flow/chain provenance)',
            ], $unsigned));
        }

        if (!$table->hasColumn('flow_run_id')) {
            $table->addColumn('flow_run_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'FK to flow_run when scheduled by FlowInterpreter',
            ], $unsigned));
        }

        if (!$table->hasColumn('flow_step_id')) {
            $table->addColumn('flow_step_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'FK to flow_run_step that scheduled this job',
            ], $unsigned));
        }

        $table->save();

        // Re-load for indexes/FKs (Phinx needs fresh table handle after addColumn save).
        $table = $this->table('job');

        if (!$table->hasIndexByName('idx_job_source_job')) {
            $table->addIndex(['source_job_id'], ['name' => 'idx_job_source_job']);
        }

        if (!$table->hasIndexByName('idx_job_flow_run')) {
            $table->addIndex(['flow_run_id'], ['name' => 'idx_job_flow_run']);
        }

        if (!$table->hasIndexByName('idx_job_flow_step')) {
            $table->addIndex(['flow_step_id'], ['name' => 'idx_job_flow_step']);
        }

        $table->save();

        if ($this->hasTable('flow_run') && !$this->foreignKeyExists('job', 'fk_job_flow_run')) {
            $this->table('job')
                ->addForeignKey('flow_run_id', 'flow_run', 'id', [
                    'constraint' => 'fk_job_flow_run',
                    'delete' => 'SET_NULL',
                    'update' => 'CASCADE',
                ])
                ->save();
        }

        if ($this->hasTable('flow_run_step') && !$this->foreignKeyExists('job', 'fk_job_flow_step')) {
            $this->table('job')
                ->addForeignKey('flow_step_id', 'flow_run_step', 'id', [
                    'constraint' => 'fk_job_flow_step',
                    'delete' => 'SET_NULL',
                    'update' => 'CASCADE',
                ])
                ->save();
        }
    }

    /**
     * @param string $table Table name
     * @param string $name  Constraint name
     */
    private function foreignKeyExists(string $table, string $name): bool
    {
        $adapter = $this->getAdapter();

        if (!method_exists($adapter, 'getForeignKeys')) {
            return false;
        }

        foreach ($adapter->getForeignKeys($table) as $fk) {
            if (($fk['constraint'] ?? '') === $name) {
                return true;
            }
        }

        return false;
    }
}
