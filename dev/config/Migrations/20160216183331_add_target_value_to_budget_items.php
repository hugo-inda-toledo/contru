<?php
use Migrations\AbstractMigration;

class AddTargetValueToBudgetItems extends AbstractMigration
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
        $table = $this->table('budget_items');
        $table->addColumn('target_value', 'float', [
            'default' => null,
            'null' => true,
            'scale' => 2,
            'precision' => 23,
        ]);
        $table->update();
    }
}
