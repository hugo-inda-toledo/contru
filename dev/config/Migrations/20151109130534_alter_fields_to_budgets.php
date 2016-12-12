<?php
use Migrations\AbstractMigration;

class AlterFieldsToBudgets extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        /** Cambios enteros por float */
        $table = $this->table('budgets');        
        $table->changeColumn('advances', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
        $table->changeColumn('retentions', 'float', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
         $table->changeColumn('utilities', 'float', [
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
        /** Dejo los campos como entero */
        $table = $this->table('budgets');        
        $table->changeColumn('advances', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
        $table->changeColumn('retentions', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
         $table->changeColumn('utilities', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
        ]);
        $table->update();
    }
}
