<?php
use Migrations\AbstractMigration;

class AddAssistanceDateToAssists extends AbstractMigration
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
        $table = $this->table('assists');
        $table->addColumn('assistance_date', 'datetime', [
            'default' => null,
            'after' => 'worker_id',
            'null' => false,
        ]);
        $table->update();
    }
}
