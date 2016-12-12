<?php
use Migrations\AbstractMigration;

class BuildingsUsers extends AbstractMigration
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
        $table = $this->table('buildings_users');
        $table->addColumn('user_id', 'integer')
              ->addColumn('building_id', 'integer')
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();
    }
}
