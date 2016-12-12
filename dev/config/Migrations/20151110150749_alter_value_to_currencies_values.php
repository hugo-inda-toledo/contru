<?php
use Migrations\AbstractMigration;

class AlterValueToCurrenciesValues extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        /** Cambios enteros por float */
        $table = $this->table('currencies_values');        
        $table->changeColumn('value', 'float', [
                'default' => null,
                'precision' =>12,
                'scale' => 3,
                'null' => false
        ]);
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        /** Dejo el campo entero */
        $table = $this->table('currencies_values');        
        $table->changeColumn('value', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);   
        $table->update();
    }
}
