<?php

use Phinx\Migration\AbstractMigration;

class Cleaning extends AbstractMigration
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
         $exists = $this->hasTable('annexes');
        if ($exists) {
             $this->dropTable('annexes');
        }

        $exists = $this->hasTable('chat_group_members');
        if ($exists) {
            $this->dropTable('chat_group_members');
        }

        $exists = $this->hasTable('chat_groups');
        if ($exists) {
            $this->dropTable('chat_groups');
        }

        $exists = $this->hasTable('configurations');
        if ($exists) {
            $this->dropTable('configurations');
        }

        $exists = $this->hasTable('events');
        if ($exists) {
            $this->dropTable('events');
        }

        $exists = $this->hasTable('files');
        if ($exists) {
            $this->dropTable('files');
        }
    }
}
