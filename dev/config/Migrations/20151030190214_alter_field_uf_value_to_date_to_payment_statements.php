<?php
use Migrations\AbstractMigration;

class AlterFieldUfValueToDateToPaymentStatements extends AbstractMigration
{
     /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('payment_statements');
        $table->removeColumn('uf_value_to_date');            
        $table->addColumn('uf_value_to_date', 'float', array('after' => 'liquid_pay_uf'));
        $table->update();
                        
    }
}
