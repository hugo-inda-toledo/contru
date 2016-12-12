<style>
    .dropdown-menu-left {
        right: auto;
        left: -93px;
    }
</style>
<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Estados de pago'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de estados de pago
        </h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-sm-6">
                <?php if ($this->request->session()->read('Auth.User.group_id') != USR_GRP_ADMIN_OBRA && $this->request->session()->read('Auth.User.group_id') != USR_GRP_ASIS_RRHH &&
                  $this->request->session()->read('Auth.User.group_id') != USR_GRP_OFI_TEC) :
                  echo $this->Element('building_filter'); // coloca un menu
               endif; ?>
            </div>

            <?php if (!empty($paymentStatements)): ?>

                <?php if(!is_null($paymentStatements[0]->client_approval)):?>
                    <div class="col-sm-6">
                        <?= $this->Html->link(__('Nuevo estado de pago'), ['action' => 'add', $budget->id],['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
                    </div>
                <?php endif;?>

            <?php else:?>

                <div class="col-sm-6">
                    <?= $this->Html->link(__('Nuevo estado de pago'), ['action' => 'add', $budget->id],['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
                </div>

            <?php endif;?>
        </div>

       <!-- Panel content -->
       <br>

        <?php if (!empty($paymentStatements)): ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Estado</th>
                        <th class="text-center" colspan="2">Avance a la Fecha</th>
                        <th class="text-center" colspan="2">Avance EP anterior</th>
                        <th class="text-center" colspan="2">Avance presente EP</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th class="text-right"><span>[%]</span></th>
                        <th class="text-right"><span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                        <th class="text-right"><span>[%]</span></th>
                        <th class="text-right"><span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                        <th class="text-right"><span>[%]</span></th>
                        <th class="text-right"><span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span></th>
                        <th></th>
                    </tr>
                </thead>
                <?php foreach ($paymentStatements as $payment_statement) : ?>
                    <tbody>
                        <tr>
                            <td><?= 'Estado Pago ['.$payment_statement->gloss.']'; ?></td>
                            <td>
                                <?php  
                                    if($payment_statement->draft == 1)
                                    {
                                        echo 'Borrador';
                                    }
                                    else
                                    {  
                                        if($payment_statement->client_approval == 1)
                                        {
                                            echo 'Aprobado por el cliente';
                                        }
                                        elseif($payment_statement->email_sent == 1 && $payment_statement->third_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->first_approval == 1 && is_null($payment_statement->client_approval))
                                        {
                                            echo 'Enviado a Cliente';
                                        }
                                        elseif($payment_statement->client_approval == 0 && $payment_statement->third_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->first_approval == 1 && $payment_statement->email_sent == 1)
                                        {
                                            echo 'Rechazado por el cliente';
                                        }
                                        elseif($payment_statement->third_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->first_approval == 1)
                                        {
                                            echo 'Aprobado por LDZ';
                                        }
                                        elseif($payment_statement->second_approval == 1 && is_null($payment_statement->third_approval))
                                        {
                                            echo 'Esperando aprobación del Gerente General';
                                        }
                                        elseif($payment_statement->first_approval == 1 && is_null($payment_statement->second_approval))
                                        {
                                            echo 'Esperando aprobación del Gerente de Finanzas';
                                        }
                                        elseif(is_null($payment_statement->first_approval))
                                        {
                                             echo 'Esperando aprobación del Visitador';
                                        }

                                    } 
                                ?>        
                            </td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_to_date) ?>%</td>
                            <td class="text-right"><?= moneda( $payment_statement->progress_present_payment_statement ); ?></td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_last) ?>%</td>
                            <td class="text-right"><?= moneda( $payment_statement->paid_to_date ); ?></td>
                            <td class="text-right"><?= moneda($payment_statement->total_percent_present); ?>%</td>
                            <td class="text-right"><?= moneda( $payment_statement->total_cost ); ?></td>
                            <td class=" text-center actions">
                                <div class="btn-group">
                                    <?= $this->Html->link('Ver', ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id],['class'=>'btn btn-warning btn-sm']) ?>
                                    <a href="#opciones" data-target="#" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                        
                                        <?php if($payment_statement->draft == 0):?>
                                            
                                            <!-- si es gerente  -->
                                            <?php if ($permisos['gerentes']) : ?>
                                                <li>
                                                    <!-- Si esta en espera de aprobacion -->
                                                    <?php
                                                    /*if (in_array($payment_statement->payment_statement_state_id, [2, 3])) :
                                                        echo $this->Html->link(__('Aprobar/Rechazar'), ['controller' => 'payment_statements', 'action' => 'approved_or_rejected', $payment_statement->id]);
                                                        //
                                                        //Si esta aprobado
                                                        elseif ($payment_statement->payment_statement_state_id == 4) :
                                                            echo $this->Html->link(__('Enviar Estado de Pago'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id,'enviar']);
                                                            // Cliente aprueba o rechaza EDP
                                                        elseif ($payment_statement->payment_statement_state_id == 6) :
                                                            echo $this->Html->link(__('Aprobado/Rechazado Cliente'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id,
                                                               'aprobar_rechazar_cliente']);
                                                    endif;*/ ?>
                                                    <?php if(is_null($payment_statement->client_approval) && $payment_statement->first_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->third_approval == 1 && $payment_statement->email_sent == 1):?>
                                                            <?= $this->Html->link(__('Aprobar / Rechazar por el cliente'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id]);?>
                                                    <?php elseif(is_null($payment_statement->email_sent) && $payment_statement->first_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->third_approval == 1):?>
                                                        <?= $this->Html->link(__('Enviar Estado de Pago'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id]);?>
                                                    <?php elseif($payment_statement->second_approval == 1 && is_null($payment_statement->third_approval)):?>
                                                        <?php if($this->Access->verifyAccessByKeyword('gerente_general') == true):?>
                                                            <?= $this->Html->link(__('Aprobar / Rechazar'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id]) ?>
                                                        <?php endif;?>
                                                    <?php elseif($payment_statement->first_approval == 1 && is_null($payment_statement->second_approval)):?>
                                                        <?php if($this->Access->verifyLevel(8) == true || $this->Access->verifyAccessByKeyword('gerente_finanzas') == true):?>
                                                            <?= $this->Html->link(__('Aprobar / Rechazar'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id]) ?>
                                                        <?php endif;?>
                                                    <?php elseif(is_null($payment_statement->first_approval)):?>
                                                        <?php if($this->Access->verifyLevel(8) == true || $this->Access->verifyAccessByKeyword('visitador') == true):?>
                                                            <?= $this->Html->link(__('Aprobar / Rechazar'), ['controller' => 'payment_statements', 'action' => 'view', $payment_statement->id]) ?>
                                                        <?php endif;?>
                                                    <?php endif;?>
                                                </li>
                                            <?php endif ?>
                                            <li>
                                                <?= $this->Html->link(__('Comentarios'), ['controller' => 'payment_statements', 'action' => 'comment', $payment_statement->id]) ?>
                                            </li>
                                            <li>
                                                <?= $this->Html->link(__('Ver Historial'), ['controller' => 'payment_statements', 'action' => 'history', $payment_statement->id]) ?>
                                            </li>
                                            
                                            <?php //if (!empty($paymentStatements)): ?>
                                                <?php //if($paymentStatements[0]->payment_statement_state->name != 'Aprobado'):?>
                                                    <!--<li class="divider"></li>
                                                    <li>
                                                        <?php //$this->Form->postLink(__('Eliminar'), ['controller' => 'payment_statements', 'action' => 'delete', $payment_statement->id], [ 'confirm' => __('Estas seguro de eliminar el estado #{0}?', $payment_statement->id)]) ?>
                                                    </li>-->
                                                <?php //endif;?>
                                            <?php //endif;?>
                                        <?php else:?>
                                            <li>
                                                <?= $this->Html->link('Editar', ['controller' => 'payment_statements', 'action' => 'edit', $payment_statement->id]) ?>
                                            </li>
                                            <li>
                                                <?= $this->Form->postLink(__('Enviar para aprobación'), ['controller' => 'payment_statements', 'action' => 'accept', $payment_statement->id], [ 'confirm' => __('Estas seguro de enviar el estado #{0} para aprobación?', $payment_statement->id)]) ?>
                                                
                                            </li>
                                            <li class="divider"></li>
                                            <li><?= $this->Form->postLink(__('Eliminar'), ['controller' => 'payment_statements', 'action' => 'delete', $payment_statement->id], [ 'confirm' => __('Estas seguro de eliminar el estado de pago #{0}?', $payment_statement->id)]) ?></li>
                                        <?php endif;?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <h4>No hay estados de pago disponibles</h4>
        <?php endif ?>

    </div>
</div>

<?php //debug($paymentStatements);?>
