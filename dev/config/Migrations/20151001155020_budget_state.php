<?php

use Phinx\Migration\AbstractMigration;

class BudgetState extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    */
    public function change()
    {
        $table = $this->table('budget_states');
        $table
            ->addColumn('name', 'string')
            ->addColumn('description', 'text')
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
              ->create();

            $table = $this->table('budget_approvals');
            $table->addColumn('budget_state_id', 'integer', array('after' => 'user_id'))
              ->update();

    }
    
    
    /**
     * Migrate Up.
     */
    public function up()
    {
    
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}