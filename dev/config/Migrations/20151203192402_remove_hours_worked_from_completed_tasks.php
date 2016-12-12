<?php
use Migrations\AbstractMigration;

class RemoveHoursWorkedFromCompletedTasks extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('completed_tasks');
        $table->removeColumn('hours_worked')
              ->update();
        $table = $this->table('schedules');
        $table->renameColumn('approval_btn', 'progress_approved')
            ->update();
        $table = $this->table('progress');
        $table->removeColumn('approved')
              ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('completed_tasks');
        $table->addColumn('hours_worked', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ])
            ->update();
        $table = $this->table('schedules');
        $table->renameColumn('progress_approved', 'approval_btn')
            ->update();
        $table = $this->table('progress');
        $table->addColumn('approved', 'boolean', [
            'default' => 0,
            'null' => false,
        ])
            ->update();
    }
}
