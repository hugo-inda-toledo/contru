<?php
use Migrations\AbstractMigration;

class AddCommentToPaymentStatements extends AbstractMigration
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
        $table->addColumn('comment', 'text', [
            'default' => null,            
        ]);
        $table->update();
    }
}
