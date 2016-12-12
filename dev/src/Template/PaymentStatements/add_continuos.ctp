<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Estado de Pago'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
?>

<div class="super-content">
<?= $this->Form->create(); ?>
<div class="panel panel-material-blue-grey-700">    
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Ítems Presupuesto</h3>
    </div><!-- end .panel-heading -->

    <?php $edp = ['suma_item'=>0,'per_ant'=>0,'per_act'=>0]; ?>

    <div class="panel-body">
            <table class="table table-hover">
                <tr>
                    <th>Ítem</th>
                    <th>Descripción</th>
                    <th>Total [<?= $datos['moneda']['nombre']; ?>]</th>
                    <th>Avance a la Fecha [%]</th>
                    <th class="text-center">Avance E.D.P anterior [%]</th>
                    <th class="text-center">Avance presente E.D.P [%]</th>
                    <th>Monto [<?= $datos['moneda']['nombre']; ?>]</th>
                </tr>

                <?php // Items budget/edp ?>

                <?php foreach ($budget_items as $bi): ?>                    
                    <?php echo $this->element('budget_items_edp_add2',['bi' => $bi,'budget'=> $budget]) ?>
                <?php endforeach ?>                 

            </table>


            <br>

            <?php // Resumen EDP ?>

            <?php $totales = [0,0,0,0]; ?>

        
            <table class="table table-hover table-bordered">
                <tr>                              
                    <th></th>                                                  
                    <th class="text-center">Total [<?= $datos['moneda']['nombre']; ?>]</th>
                    <th class="text-center">Avance a la Fecha [%]</th>                    
                    <th class="text-center">Avance a la Fecha [<?= $datos['moneda']['nombre']; ?>]</th>                                        
                    <th class="text-center">Avance E.D.P anterior [%]</th>
                    <th class="text-center">Avance E.D.P anterior [<?= $datos['moneda']['nombre']; ?>]</th>
                    <th class="text-center">Avance presente E.D.P [%]</th>                    
                    <th class="text-center">Avance presente E.D.P [<?= $datos['moneda']['nombre']; ?>]</th>                    
                 </tr>          
                
                 <tr>
                    <td>TOTAL COSTO DIRECTO</td>                    
                    <td class="text-center">
                        <?= $this->Print->round($datos['costo_directo']['total_moneda']); ?>  
                        <?php $totales[0] += $datos['costo_directo']['total_moneda']; ?>                      
                    </td>
                    
                    <td class="text-center"><?= $this->Print->round($paymentStatement->overall_progress); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['costo_directo']['a_la_fecha']); ?>
                        <?php $totales[1] += $datos['costo_directo']['a_la_fecha']; ?>
                    </td>
                    
                    <td class="text-center"><?= round($ultimo_edp['overall_progress'],2); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($ultimo_edp['total_direct_cost']); ?>
                        <?php $totales[2] += $ultimo_edp['total_direct_cost']; ?>
                    </td>
                    
                    <td class="text-center"><?= round($datos['edp']['percent'],2) ?>%</td>
                    <td class="text-center">                        
                        <?= $this->Print->round($paymentStatement['total_direct_cost']); ?>
                        <?php $totales[3] += $paymentStatement['total_direct_cost']; ?>                        
                    </td>                                                                                                              
                </tr>

                <tr>
                    <td>GASTOS GENERALES</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['gastos_generales']['total_moneda']); ?>
                        <?php $totales[0] += $datos['gastos_generales']['total_moneda']; ?> 
                    </td>
                    <td class="text-center"><?= $this->Print->round($paymentStatement->overall_progress); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['gastos_generales']['total_moneda'] * $paymentStatement->overall_progress/100); ?>
                        <?php $totales[1] += round($datos['gastos_generales']['total_moneda'] * $paymentStatement->overall_progress/100,2); ?>
                    </td>
                    <td class="text-center"><?= $this->Print->round($ultimo_edp['overall_progress']); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($ultimo_edp['gastos_generales_moneda']); ?>
                        <?php $totales[2] += $ultimo_edp['gastos_generales_moneda']; ?>
                    </td>    
                    <td class="text-center"><?= round($datos['edp']['percent'],2) ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['gastos_generales']['edp']); ?>
                        <?php $totales[3] += $datos['gastos_generales']['edp']; ?>
                    </td>

                </tr>
                 <tr>
                    <td>UTILIDAD</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['utilidad']['total_moneda']); ?>
                        <?php $totales[0] += $datos['utilidad']['total_moneda']; ?> 
                    </td>
                    <td class="text-center"><?= $this->Print->round($paymentStatement->overall_progress); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['utilidad']['total_moneda'] * $paymentStatement->overall_progress/100); ?>
                        <?php $totales[1] += $datos['utilidad']['total_moneda'] * $paymentStatement->overall_progress/100; ?> 
                    </td>     
                    <td class="text-center"><?= $this->Print->round($ultimo_edp['overall_progress']); ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($ultimo_edp['utilidad_moneda']); ?>
                        <?php $totales[2] += $ultimo_edp['utilidad_moneda']; ?>
                    </td>    
                    <td class="text-center"><?= $this->Print->round($datos['edp']['percent']) ?>%</td>
                    <td class="text-center">
                        <?= $this->Print->round($datos['utilidad']['edp']); ?>
                        <?php $totales[3] += $datos['utilidad']['edp']; ?>
                    </td>     
                </tr>

                <tr>
                    <td></td>
                    <td class="text-center"><?= $this->Print->decimal($totales[0]); ?></td>
                    <td class="text-center"></td>
                    <td class="text-center"><?= $this->Print->decimal($paymentStatement->paid_to_date_uf + $paymentStatement->advance_present_payent_statement_uf); ?></td>                                        
                    <td class="text-center"></td>
                    <td class="text-center"><?= $this->Print->decimal($totales[2]); ?></td>
                    <td class="text-center"></td>
                    <td class="text-center"><?= $this->Print->decimal($totales[3]); ?></td>
                </tr>

            </table>


        <div class="row">
                <div class="col-sm-6">
                    <table class="table table-hover table-striped table-bordered">
                        <tr>
                            <td></td>
                            <td>Unidad [<?= $datos['moneda']['nombre']; ?>]</td>
                        </tr>
                        <tr>
                            <td>Valor trabajos efectuados a la fecha</td>
                            <td><?= $this->Print->decimal($paymentStatement->paid_to_date_uf + $paymentStatement->advance_present_payent_statement_uf,2,',','.'); ?></td>
                        </tr>
                            <td>Valor trabajos estado anterior</td>
                            <td><?= $this->Print->decimal($paymentStatement->paid_to_date_uf); ?></td>
                        </tr>
                            <td>Valor presente estado de Pago</td>
                            <td><?= $this->Print->decimal($paymentStatement->advance_present_payent_statement_uf); ?></td>
                        </tr>
                            <td>Descuento por Retenciones <?= $budget['retentions']; ?>%</td>
                            <td><?= $this->Print->decimal($paymentStatement->discount_retentions_uf); ?></td>
                        </tr>
                            <td>Descuento devolución de Anticipo <?= $budget['advances']; ?>%</td>
                            <td><?= $this->Print->decimal($paymentStatement->discount_refund_advances_uf); ?></td>
                        </tr>
                            <td>Líquido a Pagar</td>
                            <td><?= $this->Print->decimal($paymentStatement->liquid_pay_uf); ?></td>
                        </tr>
                    </table>
                                  
                    <table class="table table-hover table-striped table-bordered">
                        <tr>
                            <td>Valor [<?= $datos['moneda']['nombre']; ?>] fecha presupuesto <?= $this->Time->format($budget->created,'dd-MM-YYYY'); ?></td>
                            <td><?= $this->Print->decimal($paymentStatement->contract_value_uf) ?></td>
                        </tr>                       
                    </table>
             
                    <table class="table table-hover table-striped table-bordered">
                        <tr>
                            <th>Valor [<?= $datos['moneda']['nombre']; ?>] al día Estado de Pago (<?= date('d-m-Y'); ?>)</th>
                            <td><?= $this->Print->decimal($paymentStatement->uf_value_to_date); ?></td>
                        </tr>
                        <tr>
                            <th>TOTAL NETO $</th>
                            <td><?= $this->Print->dot($paymentStatement->total_net); ?></td>
                        </tr>
                        <tr>
                            <th>IVA</th>
                            <td><?= $this->Print->dot($paymentStatement->tax); ?></td>
                        </tr>
                        <tr>
                            <th>TOTAL</th>
                            <td><?= $this->Print->dot($paymentStatement->total); ?></td>
                        </tr>
                    </table>                    
                </div>
                <div class="col-sm-6">
                    <table class="table table-hover table-striped table-bordered">
                        <tr>
                            <td></td>   
                            <td>Unidad [<?= $datos['moneda']['nombre']; ?>]</td>
                        </tr>
                        <tr>
                            <td>Valor del Contrato</td>
                            <td><?= $this->Print->round($datos['contrato']['total']); ?></td>
                        </tr>
                        <tr>
                            <td>Anticipo <?= $budget['advances']; ?>%</td>
                            <td><?= $this->Print->round($datos['contrato']['anticipo']); ?></td>
                        </tr>
                        <tr>
                            <td>Pagado a la Fecha</td>
                            <td><?= $this->Print->round($paymentStatement->paid_to_date_uf); ?></td>
                        </tr>
                        <tr>
                            <td>Avance presente Estado de Pago</td>
                            <td><?= $this->Print->round($paymentStatement->advance_present_payent_statement_uf); ?></td>
                        </tr>
                        <tr>
                            <td>Saldo por pagar</td>
                            <td><?= $this->Print->round($paymentStatement->balance_due_uf); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        <?= $this->Form->button('Guardar'); ?>

    </div><!-- end .pane-body --> 
</div><!-- end .panel -->


<?= $this->Form->end(); ?>
</div><!-- end .super-content -->

