<?php
use Migrations\AbstractMigration;

class AddExtraFieldsToPaymentStatement extends AbstractMigration
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
        $table->addColumn('gloss', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('presentation_date', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('billing_date', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('estimation_pay_date', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->update();
    }
}
