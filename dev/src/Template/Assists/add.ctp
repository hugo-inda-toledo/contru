<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Recursos Humanos'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ingresar Asistencia de Trabajadores</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <h4>Fecha Asistencia: <strong><?= convertMonthToSpanish($assistance_date->format('l, j F Y')) ?></strong></h4>
        <?= $this->Form->create($assist, ['class' => 'sendAssists']); ?>

        <?= $this->Html->link(__('Seleccionar todos'), ['action' => '#'], ['id' => 'marcartodos', 'class' => 'btn-sm btn-primary pull-right']); ?>

        <table class="table table-striped table-hover table-item">
            <col width="2%">
            <col width="30%">
            <col width="10%">
            <col width="10%">
            <col width="20%">
            <col width="20%">
            <col width="10%">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Nombre Trabajador</th>
                    <th class="text-right">Rut</th>
                    <th>Cargo</th>
                    <th>Asistencia</th>
                    <th>Falla</th>
                    <th style="display: none;"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $c=0;
                foreach ($workers as $worker) :
                    $c++; ?>
                    <tr>
                        <td><?php
                            echo $this->Form->hidden('Worker.'.$worker['ficha'], ['value' => $worker['ficha']]);
                            echo $c;
                        ?></td>
                        <td><?= $worker['nombres'] ?></td>
                        <td nowrap class="text-right"><?= $worker['rut'] ?></td>
                        <td><?= $worker['Cargo']['nombre_cargo'] ?></td>
                        <td>
                            <?= $this->Form->checkbox('Worker.'.$worker['ficha'].'.all_day', ['checked' => true, 'hiddenField' => false, 'label-data' => 'Todo el día', 'data-all_day' => 1]); ?>
                            <?= $this->Form->checkbox('Worker.'.$worker['ficha'].'.assistance', ['checked' => false, 'hiddenField' => false, 'label-data' => 'Asistió', 'data-assistance' => 1]); ?>
                        </td>
                        <td>
                            <?= $this->Form->input('Worker.'.$worker['ficha'].'.assist_type_id', ['required' => true, 'options' => $assist_types, 'label' => false,
                             'empty' => 'Seleccione una opción', 'data-assist_type' => 1]); ?>
                        </td>
                        <td class="half-day" style="display:none;">
                            <div class="form-inline">
                                <span>Asistió:</span>
                                <?= $this->Form->hidden('Worker.'.$worker['ficha'].'.0.assistance', ['value' => true, 'data-assistance' => 1]); ?>
                                <div class="form-group">
                                    <label class="control-label" style="margin-left:10px;">Horas: </label>
                                    <input disabled="disabled" type="number" name="Worker[<?= $worker['ficha']?>][0][hours]" class="form-control" id="worker-<?= $worker['ficha']?>-0-hours"
                                     placeholder="0" min="1" max="8" data-hours="1">
                                </div>
                            </div>
                            <div class="form-inline">
                                <span style="padding-right:12px;">Faltó:</span>
                                <div class="form-group">
                                    <label class="control-label" style="margin-left:10px;">Horas: </label>
                                    <input disabled="disabled" type="number" name="Worker[<?= $worker['ficha']?>][1][hours]" class="form-control" id="worker-<?= $worker['ficha']?>-1-hours" placeholder="0" min="1" max="8" data-hours="1">
                                </div>
                                <!-- <label class="control-label" style="margin-left:10px;">Falla: </label> -->
                                <?= $this->Form->input('Worker.'.$worker['ficha'].'.1.assist_type_id', ['disabled' => true, 'required' => false, 'options' => $assist_types, 'label' => false,
                                 'empty' => 'Seleccione una opción', 'data-assist_type' => 1]); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            echo $this->Form->hidden('budget_id', ['value' => $budget_id]);
            echo $this->Form->button(__('Guardar'));
            $group_id = $this->request->session()->read('Auth.User.group_id');
            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']);
            echo $this->Form->end();
        ?>
    </div>
</div>
<?= $this->Html->script('assists.add'); ?>
