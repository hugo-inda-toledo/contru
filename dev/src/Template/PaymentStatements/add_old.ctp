<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Estado de Pago'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
?>

<div class="super-content">
    <?= $this->Form->create($paymentStatement); ?>
    <div class="panel panel-material-blue-grey-700">    
        <div class="panel-heading">
            <h3 class="panel-title">Nuevo estado de Pago</h3>
        </div><!-- end .panel-heading -->
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">                          
                        <?= $this->Form->input('gloss',['type'=>'text','label'=>'Glosa del Estado de Pago','placeholder'=>'Ingrese glosa de Estado de Pago']) ?>
                        <?= $this->Form->input('presentation_date',['type'=>'text','data-type'=>'datepick','label'=>'Presentación estimada','placeholder'=>'Ingrese fecha']) ?>
                        <?= $this->Form->input('billing_date',['type'=>'text','data-type'=>'datepick','label'=>'Cuando se Factura','placeholder'=>'Ingrese fecha']) ?>                    
                        <?= $this->Form->input('estimation_pay_date',['type'=>'text','data-type'=>'datepick','label'=>'Pago estimado','placeholder'=>'Ingrese fecha']) ?>                    
                </div>
                <div class="col-sm-6">
                    <h4>Datos Generales de la Obra</h4>
                    <table class="table table-hover table-striped">
                        <tr>
                            <th>Obra</th>
                            <td><?php echo $sf_building->DesArn; ?></td>
                        </tr>
                        <tr>
                            <th>Presupuesto</th>
                            <td>Aprobado/Rechazado</td>
                        </tr>
                        <tr>
                            <th>Mandante</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Dirección</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Admin. Obra</th>
                            <td><?php echo $admin_obra ?></td>
                        </tr>
                        <tr>
                            <th>Visitador</th>
                            <td><?php echo $visitador ?></td>
                        </tr>
                        <tr>
                            <th>Inicio de Obra</th>
                            <td></td>
                        </tr>
                    </table>                
                </div>
            </div>

            <?php $unidad_moneda = $budget['currencies_values'][0]['currency']['name']; ?>
            <?php $edp = ['suma_item'=>0,'per_ant'=>0,'per_act'=>0]; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Lista de Ítems Presupuesto</h4>
                    <table class="table table-hover">
                        <tr>
                            <th>Ítem</th>
                            <th>Descripción</th>
                            <th>Total [<?= $unidad_moneda; ?>]</th>
                            <th>Avance a la Fecha [%]</th>
                            <th>Avance E.D.P anterior [%]</th>
                            <th>Avance presente E.D.P [%]</th>
                            <th>Monto [<?= $unidad_moneda; ?>]</th>
                        </tr>
                        <?php foreach ($budget_items as $bi): ?>                    
                            <?php echo $this->element('budget_items_edp_add',['bi' => $bi,'budget'=> $budget,'edp' =>'edp']) ?>
                        <?php endforeach ?>    

                    </table>
                </div>
            </div>
            <?= $this->Form->button('Siguiente') ?>    
            <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id'=>$budget->building_id]], ['class' => 'btn btn-flat btn-link']) ?>
            <?= $this->Form->end(); ?>
        </div><!-- end .panel-body -->      
    </div><!-- end .panel -->
</div><!-- end .super-content -->

