<?php

use Phinx\Migration\AbstractMigration;

class NumberOfDecimalToPaymentStatements extends AbstractMigration
{
    /**
     * Change Method.
     *
     */
    public function change()
    {
        // numero de decimales en float    
        $table = $this->table('payment_statements');
        $table->changeColumn('total_cost_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('overall_progress', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('contract_value_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('advance_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('paid_to_date_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('advance_present_payent_statement_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('balance_due_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('discount_retentions_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
          $table->changeColumn('discount_refund_advances_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('liquid_pay_uf', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);
        $table->changeColumn('uf_value_to_date', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);    
        $table->changeColumn('total_direct_cost', 'float', [
                'default' => 0,
                'precision' =>11,
                'scale' => 2,                
        ]);

        // Estado default
        $table->changeColumn('payment_statement_state_id', 'integer', [
                'default' => 2                                
        ]);

        $table->update(); 

    }
}
