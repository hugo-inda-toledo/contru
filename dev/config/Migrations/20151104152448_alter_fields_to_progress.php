<?php
use Migrations\AbstractMigration;

class AlterFieldsToProgress extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        /** Camnbio el tipo de dato a float */
        $table = $this->table('progress');        
        $table->changeColumn('proyected_progress_percent', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        /** Dejo el campo original (entero) */
        $table = $this->table('progress');        
        $table->changeColumn('proyected_progress_percent', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
        $table->update();
    }
}
