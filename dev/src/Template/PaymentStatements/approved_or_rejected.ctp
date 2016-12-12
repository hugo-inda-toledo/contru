<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Estados de Pago'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Volver'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/schedules/index/'+$schedule->budget_id];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Detalles del estado de pago</h3>
    </div>
    <div class="panel-body">

        <div class="row">
            <div class="col-sm-6">
                <table class="table table-hover table-striped">
                    <tr>
                        <th>Fecha</th>
                        <td><?= $this->Time->format($paymentStatement->created, 'dd-MM-YYYY'); ?></td>                        
                    </tr>
                    <tr>
                        <th>Glosa</th>
                        <td><?= $paymentStatement->gloss; ?></td>
                    </tr>
                    <tr>
                        <th>Presentación estimada</th>
                        <td><?= $this->Time->format($paymentStatement->presentation_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th>Cuando se Factura</th>
                        <td><?= $this->Time->format($paymentStatement->billing_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th>Pago estimado</th>
                        <td><?= $this->Time->format($paymentStatement->estimation_pay_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td><?= $paymentStatement->payment_statement_state['name']; ?></td>
                    </tr>  
                </table>             
            </div>
            <div class="col-sm-6">
                
            </div>
        </div>  
    </div>  
</div>


<div class="panel panel-material-blue-grey-700">    
    <div class="panel-heading">
        <h3 class="panel-title">Listado Ítems Presupuesto</h3>
    </div>
    <div class="panel-body">
         <table class="table table-hover table-striped">
            <tr>
                <th>Ítem</th>
                <th>Descripción</th>
                <th>Total [<?= $datos['moneda']['nombre']; ?>]</th>
                <th>Avance a la Fecha [%]</th>
                <th>Avance E.D.P anterior [%]</th>
                <th>Avance presente E.D.P [%]</th>
                <th>Monto [<?= $datos['moneda']['nombre']; ?>]</th>
            </tr>
         <?php foreach ($budget_items as $bi): ?>      
            <?php echo $this->element('budget_items_edp_view',['bi' => $bi, 'paymentStatement' => $paymentStatement]) ?>                          
         <?php endforeach ?>   
         </table>


        <?php $totales = [0,0,0,0] ?>

        <div class="row mt-50">
            <div class="col-sm-12">                        
                <table class="table table-hover table-striped table-bordered">
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
                            <?= $this->Print->round($budget['total_cost']/$datos['moneda']['valor']); ?>  
                            <?php $totales[0] += round($budget['total_cost']/$datos['moneda']['valor'],2) ?>                      
                        </td>
                        
                        <td class="text-center"><?= $this->Print->round($paymentStatement->overall_progress); ?>%</td>
                        <td class="text-center">
                            <?= $this->Print->round($datos['costo_directo']['a_la_fecha']); ?>
                            <?php $totales[1] += $datos['costo_directo']['a_la_fecha']; ?>
                        </td>
                        
                        <td class="text-center"><?= round($datos['ultimo_edp']['overall_progress'],2); ?>%</td>
                        <td class="text-center">
                            <?= $this->Print->round($datos['ultimo_edp']['total_direct_cost']); ?>
                            <?php $totales[2] += $datos['ultimo_edp']['total_direct_cost']; ?>
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
                        <td class="text-center"><?= $this->Print->round($datos['ultimo_edp']['overall_progress']); ?>%</td>
                        <td class="text-center">
                            <?= $this->Print->round($datos['gastos_generales']['anterior']); ?>
                            <?php $totales[2] += $datos['gastos_generales']['anterior']; ?>
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
                        <td class="text-center"><?= $this->Print->round($datos['ultimo_edp']['overall_progress']); ?>%</td>
                        <td class="text-center">
                            <?= $this->Print->round($datos['utilidad']['anterior']); ?>
                            <?php $totales[2] += $datos['utilidad']['anterior']; ?>
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
                        <td class="text-center"><?= $this->Print->decimal($paymentStatement->advance_present_payent_statement_uf); ?></td>
                    </tr>

                </table>
            </div>
        </div>


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
                        <tr>
                            <td>Valor Neto presente Estado de Pago [<?= $datos['moneda']['nombre']; ?>]</td>
                            <td></td>
                        </tr>
                    </table>
             
                    <table class="table table-hover table-striped table-bordered">
                        <tr>
                            <th>Valor [<?= $datos['moneda']['nombre']; ?>] al día Estado de Pago (<?= $paymentStatement->created->format('d-m-Y'); ?>)</th>
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
            </div><!-- end .row -->
        
            <div class="row">
                <div class="col-sm-12">
                <?php if ($permisos['approve']): ?>
                    <?= $this->Html->link(__('Aprobar'), '#',['class' => 'btn btn-primary','id'=>'approve-btn']) ?>    
                <?php endif ?>
                <?php if ($permisos['reject']): ?>
                    <?= $this->Html->link(__('Rechazar'), '#',['class' => 'btn btn-danger','id'=>'reject-btn']) ?>                        
                <?php endif ?>                        
                </div>
            </div>
    </div>
</div>

<?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]],['class' => 'btn btn-flat btn-link']) ?>


<!-- Modal Approve -->
<div class="modal fade" id="modal-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <?= $this->Form->create(); ?>        
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Aprobar Estado de Pago</h4>
      </div>
      <div class="modal-body">
        <p>¿Está seguro que desea <strong>Aprobar</strong> el Estado de Pago?</p>
        <?= $this->Form->hidden('action',['value'=>'approve']); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Sí, aprobar</button>
      </div>
    <?= $this->Form->end(); ?>
    </div>
  </div>
</div>


<!-- Modal Reject -->
<div class="modal fade" id="modal-reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <?= $this->Form->create(); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Estado de Pago</h4>
      </div>
      <div class="modal-body">
        <p>¿Está seguro que desea <strong>Rechazar</strong> el Estado de Pago?</p>
        <?= $this->Form->hidden('action',['value'=>'reject']); ?>
        <?= $this->Form->input('comment',['type'=>'textarea','label'=>'Comentario', 'required']); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Sí, rechazar</button>
      </div>
    <?= $this->Form->end(); ?>
    </div>
  </div>
</div>



<?php $this->start('script'); ?>
    <?= $this->Html->script('payment_statements.approved_or_proyected'); ?>        
<?php $this->end(); ?>
