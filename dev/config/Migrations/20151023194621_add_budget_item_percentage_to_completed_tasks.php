<?php
use Migrations\AbstractMigration;

class AddBudgetItemPercentageToCompletedTasks extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('completed_tasks');
        $table->addColumn('budget_item_percentage', 'float', [
            'default' => null,
            'null' => false,
            'after' => 'worker_id'
        ]);
        $table->removeColumn('installed_items_quantity');
        $table->removeColumn('completed_percent');
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('completed_tasks');
        $table->addColumn('installed_items_quantity', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'after' => 'hours_worked'
        ]);
        $table->addColumn('installed_items_quantity', 'float', [
            'default' => null,
            'null' => false,
            'after' => 'hours_worked'
        ]);
        $table->removeColumn('budget_item_percentage');
        $table->update();
    }
}
