<?php
use Migrations\AbstractMigration;

class CreatePaymentStatementStates extends AbstractMigration
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
        $table = $this->table('payment_statement_states');
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'string', [
            'default' => null,
            'limit' => 255,
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

        // Poblar Tabla        
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (1, 'Pendiente','Estado pendiente de Aprobación LDZ')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (2, 'Pendiente GF','Pendiente aprobación Gerente Finanzas LDZ')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (3, 'Pendiente GG','Pendiente aprobación Gerente General LDZ')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (4, 'Aprobado','Aprobado en espera de enviar a Cliente LDZ')");        
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (5, 'Rechazado','Rechazado LDZ')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (6, 'Enviado a Cliente','Enviado a Cliente')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (7, 'Aprobado por Cliente','Aprobado por Cliente')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (8, 'Rechazado por Cliente','Rechazado por Cliente')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (9, 'Factura Emitida','Factura Emitida')");
        $count = $this->execute("INSERT INTO payment_statement_states(id,name,description) VALUES (10, 'Pago Realizado','Pago Realizado')");

    }
}
