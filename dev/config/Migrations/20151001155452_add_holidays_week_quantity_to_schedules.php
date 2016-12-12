<?php
use Migrations\AbstractMigration;

class AddHolidaysWeekQuantityToSchedules extends AbstractMigration
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
        $table = $this->table('schedules');
        $table->addColumn('holidays_week_quantity', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
            'after' => 'description'
        ]);
        $table->update();
        $table = $this->table('progress');
        $table->renameColumn('overall_progress_hours', 'proyected_progress_percent');
        $table->renameColumn('installed_items_quantity', 'worked_items_quantity');
        $table->update();
    }
}
