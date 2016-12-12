<?php
use Migrations\AbstractMigration;

class RemoveUfValueFromBudgets extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('budgets');
        $table->removeColumn('uf_value');
        $table->renameColumn('total_cost_uf', 'total_cost');
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('budgets');
        $table->addColumn('uf_value', 'integer');
        $table->renameColumn('total_cost', 'total_cost_uf');
        $table->update();
    }
}
