<?php

use Phinx\Migration\AbstractMigration;

class IconstruyeData extends AbstractMigration
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
        $table = $this->table('iconstruye_imports');
        $table
             ->addColumn('file', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();

        $table = $this->table('iconstruye_imports');
        $table
             ->removeColumn('file_id')
            ->update();

        $table = $this->table('guide_exits');
        $table
             ->removeColumn('warehouse')
             ->removeColumn('exit_type')
             ->removeColumn('document_number')
             ->removeColumn('document_date')
             ->removeColumn('description')
             ->removeColumn('sub_total')
             ->removeColumn('discount')
             ->removeColumn('tax')
             ->removeColumn('total_net')
             ->removeColumn('total_cost')
             ->removeColumn('total_cost_uf')
             ->removeColumn('delivered_by')
            ->update();

        $table = $this->table('guide_exits');
        $table
             ->addColumn('uid', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
                'after' => 'budget_item_id'
            ])
            ->addColumn('voucher', 'text',[
            'after' => 'uid'
            ])
            ->addColumn('date_system', 'datetime',[
            'after' => 'voucher'
            ])
            ->addColumn('json', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'product_total'
            ])
            ->update();

    }
}
