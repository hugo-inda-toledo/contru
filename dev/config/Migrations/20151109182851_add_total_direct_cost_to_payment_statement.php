<?php
use Migrations\AbstractMigration;

class AddTotalDirectCostToPaymentStatement extends AbstractMigration
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
        $table->addColumn('total_direct_cost', 'float', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
