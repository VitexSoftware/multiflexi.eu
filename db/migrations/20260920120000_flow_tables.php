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
 * Store Node-RED-designed automation graphs for MultiFlexi eventor execution.
 *
 * flow              — logical automation (name, company scope, current version)
 * flow_version      — immutable Deploy snapshot (nodes/wires live here)
 * flow_node         — nodes in a version
 * flow_wire         — wires with ports (switch/catch ready)
 * flow_run          — one execution instance pinned to a flow_version
 * flow_run_step     — per-node step state within a run
 */
final class FlowTables extends AbstractMigration
{
    public function change(): void
    {
        if ($this->hasTable('flow')) {
            return;
        }

        $databaseType = $this->getAdapter()->getOption('adapter');
        $unsigned = ($databaseType === 'mysql') ? ['signed' => false] : [];

        $flow = $this->table('flow', ['comment' => 'Automation graph designed in Node-RED, executed by eventor']);
        $flow
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false, 'comment' => 'Human-readable flow name'])
            ->addColumn('company_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'Tenant scope; null = global/template',
            ], $unsigned))
            ->addColumn('nodered_tab_id', 'string', [
                'limit' => 64,
                'null' => true,
                'default' => null,
                'comment' => 'Node-RED tab/flow id used as Deploy identity',
            ])
            ->addColumn('enabled', 'boolean', ['default' => true, 'null' => false])
            ->addColumn('current_version_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'FK to flow_version.id of the active Deploy',
            ], $unsigned))
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addColumn('modified', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addIndex(['nodered_tab_id'], ['name' => 'idx_flow_nodered_tab'])
            ->addIndex(['company_id'], ['name' => 'idx_flow_company'])
            ->addIndex(['enabled'], ['name' => 'idx_flow_enabled'])
            ->create();

        if ($this->hasTable('company')) {
            $flow
                ->addForeignKey('company_id', 'company', 'id', [
                    'constraint' => 'fk_flow_company',
                    'delete' => 'SET_NULL',
                    'update' => 'CASCADE',
                ])
                ->save();
        }

        $version = $this->table('flow_version', ['comment' => 'Immutable snapshot of a flow graph from one Deploy']);
        $version
            ->addColumn('flow_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('version', 'integer', array_merge(['null' => false, 'comment' => 'Monotonic version number per flow'], $unsigned))
            ->addColumn('checksum', 'string', ['limit' => 64, 'null' => false, 'comment' => 'sha256 of nodes+wires payload'])
            ->addColumn('raw_json', 'text', ['null' => true, 'default' => null, 'comment' => 'Optional full Node-RED export for round-trip'])
            ->addColumn('interpreter_capability', 'string', [
                'limit' => 32,
                'null' => false,
                'default' => '1.0.0',
                'comment' => 'Minimum eventor interpreter capability required',
            ])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addIndex(['flow_id', 'version'], ['unique' => true, 'name' => 'uq_flow_version'])
            ->addForeignKey('flow_id', 'flow', 'id', [
                'constraint' => 'fk_flow_version_flow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();

        $node = $this->table('flow_node', ['comment' => 'Node within an immutable flow_version']);
        $node
            ->addColumn('flow_version_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('nr_node_id', 'string', ['limit' => 64, 'null' => false, 'comment' => 'Node-RED node z/id'])
            ->addColumn('type', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('config', 'text', ['null' => true, 'default' => null, 'comment' => 'JSON node configuration'])
            ->addColumn('x', 'integer', ['null' => true, 'default' => null])
            ->addColumn('y', 'integer', ['null' => true, 'default' => null])
            ->addIndex(['flow_version_id', 'nr_node_id'], ['unique' => true, 'name' => 'uq_flow_node_nr'])
            ->addIndex(['flow_version_id', 'type'], ['name' => 'idx_flow_node_type'])
            ->addForeignKey('flow_version_id', 'flow_version', 'id', [
                'constraint' => 'fk_flow_node_version',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();

        $wire = $this->table('flow_wire', ['comment' => 'Wire between nodes; ports support switch/catch']);
        $wire
            ->addColumn('flow_version_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('from_nr_node_id', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('from_port', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('to_nr_node_id', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('to_port', 'integer', ['null' => false, 'default' => 0])
            ->addIndex(['flow_version_id'], ['name' => 'idx_flow_wire_version'])
            ->addIndex(['flow_version_id', 'from_nr_node_id', 'from_port'], ['name' => 'idx_flow_wire_from'])
            ->addForeignKey('flow_version_id', 'flow_version', 'id', [
                'constraint' => 'fk_flow_wire_version',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();

        $run = $this->table('flow_run', ['comment' => 'One execution of a pinned flow_version']);
        $run
            ->addColumn('flow_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('flow_version_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('company_id', 'integer', array_merge([
                'null' => true,
                'default' => null,
                'comment' => 'Company context for this run',
            ], $unsigned))
            ->addColumn('idempotency_key', 'string', [
                'limit' => 191,
                'null' => true,
                'default' => null,
                'comment' => 'Dedup key e.g. change:{source}:{inversion}',
            ])
            ->addColumn('trigger_json', 'text', ['null' => true, 'default' => null, 'comment' => 'JSON trigger payload'])
            ->addColumn('status', 'string', [
                'limit' => 32,
                'null' => false,
                'default' => 'running',
                'comment' => 'running | completed | failed | cancelling | cancelled',
            ])
            ->addColumn('started', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('finished', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['flow_id', 'idempotency_key'], ['unique' => true, 'name' => 'uq_flow_run_idem'])
            ->addIndex(['status'], ['name' => 'idx_flow_run_status'])
            ->addIndex(['flow_version_id'], ['name' => 'idx_flow_run_version'])
            ->addForeignKey('flow_id', 'flow', 'id', [
                'constraint' => 'fk_flow_run_flow',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('flow_version_id', 'flow_version', 'id', [
                'constraint' => 'fk_flow_run_version',
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
            ])
            ->create();

        $step = $this->table('flow_run_step', ['comment' => 'Per-node step within a flow_run']);
        $step
            ->addColumn('flow_run_id', 'integer', array_merge(['null' => false], $unsigned))
            ->addColumn('nr_node_id', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('status', 'string', [
                'limit' => 32,
                'null' => false,
                'default' => 'pending',
                'comment' => 'pending | ready | waiting_job | waiting_delay | completed | failed | skipped',
            ])
            ->addColumn('job_id', 'integer', array_merge(['null' => true, 'default' => null], $unsigned))
            ->addColumn('input_json', 'text', ['null' => true, 'default' => null, 'comment' => 'FlowMsg envelope JSON'])
            ->addColumn('output_json', 'text', ['null' => true, 'default' => null, 'comment' => 'FlowMsg envelope JSON'])
            ->addColumn('due_at', 'datetime', ['null' => true, 'default' => null, 'comment' => 'For delay steps'])
            ->addColumn('error', 'text', ['null' => true, 'default' => null])
            ->addColumn('output_port', 'integer', ['null' => true, 'default' => null, 'comment' => 'Chosen output port (switch)'])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addColumn('modified', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addIndex(['flow_run_id', 'status'], ['name' => 'idx_flow_step_status'])
            ->addIndex(['job_id'], ['name' => 'idx_flow_step_job'])
            ->addForeignKey('flow_run_id', 'flow_run', 'id', [
                'constraint' => 'fk_flow_step_run',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
