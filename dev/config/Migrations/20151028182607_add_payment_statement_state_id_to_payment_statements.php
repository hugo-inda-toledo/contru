<?php
use Migrations\AbstractMigration;

class AddPaymentStatementStateIdToPaymentStatements extends AbstractMigration
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
        $table = $this->table('payment_statements');
        $table->addColumn('payment_statement_state_id', 'integer', [
            'default' => 1,
            'limit' => 11,
            'null' => false,
        ]);

        // Agrego llave foarenea
        $table->addForeignKey('payment_statement_state_id', 'payment_statement_states', 'id', array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'));
        $table->update();
    }
}
