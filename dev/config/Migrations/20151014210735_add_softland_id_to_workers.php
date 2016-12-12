<?php
use Migrations\AbstractMigration;

class AddSoftlandIdToWorkers extends AbstractMigration
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
        $table = $this->table('workers');
        $table->addColumn('softland_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'after' => 'id'
        ])
            ->addColumn('modified', 'datetime', [
            'default' => null
        ])
            ->update();
    }
}
