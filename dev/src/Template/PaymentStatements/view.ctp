<?php //debug($this->request->session()->read('groups'));?>
<style>
    body {
        font-size: 12px;
    }

    b, strong {
        font-weight: normal;
    }
</style>
<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Estados de Pago'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Volver'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/schedules/index/'+$schedule->budget_id];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Detalles del Estado de Pago</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <div class="row text-center">
            <div class="col-sm-12">
                <?= $this->Html->link(__('Volver'), '/payment_statements', ['class' => 'btn btn-lg btn-warning']);?>
            </div>
                <?php if($paymentStatement->draft == 0):?>
                    <div class="col-sm-12">
                        <?php /*if ($permisos['approve'] && !$noButtons): ?>
                            <?= $this->Html->link(__('Aprobar'), '#', ['class' => 'btn btn-primary approve-btn']) ?>
                        <?php endif ?>
                        <?php if ($permisos['reject'] && !$noButtons): ?>
                            <?= $this->Html->link(__('Rechazar'), '#', ['class' => 'btn btn-danger reject-btn']) ?>
                        <?php endif ?>
                            <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id' => $budget->building_id]],['class' => 'btn btn-flat btn-link'])*/ ?>
                            <?php if($paymentStatement->third_approval == 1 && $paymentStatement->second_approval == 1 && $paymentStatement->first_approval == 1): ?>
                                <div class="alert alert-success" role="alert">Estado de pago: <strong>Aprobado por LDZ</strong></div>

                                <?php if(is_null($paymentStatement->email_sent)):?>
                                    <br>

                                    <div class="panel panel-material-blue-grey-700">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Enviar Estado de Pago</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-sm-12">
                                                <?= $this->Form->create(null,['url'=>['controller'=>'payment_statements','action'=>'send_payment'],'id'=>'send_edp']); ?>
                                                <?= $this->Form->hidden('id',['value'=>$id]); ?>
                                                <?= $this->Form->input('email',
                                                    ['type'=>'text','placeholder'=>'Ingrese correo','label'=>'Correo electrónico',
                                                    'templates' => [
                                                    'inputContainer' => '<div class="form-group">{{content}} <span id="error-correo" class="help-block hidden">Ingrese un correo válido</span></div>',
                                                    ]
                                                    ]
                                                    ); ?>
                                                <?= $this->Form->button(__('Enviar'),['type'=>'button','id'=>'showAgreeBtn']) ?>
                                                <?= $this->Form->end(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif(is_null($paymentStatement->client_approval)):?>
                                    <div class="panel panel-material-blue-grey-700">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Cliente Aprueba o Rechaza Estado de Pago</h3>
                                        </div>
                                        <div class="panel-body">

                                            <button class="btn btn-primary" data-toggle="modal" data-target="#client_approval">Aprobado por Cliente</button>
                                            <button class="btn btn-danger" data-toggle="modal" data-target="#client_reject">Rechazado por Cliente</button>

                                        </div>
                                    </div>
                                <?php elseif($paymentStatement->client_approval == 1):?>
                                    <div class="alert alert-success" role="alert">Estado de pago: <strong>Aprobado por el Cliente</strong></div>
                                <?php else:?>
                                    <div class="alert alert-danger" role="alert">Estado de pago: <strong>Rechazado por el cliente</strong></div>

                                    <?= $this->Form->postLink(__('Corregir'), ['controller' => 'payment_statements', 'action' => 'generateByOld', $paymentStatement->id], ['class' => 'btn btn-sm btn-primary']) ?>
                                <?php endif;?>

                            <?php elseif($paymentStatement->second_approval == 1 && is_null($paymentStatement->third_approval)):?>
                                <?php if($this->Access->verifyAccessByKeyword('gerente_general') == true):?>
                                    <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                                    <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                                <?php endif;?>
                            <?php elseif($paymentStatement->first_approval == 1 && is_null($paymentStatement->second_approval)):?>
                                <?php if($this->Access->verifyLevel(8) == true || $this->Access->verifyAccessByKeyword('gerente_finanzas') == true):?>
                                    <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                                    <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                                <?php endif;?>
                            <?php elseif(is_null($paymentStatement->first_approval)):?>
                                <?php if($this->Access->verifyLevel(8) == true || $this->Access->verifyAccessByKeyword('visitador') == true):?>
                                    <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                                    <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                                <?php endif;?>
                            <?php endif;?>
                    </div>
                <?php else:?>
                    <div class="col-sm-12">
                        <div class="alert alert-info" role="alert" style="font-size:18px;"><i class="glyphicon glyphicon-pencil" style="font-size:35px;"></i><br><strong>Este estado de pago no ha sido enviado para su aprobación ya esta en modo borrador.</strong></div>
                        <?= $this->Form->postLink(__('Enviar para aprobación'), ['controller' => 'payment_statements', 'action' => 'accept', $paymentStatement->id], [ 'confirm' => __('Estas seguro de enviar el estado de pago [{0}] para aprobación?', $paymentStatement->gloss), 'class' => 'btn btn-lg btn-primary']) ?>

                        <?= $this->Html->link('Editar', '/payment_statements/edit/'.$paymentStatement->id, ['class' => 'btn btn-lg btn-warning']); ?>
                    </div>
                <?php endif;?>
            
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <h4><strong>Información General Estado de Pago: </strong><?= $paymentStatement->gloss; ?></h4>
            </div>
            <div class="col-sm-12">
                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <th><strong data-toggle="tooltip" data-placement="up" data-original-title="Fecha creación del sistema"><?= __('Creación:'); ?></strong></th>
                        <td><?= $this->Time->format($paymentStatement->created, 'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th><h5><strong data-toggle="tooltip" data-placement="up" data-original-title="Fecha que se envía al mandante"><?= __('Presentación Estimada') ?></strong></h5></th>
                        <td><?= $this->Time->format($paymentStatement->presentation_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th><strong data-toggle="tooltip" data-placement="up" data-original-title="Fecha de facturación"><?= __('Factura Estimada') ?></strong></th>
                        <td><?= $this->Time->format($paymentStatement->billing_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                    <tr>
                        <th><strong data-toggle="tooltip" data-placement="up" data-original-title="Fecha de pago mandante"><?= __('Pago Estimado') ?></strong></th>
                        <td><?= $this->Time->format($paymentStatement->estimation_pay_date,'dd-MM-YYYY'); ?></td>
                    </tr>
                </table>
            </div>
            <!--<div class="col-sm-6">
                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <th>Glosa</th>
                        <td><?php //$paymentStatement->gloss; ?></td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td><?php //$paymentStatement->payment_statement_state->name; ?></td>
                    </tr>
                    <tr>
                        <th><strong data-toggle="tooltip" data-placement="up" data-original-title="Comentarios ingresados durante revisión"><?php //__('Comentario') ?></strong></th>
                        <td><?php //$paymentStatement->comment; ?></td>
                    </tr>
                </table>
            </div>-->
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <!--<th>Ítem</th>-->
                        <th>Designación</th>
                        <th class="text-right">Total Contrato</th>
                        <th class="text-right" colspan="2">Avance a la Fecha <br><span class="th-double">[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                        <th class="text-right" colspan="2">Avance EP anterior <br><span class="th-double">[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                        <th class="text-right" colspan="2">Avance presente EP <br><span class="th-double">[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                    </tr>
                    <?php foreach ($payment_statements as $payment_statement) : ?>
                        <tr>
                            <!--<td><?= 'Estado Pago N° ' . $payment_statement->id; ?></td>-->
                            <td><?= $payment_statement->gloss; ?></td>
                            <td class="text-right"><?= moneda($payment_statement->contract_value ); ?></td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_to_date); ?>%</td>
                            <td class="text-right"><?= moneda($payment_statement->progress_present_payment_statement ); ?></td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_last) ?>%</td>
                            <td class="text-right"><?= moneda($payment_statement->paid_to_date ); ?></td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_present); ?>%</td>
                            <td class="text-right"><?= moneda($payment_statement->total_cost ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!--<div class="center-block text-center">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="highlight_with_data"> Destacar partidas con pagos
                        </label>
                    </div>
                </div>-->

                <?php $unidad_moneda = $budget['currencies_values'][0]['currency']['name']; ?>
                <div class="panel panel-material-blue-grey-700">
                    <?php $type_bi = ($type_payment_statement == 'originales') ? 0 : 1; ?>
                    <?= $this->Form->hidden('currency_value_to_date', ['step' => 'any', 'type' => 'number', 'value' => $paymentStatement->currency_value_to_date]) ?>
                    <div class="panel-group" id="budget_items" role="tablist" aria-multiselectable="true">
                    <?php if (!empty($budget_items)) : ?>

                        <!--<pre>
                        <?php //(print_r($budget_items);?>
                        </pre>-->
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <?php
                            foreach ($budget_items as $bi) :

                                $progress_totals = array();
                                $last_progress_real_data = array();

                                foreach($bi['children'] as $children)
                                {
                                    
                                    if($children['parent_id'] == $bi['id'] && $children['children'] != null)
                                    {
                                        

                                        foreach($children['children'] as $children2)
                                        {
                                            /*echo '<pre>';
                                            print_r($children2);
                                            echo '</pre>';*/

                                            $y=0;
                                            $x=0;

                                            if($children2['budget_items_payment_statements'] != null)
                                            {
                                                $progress_totals[$children->id][$children2->id]['progress_value'] = 0;
                                                $progress_totals[$children->id][$children2->id]['previous_progress_value'] = 0;
                                                $progress_totals[$children->id][$children2->id]['overall_progress_value'] = 0;

                                                foreach($children2['budget_items_payment_statements'] as $bips)
                                                {

                                                    if($x == 0)
                                                    {
                                                        $last_progress_porc = $bips->progress;
                                                        $last_progress_real_data[$y] = $bips;

                                                        $y++;
                                                        $x++;
                                                    }
                                                    else
                                                    {
                                                        if($bips->progress > $last_progress_porc || $bips->progress == $bips->previous_progress)
                                                        {
                                                            $last_progress_real_data[$y-1] = $bips;
                                                            $last_progress_porc = $bips->progress;
                                                        }
                                                        else
                                                        {
                                                            $last_progress_real_data[$y] = $bips;
                                                            $last_progress_porc = $bips->progress;

                                                            $y++;
                                                        }
                                                    }
                                                }

                                                

                                                foreach($last_progress_real_data as $lp)
                                                {
                                                    $progress_totals[$children->id][$children2->id]['progress_value'] = $lp->progress_value;
                                                    $progress_totals[$children->id][$children2->id]['previous_progress_value'] = $lp->previous_progress_value;
                                                    $progress_totals[$children->id][$children2->id]['overall_progress_value'] = $lp->overall_progress_value;
                                                    $progress_totals[$children->id][$children2->id]['item'] = $children2->item;
                                                }
                                            }
                                        }
                                    }
                                }

                                /*echo '<pre>';
                                print_r($progress_totals);
                                echo '</pre>';*/

                                $panelType = ($bi['disabled'] == 0) ? 'material-blue-grey-700' : 'warning';
                                if($bi['extra'] == $type_bi) :

                                    echo $this->element('budget_items_edp_view', ['bi' => $bi, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda, 'progress_totals' => $progress_totals]);
                                endif;
                            endforeach; ?>
                        </div>

                        <?php //debug($budget_items);?>
                        <div class="col-md-12 col-sm-12">
                            <h4><strong>Resumen</h4>
                        </div>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <div class="panel-body">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="675"></th>
                                            <th width="79" class="text-right">Total<br><a class="btn btn-link btn-xs uber-small" data-toggle="currency-total" href="javascript:void(0)"><?= $unidad_moneda; ?></a></th>
                                            <th width="215" class="text-right" colspan="2">
                                                Avance a la Fecha<br>
                                                <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                            </th>
                                            <th width="215" class="text-right" colspan="2">
                                                Avance EP anterior<br>
                                                <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                            </th>
                                            <th width="215" class="text-right" colspan="2">
                                                Avance presente EP<br>
                                                <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            

                                            $total_percent_to_date = $paymentStatement->total_percent_to_date;
                                            $total_percent_last = $paymentStatement->total_percent_last;
                                            $total_percent_present = $paymentStatement->total_percent_present;

                                            /*$total_percent_to_date = ($paymentStatement->progress_present_payment_statement * 100) / $paymentStatement->contract_value;
                                            $total_percent_last = ($paymentStatement->paid_to_date * 100) / $paymentStatement->contract_value;
                                            $total_percent_present = (($paymentStatement->progress_present_payment_statement * 100) / $paymentStatement->contract_value) - (($paymentStatement->paid_to_date * 100) / $paymentStatement->contract_value);*7

                                            /*$total_direct_cost_to_date = ($extraRow['total_cost'] * $total_percent_to_date) / 100;
                                            $total_direct_cost_last = ($extraRow['total_cost'] * $total_percent_last) / 100;
                                            $total_direct_cost_present = ($extraRow['total_cost'] * $total_percent_present) / 100;*/

                                            $total_direct_cost_to_date = $paymentStatement->total_direct_cost_to_date;
                                            $total_direct_cost_last = $paymentStatement->total_direct_cost_last;
                                            $total_direct_cost_present = $paymentStatement->total_direct_cost_present;

                                            
                                        ?>
                                        <tr>
                                            <td>Total Costo Directo </td>
                                            <td class="text-right"><?= moneda($extraRow['total_cost']);?></td>
                                            <td class="text-right" nowrap><?= moneda($total_percent_to_date); ?>%</td>
                                            <td class="text-right" nowrap>
                                                <?php
                                                    echo moneda($total_direct_cost_to_date); 
                                                ?>
                                            </td>

                                            <td class="text-right" nowrap><?= moneda($total_percent_last); ?>%</td>
                                            <td class="text-right" nowrap>
                                                <?php
                                                    echo moneda($total_direct_cost_last); 
                                                ?>    
                                            </td>
                                            
                                            <td class="text-right" nowrap><?= moneda($total_percent_present); ?>%</td>
                                            <td class="text-right" nowrap>
                                                <?php
                                                    echo moneda($total_direct_cost_present); 
                                                ?>
                                            </td>
                                        </tr>

                                        <?php
                                            $total_general_cost_to_date = ($contract['general_costs'] * $total_percent_to_date) / 100;
                                            $total_general_cost_last = ($contract['general_costs'] * $total_percent_last) / 100;
                                            $total_general_cost_present = ($contract['general_costs'] * $total_percent_present) / 100;
                                        ?>
                                        <tr>
                                            <td>Gastos generales</td>
                                            <td class="text-right">
                                                <?= moneda($contract['general_costs']); ?>
                                            </td>
                                            <td class="text-right"><?= moneda($total_percent_to_date);?>%</td>
                                            <td class="text-right"><?= moneda($total_general_cost_to_date);?></td>
                                            <td class="text-right"><?= moneda($total_percent_last)?>%</td>
                                            <td class="text-right"><?= moneda($total_general_cost_last);?></td>
                                            <td class="text-right"><?= moneda($total_percent_present); ?>%</td>
                                            <td class="text-right"><?= moneda($total_general_cost_present);?></td>
                                        </tr>

                                        <?php
                                            $total_utilities_to_date = ($contract['utilities'] * $total_percent_to_date) / 100;
                                            $total_utilities_last = ($contract['utilities'] * $total_percent_last) / 100;
                                            $total_utilities_present = ($contract['utilities'] * $total_percent_present) / 100;
                                        ?>
                                        <tr>
                                            <td>Utilidades</td>
                                            <td class="text-right">
                                                <?= moneda($contract['utilities']); ?>  
                                            </td>
                                            <td class="text-right"><?= moneda($total_percent_to_date);?>%</td>
                                            <td class="text-right"><?= moneda($total_utilities_to_date);?></td>
                                            <td class="text-right"><?= moneda($total_percent_last)?>%</td>
                                            <td class="text-right"><?= moneda($total_utilities_last);?></td>
                                            <td class="text-right"><?= moneda($total_percent_present); ?>%</td>
                                            <td class="text-right"><?= moneda($total_utilities_present);?></td>
                                        </tr>

                                        <?php
                                            $total_to_date = $total_direct_cost_to_date + $total_general_cost_to_date + $total_utilities_to_date;
                                            $total_last = $total_direct_cost_last + $total_general_cost_last + $total_utilities_last;
                                            $total_present = $total_direct_cost_present + $total_general_cost_present + $total_utilities_present;
                                        ?>
                                        <tr class="success">
                                            <td>Total</td>
                                            <td class="text-right">
                                                <?= moneda($contract['costo_directo'] + $contract['general_costs'] + $contract['utilities']); ?>
                                            </td>
                                            <td class="text-right"></td>
                                            
                                            <td class="text-right">
                                                <?= moneda($total_to_date); ?>        
                                            </td>
                                            <td class="text-right"></td>
                                            <td class="text-right">
                                                <?= moneda($total_last); ?> 
                                            </td>
                                            <td class="text-right"></td>
                                            <td class="text-right">
                                                 <?= moneda($total_present); ?>         
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!--<pre>
        <?php //print_r($contract);?>
        </pre>-->

        <div class="row">
            <div class="col-sm-6">
                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <td>Valor trabajos efectuados a la fecha</td>
                        <td class="text-right">
                        <?php 
                            echo moneda($total_to_date);
                        ?>    
                        </td>
                    </tr>
                    <tr>
                        <td>Valor trabajos estado anterior</td>
                        <td class="text-right">
                            <?php
                                echo moneda($total_last); 
                            ?>    
                        </td>
                    </tr>
                    <tr>
                        <td>Valor Presente Estado de Pago</td>
                        
                        <td class="text-right">
                            <?php 
                                echo moneda($total_present);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Descuento devolución de Anticipo <?= $budget['advances']; ?>%</td>
                        <td class="text-right"><?= moneda(($budget['advances'] / 100) * $total_present); ?></td>
                    </tr>
                    <tr>
                        <td>Descuento por Retenciones <?= $budget['retentions']; ?>%</td>
                        <td class="text-right"><?= moneda(($budget['retentions'] / 100) * $total_present); ?></td>
                    </tr>
                    <!--<tr>
                        <td>Líquido a Pagar</td>
                        <td class="text-right"><?php //moneda($paymentStatement->liquid_pay); ?></td>
                    </tr>-->
                </table>

                <table class="table table-bordered">
                    <tr>
                        <td>Valor Neto presente Estado de Pago [<?= $budget->currencies_values[0]['currency']['name'] ?>]</td>
                        <td class="text-right">
                            <?php 
                                $net_value = ($total_present - (($budget['advances'] / 100) * $total_present) - (($budget['retentions'] / 100) * $total_present));
                                
                                echo moneda($net_value);  
                            ?>        
                        </td>
                    </tr>
                </table>

                <?php if( strtolower($budget->currencies_values[0]['currency']['name']) == 'peso' ): ?>
                        <table class="table table-hover table-striped table-bordered">
                            <tr>
                                <th>TOTAL NETO $</th>
                                <td class="text-right"><?= moneda($paymentStatement->total_net); ?></td>
                            </tr>
                            <tr>
                                <th>IVA 19%</th>
                                <td class="text-right"><?= moneda($paymentStatement->tax); ?></td>
                            </tr>
                            <tr>
                                <th>TOTAL</th>
                                <td class="text-right"><?= moneda($paymentStatement->total); ?></td>
                            </tr>
                        </table>
                        <p class="text-right">(Valores en pesos)</p>
                    <?php else: ?>
                    <!--<table class="table table-hover table-striped table-bordered">
                    <tr>
                        <th>Valor [<?php //$budget->currencies_values[0]['currency']['name'] ?>] al día Estado de Pago (<?php //$paymentStatement->presentation_date->format('d-m-Y'); ?>)</th>
                        <td class="text-right"><?php //moneda($paymentStatement->currency_value_to_date); ?></td>
                    </tr>
                    </table>-->
                        <table class="table table-hover table-striped table-bordered">
                            <tr class="warning">
                                <th>Valor [<?= $budget->currencies_values[0]['currency']['name'] ?>] al día Estado de Pago (<?= $paymentStatement->presentation_date->format('d-m-Y'); ?>)</th>
                                <td colspan="2" class="text-right"><?= moneda($paymentStatement->currency_value_to_date); ?></td>
                            </tr>
                            <tr>
                                <th></th>
                                <th class="text-right">En <?= strtoupper($budget->currencies_values[0]['currency']['name']); ?></th>
                                <th class="text-right">En CLP</th>
                            </tr>
                            <tr>
                                <th>TOTAL NETO $</th>
                                <th class="text-right"><?= moneda($net_value); ?></th>
                                <th class="text-right" data-toggle="tooltip" data-placement="left" title=" = <?= moneda($net_value); ?> X <?= moneda($paymentStatement->currency_value_to_date); ?>"><?= moneda($net_value * $paymentStatement->currency_value_to_date); ?></th>
                            </tr>
                            <tr>
                                <th>IVA 19%</th>
                                <th class="text-right"><?= moneda($paymentStatement->tax / $paymentStatement->currency_value_to_date); ?></th>
                                <th class="text-right" data-toggle="tooltip" data-placement="left" title=" = <?= moneda($paymentStatement->tax / $paymentStatement->currency_value_to_date); ?> X <?= ($paymentStatement->currency_value_to_date); ?>"><?= moneda($paymentStatement->tax); ?></th>
                            </tr>
                            <tr class="warning">
                                <th>TOTAL</th>
                                <th class="text-right"><?= moneda($paymentStatement->total / $paymentStatement->currency_value_to_date); ?></th>
                                <th class="text-right" data-toggle="tooltip" data-placement="left" title=" = <?= moneda($paymentStatement->total / $paymentStatement->currency_value_to_date); ?> X <?= ($paymentStatement->currency_value_to_date); ?>"><?= moneda($paymentStatement->total); ?></th>
                            </tr>
                        </table>
                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <table class="table table-hover table-striped table-bordered">
                    <?php $advance_contract_value_currency = round($paymentStatement->contract_value * ($budget['advances'] / 100)); ?>
                    <tr>
                        <td>Valor del Contrato</td>
                        <td><?= $budget->currencies_values[0]['currency']['name'] ?></td>
                        <td class="text-right"><?= moneda($paymentStatement->contract_value); ?></td>
                    </tr>
                    <tr>
                        <td>Anticipo <?= $budget['advances']; ?>%</td>
                        <td><?= $budget->currencies_values[0]['currency']['name'] ?></td>
                        <td class="text-right"><?= moneda($advance_contract_value_currency); ?></td>
                    </tr>
                    <tr>
                        <td>Pagado a la Fecha</td>
                        <td><?= $budget->currencies_values[0]['currency']['name'] ?></td>
                        <td class="text-right"><?= moneda($paymentStatement->paid_to_date); ?></td>
                    </tr>
                    <tr>
                        <td>Avance presente Estado de Pago</td>
                        <td><?= $budget->currencies_values[0]['currency']['name'] ?></td>
                        <td class="text-right"><?= moneda($total_present); ?></td>
                    </tr>
                    <tr>
                        <td>Saldo por pagar</td>
                        <td><?= $budget->currencies_values[0]['currency']['name'] ?></td>
                        <td class="text-right"><?= moneda($paymentStatement->contract_value - $advance_contract_value_currency - $paymentStatement->paid_to_date - $total_present); ?></td>
                    </tr>
                </table>

                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <th colspan="5" class="text-center">Resumen Retención y Devolución Anticipo (NETO)</th>
                    </tr>
                    <tr>
                        <th>Estado de Pago</th>
                        <th class="text-right">Retenciones</th>
                        <th class="text-right">Devolución<br>Anticipo X EP</th>
                        <th class="text-right">Acumulado Devol.<br>Anticipo</th>
                        <th class="text-right">Por Devolver</th>
                    </tr>
                    <?php
                        $total_retencion = 0;
                        $total_devolucion = 0;
                    ?>

                    <?php $x=0; $total_acumu = 0;?>
                    <?php foreach ($payment_statements as $payment_statement) : ?>
                        <tr>
                            <td><?= 'Estado Pago [' . $payment_statement->gloss.']'; ?></td>
                            <td class="text-right"><?= moneda($payment_statement->discount_retentions);?></td>
                            <td class="text-right"><?= moneda($payment_statement->discount_advances);?></td>
                            <td class="text-right">
                                <?php 
                                    if($x=0)
                                    {
                                        $total_acumu = $payment_statement->discount_advances;
                                        echo moneda($total_acumu);
                                    }
                                    else
                                    {
                                        $total_acumu += $payment_statement->discount_advances;
                                        echo moneda($total_acumu);
                                    }
                                    
                                    $x++;
                                ?>
                            </td>
                            <td class="text-right">
                                <?php //moneda($payment_statement->balance_due);?>
                                <?php echo moneda($advance_contract_value_currency - ($total_acumu)); ?>  
                            </td>
                        </tr>
                        <?php
                            $total_retencion += $payment_statement->discount_retentions;
                            $total_devolucion += $payment_statement->discount_advances;
                        ?>
                    <?php endforeach;?>
                    <tr class="warning">
                        <td>Total a la fecha UF</td>
                        <td class="text-right"><?= moneda($total_retencion);?></td>
                        <td class="text-right"><?= moneda($total_devolucion);?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <!--<table class="table table-hover table-striped table-bordered">
                    <tr>
                        <td colspan="2" class="text-center">Valor trabajos efectuados a la fecha (costo total)</td>
                    </tr>
                    <tr>
                        <td>Costos directos</td>
                        <td class="text-right"><?php //moneda($paymentStatement->total_direct_cost); ?></td>
                    </tr>
                    <tr>
                        <td>Proporcional de Gastos generales</td>
                        <td class="text-right"><?php //moneda( $contract['general_costs'] * $paymentStatement->total_direct_cost / $contract['costo_directo'] ); ?></td>
                    </tr>
                    <tr>
                        <td>Proporcional de Utilidades</td>
                        <td class="text-right"><?php //moneda( $contract['utilities'] * $paymentStatement->total_direct_cost / $contract['costo_directo'] ); ?></td>
                    </tr>
                    <tr>
                        <td>Costo total</td>
                        <td class="text-right"><?php //moneda($paymentStatement->total_cost); ?></td>
                    </tr>
                </table>
                
                <table class="table table-hover table-striped table-bordered">
                    <tr>
                        <td>Valor [<?php //$budget->currencies_values[0]['currency']['name'] ?>] fecha presupuesto <?php //$this->Time->format($budget->created,'dd-MM-YYYY'); ?></td>
                        <td class="text-right"><?php //moneda($budget->currencies_values[0]['value']) ?></td>
                    </tr>
                    <tr>
                        <td>Valor Neto presente Estado de Pago [<?php //$budget->currencies_values[0]['currency']['name'] ?>]</td>
                        <td class="text-right"><?php //moneda( round($paymentStatement->total_net / $paymentStatement->currency_value_to_date, 2) );  ?></td>
                    </tr>
                </table>-->
            </div>
        </div><!-- end .row -->
        <div class="row text-center">
            <!--<div class="col-sm-12">
            <?php /*if ($permisos['approve'] && !$noButtons): ?>
                <?= $this->Html->link(__('Aprobar'), '#', ['class' => 'btn btn-primary approve-btn']) ?>
            <?php endif ?>
            <?php if ($permisos['reject'] && !$noButtons): ?>
                <?= $this->Html->link(__('Rechazar'), '#', ['class' => 'btn btn-danger reject-btn']) ?>
            <?php endif ?>
                <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id' => $budget->building_id]],['class' => 'btn btn-flat btn-link']) */?>
            </div>-->

            <?php if($paymentStatement->draft == 0):?>
                <div class="col-sm-12">
                    <?php /*if ($permisos['approve'] && !$noButtons): ?>
                        <?= $this->Html->link(__('Aprobar'), '#', ['class' => 'btn btn-primary approve-btn']) ?>
                    <?php endif ?>
                    <?php if ($permisos['reject'] && !$noButtons): ?>
                        <?= $this->Html->link(__('Rechazar'), '#', ['class' => 'btn btn-danger reject-btn']) ?>
                    <?php endif ?>
                        <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id' => $budget->building_id]],['class' => 'btn btn-flat btn-link'])*/ ?>
                    <?php if($this->Access->verifyLevel(8) == true):?>
                        <?php if($paymentStatement->third_approval == 1):?>
                            <div class="alert alert-success" role="alert">Estado de pago: <strong>Aprobado por LDZ</strong></div>
                        <?php elseif($paymentStatement->second_approval == 1 && is_null($paymentStatement->third_approval)):?>
                            firma 2 ok
                            <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                            <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                        <?php elseif($paymentStatement->first_approval == 1 && is_null($paymentStatement->second_approval)):?>
                            firma 1 ok
                            <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                            <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                        <?php else:?>
                            <?= $this->Form->postLink(__('Aprobar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'approve'], [ 'confirm' => __('Estas seguro de aprobar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-success']) ?>
                            <?= $this->Form->postLink(__('Rechazar'), ['controller' => 'payment_statements', 'action' => 'change_approval', $paymentStatement->id, 'decline'], [ 'confirm' => __('Estas seguro de rechazar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-sm btn-danger']) ?>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            <?php else:?>
                <div class="col-sm-12">
                    <div class="alert alert-info" role="alert" style="font-size:18px;"><i class="glyphicon glyphicon-pencil" style="font-size:35px;"></i><br><strong>Este estado de pago no ha sido enviado para su aprobación ya esta en modo borrador.</strong></div>
                    <?= $this->Form->postLink(__('Enviar para aprobación'), ['controller' => 'payment_statements', 'action' => 'accept', $paymentStatement->id], [ 'confirm' => __('Estas seguro de enviar el estado [{0}] para aprobación?', $paymentStatement->gloss), 'class' => 'btn btn-lg btn-primary']) ?>
                    <?= $this->Html->link('Editar', '/payment_statements/edit/'.$paymentStatement->id, ['class' => 'btn btn-lg btn-warning']); ?>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="showAgree" tabindex="-1" role="dialog" aria-labelledby="myAgree">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-button"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Enviar Estado de Pago</h4>
            </div>
            <div class="modal-body" id="body-prev">
                <p>¿Está seguro en enviar el Estado de Pago al correo ingresado?</p>
            </div>
            <div class="modal-body" id="body-load" style="display:none;">
                <div class="row">
                    <div class="col-sm-4">
                        <?= $this->Html->image('hourglass.svg'); ?>
                    </div>
                    <div class="col-sm-8" class="text-center">
                        <?= $this->Html->tag('h5', __('Estamos generando los archivos necesarios para enviar el correo electrónico, por favor espera un momento...'));?>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer" id="foot-buttons">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-enviar">Sí, enviar</button>
            </div>
        </div>
    </div>
</div>

<?php if(is_null($paymentStatement->client_approval) && $paymentStatement->email_sent == 1):?>
    <div class="modal fade" id="client_approval" tabindex="-1" role="dialog" aria-labelledby="myAgree">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?= $this->Form->create(null,['url'=>['controller'=>'payment_statements','action'=>'clientApproval'],'id'=>'client_app']); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Estado de Pago Aprobado - Cliente</h4>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de pasar el estado de pago al Estado aprobado por Cliente</p>
                    <?= $this->Form->hidden('id',['value'=>$id]); ?>
                    <?= $this->Form->hidden('action',['value'=>'approve']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-enviar">Sí, aprobado por cliente</button>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="client_reject" tabindex="-1" role="dialog" aria-labelledby="myAgree">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?= $this->Form->create(null,['url'=>['controller'=>'payment_statements','action'=>'clientApproval'],'id'=>'clien_rej']); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Estado de Pago Rechazado - Cliente</h4>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de pasar el estado de pago al Estado rechazado por Cliente</p>
                    <?= $this->Form->hidden('id',['value'=>$id]); ?>
                    <?= $this->Form->hidden('action',['value'=>'decline']); ?>
                    <?= $this->Form->input('obs',['type'=>'textarea', 'rows' => 5, 'placeholder'=>'Escribe la razón por la cual se rechaza el estado de pago...', 'label' => 'Observación', 'required' => 'required']); 
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-enviar">Sí, rechazado por cliente</button>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
<?php endif;?>

<?php $this->start('script'); ?>
<?= $this->Html->script('payment_statements.approved_or_proyected'); ?>
<?= $this->Html->script('payment_statements.view'); ?>
<?php $this->end(); ?>


<?php //debug($paymentStatement);?>