<?php

use Phinx\Migration\AbstractMigration;

class Bonus extends AbstractMigration
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
        $table = $this->table('bonus_details');
            $table
                ->addColumn('bonus_id', 'integer')
                ->addColumn('budget_items_id', 'integer')
                ->addColumn('percentage', 'integer')
                ->addColumn('created', 'datetime')
                ->addColumn('modified', 'datetime')
                ->addColumn('user_created_id', 'integer')
                ->addColumn('user_modified_id', 'integer')
                ->create();


        $table = $this->table('bonuses');
            $table
                ->dropForeignKey(['bonus_types_id'])
                ->removeColumn('bonus_types_id')
                ->addColumn('description', 'text', [
                                                'default' => null,
                                                'null' => false,
                                                'after' => 'budget_id'
                                            ])
                ->addColumn('state', 'string', [
                                                'default' => null,
                                                'null' => false,
                                                'after' => 'budget_id'
                                            ])
                ->update();

        $this->dropTable('bonus_types');
    }
}
