<?php

use Phinx\Migration\AbstractMigration;

class Charges extends AbstractMigration
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
        $table = $this->table('charges');
            $table
                ->addColumn('softland_id', 'integer', [
                                                'default' => null,
                                                'limit' => 11,
                                                'null' => false,
                                                'after' => 'id'
                                            ])
                ->addColumn('max_amount_deals', 'integer', [
                                                'default' => null,
                                                'limit' => 11,
                                                'null' => false,
                                                'after' => 'name'
                                            ])
                ->addColumn('max_amount_bonus', 'integer', [
                                                'default' => null,
                                                'limit' => 11,
                                                'null' => false,
                                                'after' => 'max_amount_deals'
                                            ])
                ->addColumn('created', 'datetime', [
                                                'default' => null,
                                                'after' => 'max_amount_bonus'
                                            ])
                ->addColumn('modified', 'datetime', [
                                                'default' => null,
                                                'after' => 'created'
                                            ])
                ->update();
    }
}
