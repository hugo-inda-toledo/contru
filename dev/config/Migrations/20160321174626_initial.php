<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('acos');
        $table
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $table = $this->table('acos_groups');
        $table
            ->addColumn('aco_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('permission', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('approvals');
        $table
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('action', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('approve', 'integer', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('reject', 'integer', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('model_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('records_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('aros');
        $table
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('alias', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $table = $this->table('aros_acos');
        $table
            ->addColumn('aro_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('aco_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('_create', 'string', [
                'default' => 0,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_read', 'string', [
                'default' => 0,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_update', 'string', [
                'default' => 0,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_delete', 'string', [
                'default' => 0,
                'limit' => 2,
                'null' => false,
            ])
            ->addIndex(
                [
                    'aro_id',
                    'aco_id',
                ],
                ['unique' => true]
            )
            ->create();

        $table = $this->table('assist_types');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('assists');
        $table
            ->addColumn('budget_id', 'integer', [
                'comment' => 'Identificador presupuesto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('worker_id', 'integer', [
                'comment' => 'Identificador trabajador',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('assistance_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('overtime', 'integer', [
                'default' => 0,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('delay', 'integer', [
                'default' => 0,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->addIndex(
                [
                    'worker_id',
                ]
            )
            ->create();

        $table = $this->table('assists_assist_types');
        $table
            ->addColumn('assist_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('assist_type_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('hours', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('bonus_details');
        $table
            ->addColumn('bonus_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('percentage', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->create();

        $table = $this->table('bonuses');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('state', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('worker_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->addIndex(
                [
                    'worker_id',
                ]
            )
            ->create();

        $table = $this->table('budget_approvals');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_state_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('approve', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('reject', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $table = $this->table('budget_items');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('item', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('unit_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unity_price', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('total_price', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('total_uf', 'float', [
                'comment' => 'total partida en moneda',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('comments', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('disabled', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('extra', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('utilities', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('general_cost', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('material_contribution', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('retention', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('advance', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('target_value', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->create();

        $table = $this->table('budget_items_payment_statements');
        $table
            ->addColumn('payment_statement_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('overall_progress', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('previous_progress', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('progress', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('overall_progress_value', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('previous_progress_value', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('progress_value', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('budget_items_schedules');
        $table
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('schedule_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $table = $this->table('budget_states');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('budgets');
        $table
            ->addColumn('building_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('duration', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('comments', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('file', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('advances', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('retentions', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('utilities', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('general_costs', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'building_id',
                ]
            )
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->create();

        $table = $this->table('buildings');
        $table
            ->addColumn('address', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('client', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('softland_id', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('omit', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('active', 'boolean', [
                'default' => 1,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('buildings_users');
        $table
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('building_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('charges');
        $table
            ->addColumn('softland_id', 'string', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('max_amount_deals', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('max_amount_bonus', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('completed_tasks');
        $table
            ->addColumn('schedule_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('worker_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_percentage', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'schedule_id',
                ]
            )
            ->addIndex(
                [
                    'worker_id',
                ]
            )
            ->create();

        $table = $this->table('currencies');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('amount', 'decimal', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('currencies_values');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('currency_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('value', 'float', [
                'default' => null,
                'limit' => 12,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('deal_details');
        $table
            ->addColumn('deal_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('percentage', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->create();

        $table = $this->table('deals');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('state', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('worker_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('start_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('end_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->addIndex(
                [
                    'worker_id',
                ]
            )
            ->create();

        $table = $this->table('groups');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'default' => 1,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('guide_entries');
        $table
            ->addColumn('iconstruye_import_id', 'integer', [
                'comment' => 'Identificador registro importacion iconstruye',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'comment' => 'Identificador budget item (partida presupuesto)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('warehouse', 'string', [
                'comment' => 'bodega',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('entry_date', 'datetime', [
                'comment' => 'fecha ingreso',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('entry_type', 'string', [
                'comment' => 'forma de ingreso',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('document_number', 'integer', [
                'comment' => 'numero de documento',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('document_date', 'date', [
                'comment' => 'fecha documento',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('oc_number', 'string', [
                'comment' => 'Número de orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('oc_date', 'datetime', [
                'comment' => 'Fecha orden de compra',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('cost_center', 'string', [
                'comment' => 'Centro de costo orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('product_code', 'string', [
                'comment' => 'Código producto orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('product_name', 'string', [
                'comment' => 'Nombre producto orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'comment' => 'Cantidad producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_price', 'integer', [
                'comment' => 'Precio unitario producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_type', 'string', [
                'comment' => 'Tipo unidad producto orden de compra',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('product_total', 'integer', [
                'comment' => 'Total producto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('observations', 'string', [
                'comment' => 'observaciones',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('sub_total', 'integer', [
                'comment' => 'Sub Total de orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('discount', 'integer', [
                'comment' => 'Descuento orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('tax', 'integer', [
                'comment' => 'Impuesto (IVA) orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_net', 'integer', [
                'comment' => 'Total neto orden de compra (sin iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost', 'integer', [
                'comment' => 'Costo total orden de compra (con iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost_uf', 'float', [
                'comment' => 'Total costo en UF',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('received_by', 'string', [
                'comment' => 'ingresado por',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Fecha creación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Fecha modificación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'iconstruye_import_id',
                ]
            )
            ->create();

        $table = $this->table('guide_exits');
        $table
            ->addColumn('iconstruye_import_id', 'integer', [
                'comment' => 'Identificador registro importacion iconstruye',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'comment' => 'Identificador budget item (partida presupuesto)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('uid', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('voucher', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('date_system', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('product_code', 'string', [
                'comment' => 'Código producto orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('product_name', 'string', [
                'comment' => 'Nombre producto orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'comment' => 'Cantidad producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_price', 'integer', [
                'comment' => 'Precio unitario producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_type', 'string', [
                'comment' => 'Tipo unidad producto orden de compra',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('product_total', 'integer', [
                'comment' => 'Total producto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('json', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('observations', 'string', [
                'comment' => 'observaciones',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Fecha creación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Fecha modificación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'iconstruye_import_id',
                ]
            )
            ->create();

        $table = $this->table('histories');
        $table
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('method', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('text', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('data', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('iconstruye_imports');
        $table
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('file_name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('transaction_lines', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_uploader_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('file', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $table = $this->table('invoices');
        $table
            ->addColumn('iconstruye_import_id', 'integer', [
                'comment' => 'Identificador registro importacion iconstruye',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'comment' => 'Identificador budget item (partida presupuesto)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('invoice_number', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('invoice_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('oc_number', 'string', [
                'comment' => 'Número de orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('oc_date', 'datetime', [
                'comment' => 'Fecha orden de compra',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('product_code', 'string', [
                'comment' => 'Código producto orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('product_name', 'string', [
                'comment' => 'Nombre producto orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'comment' => 'Cantidad producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_price', 'integer', [
                'comment' => 'Precio unitario producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_type', 'string', [
                'comment' => 'Tipo unidad producto orden de compra',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('product_total', 'integer', [
                'comment' => 'Total Producto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('sub_total', 'integer', [
                'comment' => 'Sub Total de orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('discount', 'integer', [
                'comment' => 'Descuento orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('tax', 'integer', [
                'comment' => 'Impuesto (IVA) orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('exempt', 'integer', [
                'comment' => 'exento',
                'default' => 0,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_net', 'integer', [
                'comment' => 'Total neto orden de compra (sin iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost', 'integer', [
                'comment' => 'Costo total orden de compra (con iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Fecha creación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Fecha modificación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'iconstruye_import_id',
                ]
            )
            ->create();

        $table = $this->table('observations');
        $table
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('action', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('observation', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('model_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->create();

        $table = $this->table('payment_statement_approvals');
        $table
            ->addColumn('payment_statement_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('payment_statement_state_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('payment_statement_states');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('payment_statements');
        $table
            ->addColumn('budget_id', 'integer', [
                'comment' => 'identificador de presupuesto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('version_number', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('payment_statement_parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'fecha creacion',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'fecha modificacion',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'comment' => 'identificador usuario creador',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'comment' => 'identificador usuario modificador',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('payment_statement_state_id', 'integer', [
                'default' => 2,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('gloss', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('presentation_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('billing_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('estimation_pay_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('total_direct_cost', 'float', [
                'default' => 0,
                'limit' => 23,
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('total_cost', 'float', [
                'comment' => 'costo total estado pago en uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('overall_progress', 'float', [
                'comment' => 'avance obra estado de pago',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('contract_value', 'float', [
                'comment' => 'valor del contrato uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('paid_to_date', 'float', [
                'comment' => 'pagado a la fecha uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('progress_present_payment_statement', 'float', [
                'comment' => 'avance presente estado pago',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('balance_due', 'float', [
                'comment' => 'saldo por pagar',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('discount_retentions', 'float', [
                'comment' => 'descuento por retenciones uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('discount_advances', 'float', [
                'comment' => 'descuento devolucion de anticipo uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('liquid_pay', 'float', [
                'comment' => 'liquido a pagar en uf',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('currency_value_to_date', 'float', [
                'comment' => 'valor uf a fecha estado de pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_net', 'integer', [
                'comment' => 'total neto',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('tax', 'integer', [
                'comment' => 'iva',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('total', 'integer', [
                'comment' => 'total',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->create();

        $table = $this->table('progress');
        $table
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('schedule_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('overall_progress_percent', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('proyected_progress_percent', 'float', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('worked_items_quantity', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'schedule_id',
                ]
            )
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->addIndex(
                [
                    'user_modified_id',
                ]
            )
            ->create();

        $table = $this->table('purchase_orders');
        $table
            ->addColumn('iconstruye_import_id', 'integer', [
                'comment' => 'Identificador registro importacion iconstruye',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'comment' => 'Identificador budget item (partida presupuesto)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('oc_number', 'string', [
                'comment' => 'Número de orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('oc_date', 'datetime', [
                'comment' => 'Fecha orden de compra',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('cost_center', 'string', [
                'comment' => 'Centro de costo orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('product_code', 'string', [
                'comment' => 'Código producto orden de compra',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('product_name', 'string', [
                'comment' => 'Nombre producto orden de compra',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'comment' => 'Cantidad producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_price', 'integer', [
                'comment' => 'Precio unitario producto orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_type', 'string', [
                'comment' => 'Tipo unidad producto orden de compra',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('product_total', 'integer', [
                'comment' => 'Total Producto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('sub_total', 'integer', [
                'comment' => 'Sub Total de orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('discount', 'integer', [
                'comment' => 'Descuento orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('tax', 'integer', [
                'comment' => 'Impuesto (IVA) orden de compra',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_net', 'integer', [
                'comment' => 'Total neto orden de compra (sin iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost', 'integer', [
                'comment' => 'Costo total orden de compra (con iva)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost_uf', 'float', [
                'comment' => 'Total costo en UF',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Fecha creación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Fecha modificación',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'iconstruye_import_id',
                ]
            )
            ->create();

        $table = $this->table('rendition_items');
        $table
            ->addColumn('rendition_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('product_name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('product_total', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('unit_price', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('quantity', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_item_id',
                ]
            )
            ->addIndex(
                [
                    'rendition_id',
                ]
            )
            ->create();

        $table = $this->table('renditions');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_items', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->addIndex(
                [
                    'user_created_id',
                ]
            )
            ->addIndex(
                [
                    'user_modified_id',
                ]
            )
            ->create();

        $table = $this->table('salary_reports');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('worker_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('assistance_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('total_taxable', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_not_taxable', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_assets', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('travel_expenses', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('other_discounts', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_discounts', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('liquid_to_pay', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('schedules');
        $table
            ->addColumn('budget_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('holidays_week_quantity', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_days', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('start_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('finish_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_created_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_modified_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('progress_approved', 'boolean', [
                'default' => 0,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'budget_id',
                ]
            )
            ->create();

        $table = $this->table('subcontracts');
        $table
            ->addColumn('budget_item_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('iconstruye_import_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('rut', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('subcontract_work_number', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('unit_type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('currency', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('currency_rate', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('amount', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('price', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('total', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('partial_description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('partial_amount', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('partial_total', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('balance_due', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('payment_statement_total', 'float', [
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('json', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('units');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 25,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('users');
        $table
            ->addColumn('group_id', 'integer', [
                'comment' => 'identificador grupo',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'comment' => 'email para inicio sesion y recuperacion password',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('first_name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('lastname_f', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('lastname_m', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('celphone', 'string', [
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('address', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('active', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('user_creator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_modifier_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('last_login', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'group_id',
                ]
            )
            ->create();

        $table = $this->table('workers');
        $table
            ->addColumn('softland_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('assists')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_created_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'worker_id',
                'workers',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('bonuses')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_created_id',
                'bonuses',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'worker_id',
                'workers',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('budget_approvals')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('budget_items')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('budgets')            ->addForeignKey(
                'building_id',
                'buildings',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_created_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('completed_tasks')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'schedule_id',
                'schedules',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'worker_id',
                'workers',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('deals')            ->addForeignKey(
                'user_created_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'worker_id',
                'workers',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('guide_entries')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'iconstruye_import_id',
                'iconstruye_imports',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('guide_exits')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'iconstruye_import_id',
                'iconstruye_imports',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('invoices')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'iconstruye_import_id',
                'iconstruye_imports',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('progress')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'schedule_id',
                'schedules',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_created_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_modified_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('purchase_orders')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'iconstruye_import_id',
                'iconstruye_imports',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('rendition_items')            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'rendition_id',
                'renditions',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('renditions')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_created_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_modified_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('schedules')            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('users')            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

    }

    public function down()
    {
        $this->table('assists')
            ->dropForeignKey(
                'budget_id'            )            ->dropForeignKey(
                'user_created_id'            )            ->dropForeignKey(
                'worker_id'            );
        $this->table('bonuses')
            ->dropForeignKey(
                'budget_id'            )            ->dropForeignKey(
                'user_created_id'            )            ->dropForeignKey(
                'worker_id'            );
        $this->table('budget_approvals')
            ->dropForeignKey(
                'budget_id'            )            ->dropForeignKey(
                'user_id'            );
        $this->table('budget_items')
            ->dropForeignKey(
                'budget_id'            );
        $this->table('budgets')
            ->dropForeignKey(
                'building_id'            )            ->dropForeignKey(
                'user_created_id'            );
        $this->table('completed_tasks')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'schedule_id'            )            ->dropForeignKey(
                'worker_id'            );
        $this->table('deals')
            ->dropForeignKey(
                'user_created_id'            )            ->dropForeignKey(
                'worker_id'            );
        $this->table('guide_entries')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'iconstruye_import_id'            );
        $this->table('guide_exits')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'iconstruye_import_id'            );
        $this->table('invoices')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'iconstruye_import_id'            );
        $this->table('progress')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'schedule_id'            )            ->dropForeignKey(
                'user_created_id'            )            ->dropForeignKey(
                'user_modified_id'            );
        $this->table('purchase_orders')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'iconstruye_import_id'            );
        $this->table('rendition_items')
            ->dropForeignKey(
                'budget_item_id'            )            ->dropForeignKey(
                'rendition_id'            );
        $this->table('renditions')
            ->dropForeignKey(
                'budget_id'            )            ->dropForeignKey(
                'user_created_id'            )            ->dropForeignKey(
                'user_modified_id'            );
        $this->table('schedules')
            ->dropForeignKey(
                'budget_id'            );
        $this->table('users')
            ->dropForeignKey(
                'group_id'            );
        $this->dropTable('acos');
        $this->dropTable('acos_groups');
        $this->dropTable('approvals');
        $this->dropTable('aros');
        $this->dropTable('aros_acos');
        $this->dropTable('assist_types');
        $this->dropTable('assists');
        $this->dropTable('assists_assist_types');
        $this->dropTable('bonus_details');
        $this->dropTable('bonuses');
        $this->dropTable('budget_approvals');
        $this->dropTable('budget_items');
        $this->dropTable('budget_items_payment_statements');
        $this->dropTable('budget_items_schedules');
        $this->dropTable('budget_states');
        $this->dropTable('budgets');
        $this->dropTable('buildings');
        $this->dropTable('buildings_users');
        $this->dropTable('charges');
        $this->dropTable('completed_tasks');
        $this->dropTable('currencies');
        $this->dropTable('currencies_values');
        $this->dropTable('deal_details');
        $this->dropTable('deals');
        $this->dropTable('groups');
        $this->dropTable('guide_entries');
        $this->dropTable('guide_exits');
        $this->dropTable('histories');
        $this->dropTable('iconstruye_imports');
        $this->dropTable('invoices');
        $this->dropTable('observations');
        $this->dropTable('payment_statement_approvals');
        $this->dropTable('payment_statement_states');
        $this->dropTable('payment_statements');
        $this->dropTable('progress');
        $this->dropTable('purchase_orders');
        $this->dropTable('rendition_items');
        $this->dropTable('renditions');
        $this->dropTable('salary_reports');
        $this->dropTable('schedules');
        $this->dropTable('subcontracts');
        $this->dropTable('units');
        $this->dropTable('users');
        $this->dropTable('workers');
    }
}
