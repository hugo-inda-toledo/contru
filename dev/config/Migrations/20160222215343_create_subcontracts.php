<?php
use Migrations\AbstractMigration;

class CreateSubcontracts extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('guide_exits');
        $table->removeColumn('json');
        $table->addColumn('json', 'text', [
            'default' => null,
            'null' => false,
            'after' => 'product_total'
        ]);
        $table->update();
        $table = $this->table('iconstruye_imports');
        $table->addColumn('type', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 50,
            'after' => 'id'
        ]);
        $table->update();
        $table = $this->table('subcontracts');
        $table->addColumn('budget_item_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('iconstruye_import_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('rut', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('subcontract_work_number', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('unit_type', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 50,
        ]);
        $table->addColumn('currency', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 50,
        ]);
        $table->addColumn('currency_rate', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('amount', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('price', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('total', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('partial_description', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('partial_amount', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('partial_total', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('balance_due', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('payment_statement_total', 'float', [
            'default' => null,
            'null' => false,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->addColumn('date', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('json', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }

    public function down()
    {
        $table = $this->table('guide_exits');
        $table->removeColumn('json');
        $table->addColumn('json', 'string', [
            'default' => null,
            'null' => false,
            'after' => 'product_total'
        ]);
        $table->update();
        $table = $this->table('iconstruye_imports');
        $table->removeColumn('type');
        $table->update();
        $this->dropTable('subcontracts');
    }
}
