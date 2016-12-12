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
        <h3 class="panel-title">Historial de cambios del Estado de Pago</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <div class="row text-center">
            <div class="col-sm-12">
                <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id' => $budget->building_id]],['class' => 'btn btn-flat btn-link']) ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Estados de Pago de la Obra</h4>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <?php if(!empty($payment_statements)){ ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Versión</th>
                                        <th>Usuario</th>
                                        <th>Fecha Edición</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payment_statements AS $ps){ ?>
                                        <tr>
                                            <td><?=$ps['version_number']?></td>
                                            <td><?=$ps->user->first_name.' '.$ps->user->lastname_m.' '.$ps->user->lastname_f; ?></td>
                                            <td><?=$ps['created']->format('d-m-Y')?></td>
                                            <td><?=$this->Html->link('Ver', ['action' => 'view', $ps->id],['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle'])?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <p>No se encontraron versiones anteriores</p>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>