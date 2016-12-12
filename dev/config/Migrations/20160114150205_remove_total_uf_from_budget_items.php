<?php
use Migrations\AbstractMigration;

class RemoveTotalUfFromBudgetItems extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('budget_items');
        $table->removeColumn('total_uf');
        $table->addColumn('total_uf', 'float', [
            'comment' => 'total partida en moneda',
            'default' => null,
            'after' => 'total_price',
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->update();

        $table = $this->table('budgets');
        $table->removeColumn('cient');
        $table->update();
    }
     /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('budget_items');
        $table->removeColumn('total_uf');
        $table->addColumn('total_uf', 'integer', [
            'comment' => 'total partida en uf',
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
        $table = $this->table('budgets');
        $table->addColumn('client', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->update();
    }
}
