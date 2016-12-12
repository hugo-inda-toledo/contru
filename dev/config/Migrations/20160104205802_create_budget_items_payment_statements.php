<?php
use Migrations\AbstractMigration;

class CreateBudgetItemsPaymentStatements extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('payment_statements');
        $table
            ->removeColumn('total_cost_uf')
            ->addColumn('total_cost', 'float', [
                'comment' => 'costo total estado pago en uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('overall_progress')
            ->addColumn('overall_progress', 'float', [
                'comment' => 'avance obra estado de pago',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('contract_value_uf')
            ->addColumn('contract_value', 'float', [
                'comment' => 'valor del contrato uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('advance_uf')
            ->removeColumn('paid_to_date_uf')
            ->addColumn('paid_to_date', 'float', [
                'comment' => 'pagado a la fecha uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('advance_present_payent_statement_uf')
            ->addColumn('progress_present_payment_statement', 'float', [
                'comment' => 'avance presente estado pago',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('balance_due_uf')
            ->addColumn('balance_due', 'float', [
                'comment' => 'saldo por pagar',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('discount_retentions_uf')
            ->addColumn('discount_retentions', 'float', [
                'comment' => 'descuento por retenciones uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('discount_refund_advances_uf')
            ->addColumn('discount_advances', 'float', [
                'comment' => 'descuento devolucion de anticipo uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('liquid_pay_uf')
            ->addColumn('liquid_pay', 'float', [
                'comment' => 'liquido a pagar en uf',
                'default' => null,
                'limit' => 23,
                'scale' => 2,
                'precision' => 23,
                'null' => true,
            ])
            ->removeColumn('uf_value_to_date')
            ->addColumn('currency_value_to_date', 'float', [
                'comment' => 'valor uf a fecha estado de pago',
                'default' => null,
                'limit' => 11,
                'scale' => 2,
                'precision' => 23,
                'null' => false,
            ])
            ->removeColumn('total_net')
            ->addColumn('total_net', 'integer', [
                'comment' => 'total neto',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->removeColumn('tax')
            ->addColumn('tax', 'integer', [
                'comment' => 'iva',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->removeColumn('total')
            ->addColumn('total', 'integer', [
                'comment' => 'total',
                'default' => null,
                'limit' => 23,
                'null' => false,
            ])
            ->addColumn('payment_statement_parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
                'after' => 'budget_id'
            ])
            ->addColumn('version_number', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
                'after' => 'budget_id'
            ])
            ->update();

        $table = $this->table('budget_items_payment_statements');
        $table->addColumn('payment_statement_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('budget_item_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('overall_progress', 'float', [
            'default' => null,
            'limit' => 11,
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->addColumn('previous_progress', 'float', [
            'default' => null,
            'limit' => 11,
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->addColumn('progress', 'float', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('overall_progress_value', 'float', [
            'default' => null,
            'limit' => 23,
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->addColumn('previous_progress_value', 'float', [
            'default' => null,
            'limit' => 23,
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->addColumn('progress_value', 'float', [
            'default' => null,
            'limit' => 23,
            'scale' => 2,
            'precision' => 23,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();

        $table = $this->table('progress');
        $table->dropForeignKey('progress_ibfk_2');
        $table->removeColumn('payment_statement_id');
        $table->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('payment_statements');
        $table
            ->removeColumn('total_cost')
            ->addColumn('total_cost_uf', 'float', [
                'comment' => 'costo total estado pago en uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('overall_progress')
            ->addColumn('overall_progress', 'float', [
                'comment' => 'avance obra estado de pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('contract_value')
            ->addColumn('contract_value_uf', 'float', [
                'comment' => 'valor del contrato uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('advance_uf', 'float', [
                'comment' => 'pagado a la fecha uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('paid_to_date')
            ->addColumn('paid_to_date_uf', 'float', [
                'comment' => 'pagado a la fecha uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('progress_present_payment_statement')
            ->addColumn('advance_present_payent_statement_uf', 'float', [
                'comment' => 'avance presente estado pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('balance_due')
            ->addColumn('balance_due_uf', 'float', [
                'comment' => 'saldo por pagar',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('discount_retentions')
            ->addColumn('discount_retentions_uf', 'float', [
                'comment' => 'descuento por retenciones uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('discount_advances')
            ->addColumn('discount_refund_advances_uf', 'float', [
                'comment' => 'descuento devolucion de anticipo uf',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('liquid_pay')
            ->addColumn('liquid_pay_uf', 'float', [
                'comment' => 'liquido a pagar en uf',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->removeColumn('currency_value_to_date')
            ->addColumn('uf_value_to_date', 'integer', [
                'comment' => 'valor uf a fecha estado de pago',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('total_net')
            ->addColumn('total_net', 'integer', [
                'comment' => 'total neto',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('tax')
            ->addColumn('tax', 'integer', [
                'comment' => 'iva',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('total')
            ->addColumn('total', 'integer', [
                'comment' => 'total',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->removeColumn('payment_statement_parent_id')
            ->removeColumn('version_number')
            ->update();

        $this->dropTable('budget_items_payment_statements');

        $table = $this->table('progress');
        $table->addColumn('payment_statement_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->update();
    }
}
