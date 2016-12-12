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

        $table = $this->table('annexes');
        $table
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
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 120,
                'null' => true,
            ])
            ->addColumn('number', 'integer', [
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
            ->addColumn('assistance', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('permit', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('license', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('overtime', 'integer', [
                'default' => 0,
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
                    'worker_id',
                ]
            )
            ->create();

        $table = $this->table('bonus_types');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
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
            ->create();

        $table = $this->table('bonuses');
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
            ->addColumn('bonus_types_id', 'integer', [
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
                    'bonus_types_id',
                ]
            )
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
            ->addColumn('unity_price', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_price', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_uf', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
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

        $table = $this->table('budgets');
        $table
            ->addColumn('building_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('client', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('duration', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('uf_value', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost_uf', 'integer', [
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
            ->addColumn('client_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('address', 'string', [
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

        $table = $this->table('charges');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->create();

        $table = $this->table('chat_group_members');
        $table
            ->addColumn('chat_group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_creator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $table = $this->table('chat_groups');
        $table
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
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
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
            ->addColumn('hours_worked', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('completed_percent', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('installed_items_quantity', 'integer', [
                'default' => null,
                'limit' => 11,
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

        $table = $this->table('configurations');
        $table
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
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('value', 'string', [
                'default' => null,
                'limit' => 200,
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
            ->create();

        $table = $this->table('deals');
        $table
            ->addColumn('worker_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('budget_item_id', 'integer', [
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
                    'budget_item_id',
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

        $table = $this->table('events');
        $table
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
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('start_time', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('end_time', 'datetime', [
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

        $table = $this->table('files');
        $table
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('file_type', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('file_extension', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('size', 'integer', [
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
                'limit' => 11,
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
            ->addColumn('warehouse', 'string', [
                'comment' => 'bodega',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('exit_type', 'string', [
                'comment' => 'forma de salida',
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
            ->addColumn('description', 'string', [
                'comment' => 'Descripción',
                'default' => null,
                'limit' => 255,
                'null' => true,
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
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('delivered_by', 'string', [
                'comment' => 'entregado por',
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
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $table = $this->table('iconstruye_imports');
        $table
            ->addColumn('file_id', 'integer', [
                'default' => null,
                'limit' => 11,
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
            ->create();

        $table = $this->table('payment_statements');
        $table
            ->addColumn('budget_id', 'integer', [
                'comment' => 'identificador de presupuesto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_cost_uf', 'float', [
                'comment' => 'costo total estado pago en uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('overall_progress', 'float', [
                'comment' => 'avance obra estado de pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('contract_value_uf', 'float', [
                'comment' => 'valor del contrato uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('advance_uf', 'float', [
                'comment' => 'anticipo uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('paid_to_date_uf', 'float', [
                'comment' => 'pagado a la fecha uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('advance_present_payent_statement_uf', 'float', [
                'comment' => 'avance presente estado pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('balance_due_uf', 'float', [
                'comment' => 'saldo por pagar',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('discount_retentions_uf', 'float', [
                'comment' => 'descuento por retenciones uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('discount_refund_advances_uf', 'float', [
                'comment' => 'descuento devolucion de anticipo uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('liquid_pay_uf', 'float', [
                'comment' => 'liquido a pagar en uf',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('uf_value_to_date', 'integer', [
                'comment' => 'valor uf a fecha estado de pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total_net', 'integer', [
                'comment' => 'total neto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('tax', 'integer', [
                'comment' => 'iva',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('total', 'integer', [
                'comment' => 'total',
                'default' => null,
                'limit' => 11,
                'null' => false,
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
            ->addColumn('payment_statement_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('overall_progress_percent', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('overall_progress_hours', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('installed_items_quantity', 'integer', [
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
            ->addColumn('user_modified', 'integer', [
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
                    'payment_statement_id',
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
                    'user_modified',
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
            ->addIndex(
                [
                    'budget_id',
                ]
            )
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
            ->addIndex(
                [
                    'group_id',
                ]
            )
            ->create();

        $table = $this->table('workers');
        $table
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('assists')
            ->addForeignKey(
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

        $this->table('bonuses')
            ->addForeignKey(
                'bonus_types_id',
                'bonus_types',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
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

        $this->table('budget_approvals')
            ->addForeignKey(
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

        $this->table('budget_items')
            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('budgets')
            ->addForeignKey(
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

        $this->table('completed_tasks')
            ->addForeignKey(
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

        $this->table('deals')
            ->addForeignKey(
                'budget_item_id',
                'budget_items',
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

        $this->table('guide_entries')
            ->addForeignKey(
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

        $this->table('guide_exits')
            ->addForeignKey(
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

        $this->table('invoices')
            ->addForeignKey(
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

        $this->table('payment_statements')
            ->addForeignKey(
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
            ->update();

        $this->table('progress')
            ->addForeignKey(
                'budget_item_id',
                'budget_items',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'payment_statement_id',
                'payment_statements',
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
                'user_modified',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('purchase_orders')
            ->addForeignKey(
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

        $this->table('rendition_items')
            ->addForeignKey(
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

        $this->table('renditions')
            ->addForeignKey(
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

        $this->table('schedules')
            ->addForeignKey(
                'budget_id',
                'budgets',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('users')
            ->addForeignKey(
                'group_id',
                'groups',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        // execute()
        $count = $this->execute("INSERT INTO `groups` VALUES ('1', 'Administrador', 'Administrador Sistema', '2015-07-29 12:45:11', '2015-09-21 14:33:01', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('2', 'Coordinador Proyectos', 'Coordinador de Proyectos', '2015-09-21 14:33:29', '2015-09-21 14:33:29', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('3', 'Gerente General', 'Gerente General Empresa', '2015-09-21 14:33:51', '2015-09-21 14:33:51', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('4', 'Gerente Finanzas', 'Gerente Finanzas Empresa', '2015-09-21 14:34:15', '2015-09-21 14:34:15', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('5', 'Jefe RRHH', 'Jefe Recursos Humanos', '2015-09-21 14:43:21', '2015-09-21 14:43:21', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('6', 'Visitador', 'Visitador de Obra', '2015-09-21 14:43:35', '2015-09-21 14:43:35', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('7', 'Admin Obra', 'Administrador de Obra', '2015-09-21 14:43:49', '2015-09-21 14:43:49', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('8', 'Asistente RRHH', 'Asistente Recursos Humanos', '2015-09-21 14:44:08', '2015-09-21 14:44:08', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('9', 'Jefe Adquisiciones', 'Jefe Adquisiciones', '2015-10-06 12:44:41', '2015-10-06 12:44:41', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('10', 'Jefe Inventario', 'Jefe Inventario', '2015-10-06 12:44:59', '2015-10-06 12:44:59', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('11', 'Finanzas', 'Finanzas', '2015-10-06 12:45:16', '2015-10-06 12:46:55', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('12', 'Oficina Tecnica', 'Oficina Tecnica', '2015-10-06 12:45:36', '2015-10-06 12:45:36', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('13', 'Bodega', 'Bodega', '2015-10-06 12:46:15', '2015-10-06 12:46:15', '1')");
        $count = $this->execute("INSERT INTO `groups` VALUES ('14', 'Contabilidad', 'Contabilidad', '2015-10-06 12:47:20', '2015-10-06 12:47:20', '1')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('1', null, null, null, 'controllers', '1', '524')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('2', '1', null, null, 'App', '2', '7')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('3', '2', null, null, 'check_group', '3', '4')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('4', '2', null, null, 'redirect_home', '5', '6')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('5', '1', null, null, 'Approvals', '8', '23')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('6', '5', null, null, 'index', '9', '10')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('7', '5', null, null, 'view', '11', '12')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('8', '5', null, null, 'add', '13', '14')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('9', '5', null, null, 'edit', '15', '16')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('10', '5', null, null, 'delete', '17', '18')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('11', '5', null, null, 'check_group', '19', '20')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('12', '5', null, null, 'redirect_home', '21', '22')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('13', '1', null, null, 'Assists', '24', '39')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('14', '13', null, null, 'index', '25', '26')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('15', '13', null, null, 'view', '27', '28')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('16', '13', null, null, 'add', '29', '30')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('17', '13', null, null, 'edit', '31', '32')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('18', '13', null, null, 'delete', '33', '34')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('19', '13', null, null, 'check_group', '35', '36')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('20', '13', null, null, 'redirect_home', '37', '38')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('21', '1', null, null, 'Bonuses', '40', '55')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('22', '21', null, null, 'index', '41', '42')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('23', '21', null, null, 'view', '43', '44')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('24', '21', null, null, 'add', '45', '46')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('25', '21', null, null, 'edit', '47', '48')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('26', '21', null, null, 'delete', '49', '50')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('27', '21', null, null, 'check_group', '51', '52')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('28', '21', null, null, 'redirect_home', '53', '54')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('29', '1', null, null, 'BonusTypes', '56', '71')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('30', '29', null, null, 'index', '57', '58')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('31', '29', null, null, 'view', '59', '60')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('32', '29', null, null, 'add', '61', '62')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('33', '29', null, null, 'edit', '63', '64')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('34', '29', null, null, 'delete', '65', '66')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('35', '29', null, null, 'check_group', '67', '68')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('36', '29', null, null, 'redirect_home', '69', '70')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('37', '1', null, null, 'BudgetApprovals', '72', '87')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('38', '37', null, null, 'index', '73', '74')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('39', '37', null, null, 'view', '75', '76')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('40', '37', null, null, 'add', '77', '78')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('41', '37', null, null, 'edit', '79', '80')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('42', '37', null, null, 'delete', '81', '82')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('43', '37', null, null, 'check_group', '83', '84')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('44', '37', null, null, 'redirect_home', '85', '86')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('45', '1', null, null, 'BudgetItems', '88', '103')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('46', '45', null, null, 'index', '89', '90')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('47', '45', null, null, 'view', '91', '92')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('48', '45', null, null, 'add', '93', '94')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('49', '45', null, null, 'edit', '95', '96')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('50', '45', null, null, 'delete', '97', '98')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('51', '45', null, null, 'check_group', '99', '100')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('52', '45', null, null, 'redirect_home', '101', '102')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('53', '1', null, null, 'Budgets', '104', '121')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('54', '53', null, null, 'index', '105', '106')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('55', '53', null, null, 'view', '107', '108')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('56', '53', null, null, 'add', '109', '110')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('57', '53', null, null, 'edit', '111', '112')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('58', '53', null, null, 'delete', '113', '114')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('59', '53', null, null, 'import_excel', '115', '116')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('60', '53', null, null, 'check_group', '117', '118')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('61', '53', null, null, 'redirect_home', '119', '120')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('62', '1', null, null, 'Buildings', '122', '137')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('63', '62', null, null, 'index', '123', '124')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('64', '62', null, null, 'view', '125', '126')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('65', '62', null, null, 'add', '127', '128')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('66', '62', null, null, 'edit', '129', '130')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('67', '62', null, null, 'delete', '131', '132')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('68', '62', null, null, 'check_group', '133', '134')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('69', '62', null, null, 'redirect_home', '135', '136')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('70', '1', null, null, 'CompletedTasks', '138', '153')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('71', '70', null, null, 'index', '139', '140')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('72', '70', null, null, 'view', '141', '142')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('73', '70', null, null, 'add', '143', '144')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('74', '70', null, null, 'edit', '145', '146')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('75', '70', null, null, 'delete', '147', '148')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('76', '70', null, null, 'check_group', '149', '150')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('77', '70', null, null, 'redirect_home', '151', '152')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('78', '1', null, null, 'Deals', '154', '169')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('79', '78', null, null, 'index', '155', '156')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('80', '78', null, null, 'view', '157', '158')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('81', '78', null, null, 'add', '159', '160')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('82', '78', null, null, 'edit', '161', '162')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('83', '78', null, null, 'delete', '163', '164')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('84', '78', null, null, 'check_group', '165', '166')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('85', '78', null, null, 'redirect_home', '167', '168')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('86', '1', null, null, 'Events', '170', '189')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('87', '86', null, null, 'index', '171', '172')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('88', '86', null, null, 'view', '173', '174')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('89', '86', null, null, 'add', '175', '176')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('90', '86', null, null, 'edit', '177', '178')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('91', '86', null, null, 'delete', '179', '180')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('92', '86', null, null, 'viewEvents', '181', '182')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('93', '86', null, null, 'editStatus', '183', '184')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('94', '86', null, null, 'check_group', '185', '186')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('95', '86', null, null, 'redirect_home', '187', '188')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('96', '1', null, null, 'Files', '190', '205')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('97', '96', null, null, 'index', '191', '192')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('98', '96', null, null, 'view', '193', '194')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('99', '96', null, null, 'add', '195', '196')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('100', '96', null, null, 'edit', '197', '198')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('101', '96', null, null, 'delete', '199', '200')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('102', '96', null, null, 'check_group', '201', '202')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('103', '96', null, null, 'redirect_home', '203', '204')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('104', '1', null, null, 'Groups', '206', '225')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('105', '104', null, null, 'index', '207', '208')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('106', '104', null, null, 'view', '209', '210')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('107', '104', null, null, 'add', '211', '212')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('108', '104', null, null, 'edit', '213', '214')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('109', '104', null, null, 'delete', '215', '216')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('110', '104', null, null, 'check_group', '217', '218')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('111', '104', null, null, 'redirect_home', '219', '220')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('112', '1', null, null, 'GuideEntries', '226', '241')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('113', '112', null, null, 'index', '227', '228')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('114', '112', null, null, 'view', '229', '230')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('115', '112', null, null, 'add', '231', '232')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('116', '112', null, null, 'edit', '233', '234')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('117', '112', null, null, 'delete', '235', '236')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('118', '112', null, null, 'check_group', '237', '238')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('119', '112', null, null, 'redirect_home', '239', '240')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('120', '1', null, null, 'GuideExits', '242', '257')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('121', '120', null, null, 'index', '243', '244')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('122', '120', null, null, 'view', '245', '246')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('123', '120', null, null, 'add', '247', '248')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('124', '120', null, null, 'edit', '249', '250')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('125', '120', null, null, 'delete', '251', '252')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('126', '120', null, null, 'check_group', '253', '254')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('127', '120', null, null, 'redirect_home', '255', '256')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('128', '1', null, null, 'IconstruyeImports', '258', '273')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('129', '128', null, null, 'index', '259', '260')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('130', '128', null, null, 'view', '261', '262')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('131', '128', null, null, 'add', '263', '264')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('132', '128', null, null, 'edit', '265', '266')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('133', '128', null, null, 'delete', '267', '268')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('134', '128', null, null, 'check_group', '269', '270')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('135', '128', null, null, 'redirect_home', '271', '272')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('136', '1', null, null, 'Invoices', '274', '289')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('137', '136', null, null, 'index', '275', '276')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('138', '136', null, null, 'view', '277', '278')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('139', '136', null, null, 'add', '279', '280')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('140', '136', null, null, 'edit', '281', '282')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('141', '136', null, null, 'delete', '283', '284')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('142', '136', null, null, 'check_group', '285', '286')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('143', '136', null, null, 'redirect_home', '287', '288')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('144', '1', null, null, 'Observations', '290', '305')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('145', '144', null, null, 'index', '291', '292')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('146', '144', null, null, 'view', '293', '294')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('147', '144', null, null, 'add', '295', '296')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('148', '144', null, null, 'edit', '297', '298')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('149', '144', null, null, 'delete', '299', '300')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('150', '144', null, null, 'check_group', '301', '302')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('151', '144', null, null, 'redirect_home', '303', '304')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('152', '1', null, null, 'Pages', '306', '313')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('153', '152', null, null, 'display', '307', '308')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('154', '152', null, null, 'check_group', '309', '310')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('155', '152', null, null, 'redirect_home', '311', '312')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('156', '1', null, null, 'PaymentStatements', '314', '329')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('157', '156', null, null, 'index', '315', '316')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('158', '156', null, null, 'view', '317', '318')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('159', '156', null, null, 'add', '319', '320')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('160', '156', null, null, 'edit', '321', '322')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('161', '156', null, null, 'delete', '323', '324')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('162', '156', null, null, 'check_group', '325', '326')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('163', '156', null, null, 'redirect_home', '327', '328')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('164', '1', null, null, 'Progress', '330', '345')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('165', '164', null, null, 'index', '331', '332')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('166', '164', null, null, 'view', '333', '334')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('167', '164', null, null, 'add', '335', '336')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('168', '164', null, null, 'edit', '337', '338')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('169', '164', null, null, 'delete', '339', '340')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('170', '164', null, null, 'check_group', '341', '342')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('171', '164', null, null, 'redirect_home', '343', '344')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('172', '1', null, null, 'PurchaseOrders', '346', '361')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('173', '172', null, null, 'index', '347', '348')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('174', '172', null, null, 'view', '349', '350')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('175', '172', null, null, 'add', '351', '352')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('176', '172', null, null, 'edit', '353', '354')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('177', '172', null, null, 'delete', '355', '356')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('178', '172', null, null, 'check_group', '357', '358')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('179', '172', null, null, 'redirect_home', '359', '360')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('180', '1', null, null, 'RenditionItems', '362', '377')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('181', '180', null, null, 'index', '363', '364')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('182', '180', null, null, 'view', '365', '366')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('183', '180', null, null, 'add', '367', '368')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('184', '180', null, null, 'edit', '369', '370')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('185', '180', null, null, 'delete', '371', '372')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('186', '180', null, null, 'check_group', '373', '374')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('187', '180', null, null, 'redirect_home', '375', '376')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('188', '1', null, null, 'Renditions', '378', '393')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('189', '188', null, null, 'index', '379', '380')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('190', '188', null, null, 'view', '381', '382')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('191', '188', null, null, 'add', '383', '384')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('192', '188', null, null, 'edit', '385', '386')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('193', '188', null, null, 'delete', '387', '388')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('194', '188', null, null, 'check_group', '389', '390')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('195', '188', null, null, 'redirect_home', '391', '392')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('196', '1', null, null, 'Schedules', '394', '409')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('197', '196', null, null, 'index', '395', '396')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('198', '196', null, null, 'view', '397', '398')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('199', '196', null, null, 'add', '399', '400')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('200', '196', null, null, 'edit', '401', '402')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('201', '196', null, null, 'delete', '403', '404')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('202', '196', null, null, 'check_group', '405', '406')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('203', '196', null, null, 'redirect_home', '407', '408')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('204', '1', null, null, 'Units', '410', '425')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('205', '204', null, null, 'index', '411', '412')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('206', '204', null, null, 'view', '413', '414')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('207', '204', null, null, 'add', '415', '416')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('208', '204', null, null, 'edit', '417', '418')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('209', '204', null, null, 'delete', '419', '420')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('210', '204', null, null, 'check_group', '421', '422')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('211', '204', null, null, 'redirect_home', '423', '424')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('212', '1', null, null, 'Users', '426', '479')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('213', '212', null, null, 'home', '427', '428')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('214', '212', null, null, 'login', '429', '430')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('215', '212', null, null, 'logout', '431', '432')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('216', '212', null, null, 'index', '433', '434')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('217', '212', null, null, 'view', '435', '436')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('218', '212', null, null, 'add', '437', '438')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('219', '212', null, null, 'edit', '439', '440')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('220', '212', null, null, 'editUser', '441', '442')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('221', '212', null, null, 'delete', '443', '444')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('222', '212', null, null, 'updatePassword', '445', '446')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('223', '212', null, null, 'updatePasswordAdmin', '447', '448')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('224', '212', null, null, 'listEditStatus', '449', '450')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('225', '212', null, null, 'editStatus', '451', '452')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('226', '212', null, null, 'forgottenPassword', '453', '454')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('227', '212', null, null, 'restorePassword', '455', '456')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('228', '212', null, null, 'my_account', '457', '458')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('229', '212', null, null, 'salaries', '459', '460')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('230', '212', null, null, 'holidays', '461', '462')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('231', '212', null, null, 'certificates', '463', '464')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('232', '212', null, null, 'accountabilities', '465', '466')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('233', '212', null, null, 'phones', '467', '468')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('234', '212', null, null, 'birthday', '469', '470')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('235', '212', null, null, 'chat', '471', '472')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('236', '212', null, null, 'check_group', '473', '474')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('237', '212', null, null, 'redirect_home', '475', '476')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('238', '212', null, null, 'cell', '477', '478')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('239', '1', null, null, 'Workers', '480', '495')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('240', '239', null, null, 'index', '481', '482')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('241', '239', null, null, 'view', '483', '484')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('242', '239', null, null, 'add', '485', '486')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('243', '239', null, null, 'edit', '487', '488')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('244', '239', null, null, 'delete', '489', '490')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('245', '239', null, null, 'check_group', '491', '492')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('246', '239', null, null, 'redirect_home', '493', '494')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('247', '1', null, null, 'Acl', '496', '497')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('248', '1', null, null, 'Bake', '498', '499')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('249', '1', null, null, 'Bootstrap3', '500', '501')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('250', '1', null, null, 'DebugKit', '502', '517')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('251', '250', null, null, 'Panels', '503', '508')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('252', '251', null, null, 'index', '504', '505')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('253', '251', null, null, 'view', '506', '507')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('254', '250', null, null, 'Requests', '509', '512')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('255', '254', null, null, 'view', '510', '511')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('256', '250', null, null, 'Toolbar', '513', '516')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('257', '256', null, null, 'clearCache', '514', '515')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('258', '1', null, null, 'Migrations', '518', '519')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('259', '1', null, null, 'Proffer', '520', '521')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('260', '1', null, null, 'Search', '522', '523')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('266', '104', null, null, 'activate', '221', '222')");
        $count = $this->execute("INSERT INTO `acos` VALUES ('267', '104', null, null, 'deactivate', '223', '224')");
    }

    public function down()
    {
        $this->table('assists')
            ->dropForeignKey(
                'budget_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->dropForeignKey(
                'worker_id'
            )
            ->update();

        $this->table('bonuses')
            ->dropForeignKey(
                'bonus_types_id'
            )
            ->dropForeignKey(
                'budget_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->dropForeignKey(
                'worker_id'
            )
            ->update();

        $this->table('budget_approvals')
            ->dropForeignKey(
                'budget_id'
            )
            ->dropForeignKey(
                'user_id'
            )
            ->update();

        $this->table('budget_items')
            ->dropForeignKey(
                'budget_id'
            )
            ->update();

        $this->table('budgets')
            ->dropForeignKey(
                'building_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->update();

        $this->table('completed_tasks')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'schedule_id'
            )
            ->dropForeignKey(
                'worker_id'
            )
            ->update();

        $this->table('deals')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->dropForeignKey(
                'worker_id'
            )
            ->update();

        $this->table('guide_entries')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'iconstruye_import_id'
            )
            ->update();

        $this->table('guide_exits')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'iconstruye_import_id'
            )
            ->update();

        $this->table('invoices')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'iconstruye_import_id'
            )
            ->update();

        $this->table('payment_statements')
            ->dropForeignKey(
                'budget_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->update();

        $this->table('progress')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'payment_statement_id'
            )
            ->dropForeignKey(
                'schedule_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->dropForeignKey(
                'user_modified'
            )
            ->update();

        $this->table('purchase_orders')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'iconstruye_import_id'
            )
            ->update();

        $this->table('rendition_items')
            ->dropForeignKey(
                'budget_item_id'
            )
            ->dropForeignKey(
                'rendition_id'
            )
            ->update();

        $this->table('renditions')
            ->dropForeignKey(
                'budget_id'
            )
            ->dropForeignKey(
                'user_created_id'
            )
            ->dropForeignKey(
                'user_modified_id'
            )
            ->update();

        $this->table('schedules')
            ->dropForeignKey(
                'budget_id'
            )
            ->update();

        $this->table('users')
            ->dropForeignKey(
                'group_id'
            )
            ->update();

        $this->dropTable('acos');
        $this->dropTable('acos_groups');
        $this->dropTable('annexes');
        $this->dropTable('approvals');
        $this->dropTable('aros');
        $this->dropTable('aros_acos');
        $this->dropTable('assists');
        $this->dropTable('bonus_types');
        $this->dropTable('bonuses');
        $this->dropTable('budget_approvals');
        $this->dropTable('budget_items');
        $this->dropTable('budget_items_schedules');
        $this->dropTable('budgets');
        $this->dropTable('buildings');
        $this->dropTable('charges');
        $this->dropTable('chat_group_members');
        $this->dropTable('chat_groups');
        $this->dropTable('completed_tasks');
        $this->dropTable('configurations');
        $this->dropTable('deals');
        $this->dropTable('events');
        $this->dropTable('files');
        $this->dropTable('groups');
        $this->dropTable('guide_entries');
        $this->dropTable('guide_exits');
        $this->dropTable('histories');
        $this->dropTable('iconstruye_imports');
        $this->dropTable('invoices');
        $this->dropTable('observations');
        $this->dropTable('payment_statements');
        $this->dropTable('progress');
        $this->dropTable('purchase_orders');
        $this->dropTable('rendition_items');
        $this->dropTable('renditions');
        $this->dropTable('schedules');
        $this->dropTable('units');
        $this->dropTable('users');
        $this->dropTable('workers');
    }
}
