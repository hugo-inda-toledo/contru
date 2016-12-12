<div class="panel panel-default">
    <div class="panel-body">
        <h4><strong>Totales e Información Específica del Presupuesto de Obra</strong></h4>
        <div class="row">
            <div class="col-lg-5 col-xs-12">
                <dl class="dl-horizontal">
                    <dt class="text-left" data-toggle="tooltip" data-placement="up" data-original-title="Subtotal de todas las Partidas del Presupuesto"><?= __('Total Costo Directo') ?>:</dt>
                    <dd class="text-right"><?= moneda($budget->total_cost) ?></dd>
                    <dt class="text-left bg-success" data-toggle="tooltip" data-placement="up" data-original-title="Total Gastos Generales del Presupuesto"><?= __('Gastos Generales') ?>:</dt>
                    <dd class="text-right bg-success"><?= moneda($budget->general_costs) ?></dd>
                    <dt class="text-left" data-toggle="tooltip" data-placement="up" data-original-title="Monto Utilidades del Presupuesto"><?= __('Utilidades') ?>:</dt>
                    <dd class="text-right"><?= moneda($budget->utilities) . '%' ?></dd>
                    <?php
                        $utilities_contract = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
                        $total_contract_currency = $budget->total_cost + $budget->general_costs + $utilities_contract; ?>
                    <dt class="text-left bg-success" data-toggle="tooltip" data-placement="up" data-original-title="Suma de Costo Directo, Gastos Generales y Utilidades del Presupuesto"><?= __('Total Neto') ?>:</dt>
                    <dd class="text-right bg-success"><?= moneda($total_contract_currency) ?></dd>
                </dl>
            </div>
            <div class="col-lg-2 col-xs-12">
            </div>
            <div class="col-lg-5 col-xs-12">
                <dl class="dl-horizontal">
                    <dt class="text-left" data-toggle="tooltip" data-placement="up" data-original-title="Moneda seleccionada en la creación del Presupuesto"><?= __('Moneda') ?>:</dt>
                    <dd class="text-right"><?= $budget->currencies[0]->long_name; ?></dd>
                    <dt class="text-left bg-success" data-toggle="tooltip" data-placement="up" data-original-title="Valor Referencial Moneda (Fecha Creación del Presupuesto)"><?= __('Valor Moneda') ?>:</dt>
                    <dd class="text-right bg-success">

                        <?php 
                            if(count($budget->currencies[0]->valoresmonedas) > 0)
                            {
                                echo moneda($budget->currencies[0]->valoresmonedas[0]->currency_value).'<br>('.__('Valor del día: {0}', h($budget->currencies[0]->valoresmonedas[0]->currency_date->format('d/m/Y'))).')'; 
                            }
                            else
                            {
                                echo 1;
                            }
                            
                        ?>
                    </dd>
                    <dt class="text-left" data-toggle="tooltip" data-placement="up" data-original-title="Total Neto en valor referencial Moneda Presupuesto"><?= __('Total Neto Moneda') ?>:</dt>
                    <dd class="text-right"><?= moneda($total_contract_currency); ?></dd>
                    <dt class="text-left bg-success" data-toggle="tooltip" data-placement="up" data-original-title="Porcentaje de Anticipo del Presupuesto"><?= __('Anticipo') ?>:</dt>
                    <dd class="text-right bg-success"><?= moneda($budget->advances) . '%' ?></dd>
                    <dt class="text-left" data-toggle="tooltip" data-placement="up" data-original-title="Porcentaje de Retenciones del Presupuesto"><?= __('Retenciones') ?>:</dt>
                    <dd class="text-right"><?= moneda($budget->retentions) . '%' ?></dd>
                </dl>
            </div>
        </div>

    </div>
</div>