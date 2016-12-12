<?php
use Migrations\AbstractMigration;

class RemoveMonthlyLunchFromBudgets extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('budgets');
        $table->removeColumn('monthly_mobilization');
        $table->removeColumn('monthly_lunch');
        $table->removeColumn('material_contribution');
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('budgets');
        $table->addColumn('monthly_mobilization', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('monthly_lunch', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('material_contribution', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
