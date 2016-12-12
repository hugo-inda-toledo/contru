<?php
use Migrations\AbstractMigration;

class Currencies extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        //crea tabla currencies
        $table = $this->table('currencies');
        $table->addColumn('name', 'string', array('null' => false))
              ->addColumn('description', 'text')
              ->addColumn('amount', 'decimal')
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();
       //crea tabla currencies_values
        $table = $this->table('currencies_values');
        $table->addColumn('budget_id', 'integer', array('null' => false))
              ->addColumn('currency_id', 'integer', array('null' => false))
              ->addColumn('value', 'integer', array('null' => false))
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();
        //nuevos campos budget_items
        $table = $this->table('budget_items');
        $table->addColumn('general_cost', 'integer', array('after' => 'extra'))
              ->addColumn('utilities', 'integer', array('after' => 'extra'))
              ->update();
        //nuevos campos budgets
        $table = $this->table('budgets');
        $table->addColumn('general_costs', 'integer', array('after' => 'file', 'null' => false))
              ->addColumn('utilities', 'integer', array('after' => 'file', 'null' => false))
              ->addColumn('retentions', 'integer', array('after' => 'file', 'null' => false))
              ->addColumn('advances', 'integer', array('after' => 'file', 'null' => false))
              ->addColumn('monthly_lunch', 'integer', array('after' => 'file', 'null' => false))
              ->addColumn('monthly_mobilization', 'integer', array('after' => 'file', 'null' => false))
              ->update();
    }
}
