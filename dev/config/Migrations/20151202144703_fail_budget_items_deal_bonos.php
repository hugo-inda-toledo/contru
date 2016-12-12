<?php

use Phinx\Migration\AbstractMigration;

class FailBudgetItemsDealBonos extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('deal_details');
        $table->removeColumn('budget_items_id');
        $table->addColumn('budget_item_id', 'integer', [
            'limit' => 11,
            'default' => null,
            'null' => false,
            'after' => 'deal_id'
        ]);
        $table->update();

        $table = $this->table('bonus_details');
        $table->removeColumn('budget_items_id');
        $table->addColumn('budget_item_id', 'integer', [
            'limit' => 11,
            'default' => null,
            'null' => false,
            'after' => 'bonus_id'
        ]);
        $table->update();
    }
}
