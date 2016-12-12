<?php
use Migrations\AbstractMigration;

class AssistTypes extends AbstractMigration
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
        $table = $this->table('assist_types');
        $table->addColumn('name', 'string')
              ->addColumn('description', 'text')
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();

        $table = $this->table('assists_assist_types');
        $table->addColumn('assist_id', 'integer', ['null' => false])
              ->addColumn('assist_type_id', 'integer', ['null' => false])
              ->addColumn('hours', 'integer', ['null' => false])
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();

        $table = $this->table('assists');
        $table->addColumn('delay', 'integer', ['null' => true, 'after' => 'overtime', 'default' => 0])
              ->removeColumn('assistance')
              ->removeColumn('permit')
              ->removeColumn('license')
              ->update();

    }
}
