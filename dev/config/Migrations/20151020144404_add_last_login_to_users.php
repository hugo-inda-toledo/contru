<?php
use Migrations\AbstractMigration;

class AddLastLoginToUsers extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('last_login', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->update();

        $table = $this->table('completed_tasks');
        $table->removeColumn('hours_worked');
        $table->removeColumn('completed_percent');
        $table->addColumn('hours_worked', 'integer', [
            'default' => null,
            'after' => 'worker_id',
            'null' => false,
        ]);
        $table->addColumn('completed_percent', 'float', [
            'default' => null,
            'after' => 'worker_id',
            'null' => false,
        ]);
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('last_login');
        $table->update();

        $table = $this->table('completed_tasks');
        $table->removeColumn('hours_worked');
        $table->removeColumn('completed_percent');
        $table->addColumn('hours_worked', 'integer', [
            'default' => null,
            'after' => 'worker_id',
            'null' => true,
        ]);
        $table->addColumn('completed_percent', 'float', [
            'default' => null,
            'after' => 'worker_id',
            'null' => true,
        ]);
        $table->update();
    }
}
