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
        <h3 class="panel-title">Editar Asistencia de Trabajadores</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?php
        if (!empty($reject)) :
            echo '<div class="well well-sm"><p class="text-danger">Rechazado: ';
            echo $reject['comment'] . ' | Fecha: ' . $reject['created']->nice();
            echo '</p></div>';
        endif; ?>
        <?= $this->Form->create(null, [
            'url' => ['controller' => 'Assists', 'action' => 'edit', $budget_id, $assistance_date->format('Y-m-d')],
            'class' => 'sendAssists'
        ]); ?>
        <h4>
            Fecha Asistencia: <strong><?php //echo $assistance_date->nice(); ?><?= convertMonthToSpanish($assistance_date->format('l, j F Y')) ?></strong>
        </h4>
        <table class="table table-striped table-hover table-item">
            <col width="2%">
            <col width="25%">
            <col width="10%">
            <col width="10%">
            <col width="20%">
            <col width="20%">
            <col width="15%">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Nombre Trabajador</th>
                    <th class="text-right">Rut</th>
                    <th>Cargo</th>
                    <th>Asistencia</th>
                    <th>Falla</th>
                    <th class="title_detail"></th>
                    <th>Horas Extras</th>
                    <th>Horas Atraso</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $c=0;
                foreach ($workers as $worker) :
                    $c++;
                ?>
                    <tr>
                        <td>
                            <?= $this->Form->hidden('Assists.'.$assists[$worker['ficha']]['id'].'.assist_id', ['value' => $assists[$worker['ficha']]['id']]) ?>
                            <?= $this->Form->hidden('Assists.'.$assists[$worker['ficha']]['id'].'.worker_id', ['value' => $assists[$worker['ficha']]['worker_id']]) ?>
                            <?= $c;?>
                        </td>
                        <td><?= $worker['nombres'] ?></td>
                        <td nowrap class="text-right"><?= $worker['rut'] ?></td>
                        <td><?= $worker['Cargo']['nombre_cargo']; ?></td>
                        <?php
                        $half_day_info = array();
                        $half_day = false;
                        $select_disabled = false;
                        $assist = false;
                        $assist_disabled = false;
                        if(isset($assists[$worker['ficha']])){
                            $assist_id = $assists[$worker['ficha']]['id'];
                            foreach ($assists[$worker['ficha']]['assist_types'] as $key => $assist_type) :
                                if (count($assists[$worker['ficha']]['assist_types']) > 1) :
                                    // pr($assist_type);
                                    $half_day = true;
                                    $assist = true; // asistencia
                                    $select_disabled = true; // estado de select
                                    $assist_disabled = false; // estado de asistencia
                                    // Dejar como 0 la asistencia y como 1 la falla
                                    ($assist_type['id'] == 1) ? $half_day_info[$key]['assist_disabled'] = false :  $half_day_info[$key]['assist_disabled'] = true;
                                    ($assist_type['id'] == 1) ? $half_day_info[$key]['select_disabled'] = true :  $half_day_info[$key]['select_disabled'] = false;
                                    ($assist_type['id'] == 1) ? $half_day_info[$key]['assist'] = true :  $half_day_info[$key]['assist'] = false;
                                    $half_day_info[$key]['assist_type_id'] = $assist_type['_joinData']['assist_type_id'];
                                    $half_day_info[$key]['hours'] = $assist_type['_joinData']['hours'];
                                else :
                                    $assist = true; // asistencia
                                    $select_disabled = true; // estado de select
                                    $assist_disabled = false; // estado de asistencia
                                    ($assist_type['id'] == 1) ? $assist_disabled = false :  $assist_disabled = true;
                                    ($assist_type['id'] == 1) ? $select_disabled = true :  $select_disabled = false;
                                    ($assist_type['id'] == 1) ? $assist = true :  $assist = false;
                                endif;
                            endforeach;
                            if ($half_day) { //datos para media jornada
                            ?>
                                <td class="half_day">
                                    <?= $this->Form->checkbox('Assists.'.$assists[$worker['ficha']]['id'].'.all_day', ['checked' => false, 'hiddenField' => false, 'label-data' => 'Todo el día', 'data-all_day' => 1]); ?>
                                    <?= $this->Form->checkbox('Assists.'.$assists[$worker['ficha']]['id'].'.assistance', ['disabled' => true, 'checked' => false, 'hiddenField' => false, 'label-data' => 'Asistió', 'data-assistance' => 1, 'display-data' => 'none']); ?>
                                </td>
                                <td>
                                    <?php //echo $this->Form->input('Assists.'.$assists[$worker['ficha']]['id'].'.assist_type_id', ['required' => false, 'options' => $assist_types, 'label' => false, 'empty' => 'Seleccione una opción', 'data-assist_type' => 1, 'display-data' => 'none']); ?>
                                    <?= $this->Form->input('Assists.'.$assists[$worker['ficha']]['id'] . '.assist_type_id', ['disabled' => $half_day_info[1]['select_disabled'], 'required' => $half_day_info[1]['select_disabled'], 'options' => $assist_types, 'label' => false, 'empty' => 'Seleccione una opción', 'data-assist_type' => 1, 'value' => $half_day_info[1]['assist_type_id']]); ?>
                                </td>
                                <td class="half-day">
                                    <div class="form-inline">
                                        <span>Asistió:</span>
                                        <?= $this->Form->hidden('Assists.'.$assists[$worker['ficha']]['id'].'.half_day.0.assistance', ['value' => true, 'data-assistance' => 1]); ?>
                                        <div class="form-group">
                                            <label class="control-label" style="margin-left:10px;">Horas: </label>
                                            <input type="number" name="Assists[<?= $assists[$worker['ficha']]['id']?>][half_day][0][hours]" class="form-control" id="worker-<?= $assists[$worker['ficha']]['id']?>-0-hours" placeholder="0" min="1" max="8" data-hours="1" value="<?= $half_day_info[0]['hours']?>">
                                        </div>
                                        <br>
                                    </div>
                                    <div class="form-inline">
                                        <span style="padding-right:12px;">Faltó:</span>
                                        <div class="form-group">
                                            <label class="control-label" style="margin-left:10px;">Horas: </label>
                                            <input type="number" name="Assists[<?= $assists[$worker['ficha']]['id']?>][half_day][1][hours]" class="form-control" id="worker-<?= $assists[$worker['ficha']]['id']?>-1-hours"
                                             placeholder="0" min="1" max="8" data-hours="1" value="<?= $half_day_info[1]['hours']?>">
                                        </div>
                                        <!-- <br> -->
                                        <!-- <label class="control-label" style="margin-left:10px;">Falla: </label> -->
                                        <?php //echo $this->Form->input('Assists.'.$assists[$worker['ficha']]['id'] . '.half_day.1.assist_type_id', ['disabled' => $half_day_info[1]['select_disabled'], 'required' => $half_day_info[1]['select_disabled'], 'options' => $assist_types, 'label' => false, 'empty' => 'Seleccione una opción', 'data-assist_type' => 1, 'value' => $half_day_info[1]['assist_type_id']]); ?>
                                    </div>
                                </td>
                                <td><?= $this->Form->input('Assists.'.$assists[$worker['ficha']]['id'].'.overtime', ['disabled' => false, 'value' => $assists[$worker['ficha']]['overtime'],
                                    'label' => false, 'min' => 0, 'max' => 10, 'data-overtime' => 1]) ?></td>
                                <td><?= $this->Form->input('Assists.'.$assists[$worker['ficha']]['id'].'.delay', ['disabled' => false, 'value' => $assists[$worker['ficha']]['delay'],
                                    'label' => false, 'min' => 0, 'max' => 10, 'data-delay' => 1]) ?></td>
                            <?php
                            }else{
                                $assist_type_id = (count($assists[$worker['ficha']]['assist_types']) == 1)?$assists[$worker['ficha']]['assist_types'][0]['id']:'';
                            ?>
                                <td class="no_half_day">
                                    <?= $this->Form->checkbox('Assists.'.$assist_id.'.all_day', ['checked' => true, 'hiddenField' => false,
                                     'label-data' => 'Todo el día', 'data-all_day' => 1]); ?>
                                    <?= $this->Form->checkbox('Assists.'.$assist_id.'.assistance', ['checked' => $assist, 'hiddenField' => false,
                                     'label-data' => 'Asistió', 'data-assistance' => 1]); ?>
                                </td>
                                <td>
                                    <?= $this->Form->input('Assists.'.$assist_id.'.assist_type_id', ['disabled' => $select_disabled, 'options' => $assist_types, 'label' => false,
                                     'empty' => 'Seleccione una opción', 'data-assist_type' => 1, 'value' => $assist_type_id]); ?>
                                </td>

                                <td class="half-day">
                                    <div style="display:none;" class="half-day">
                                        <div>
                                            <span>Asistió:</span>
                                            <?= $this->Form->hidden('Assists.'.$assist_id.'.half_day.0.assistance', ['value' => true, 'data-assistance' => 1]); ?>
                                            <div class="form-group">
                                                <label class="control-label" style="margin-left:10px;">Horas: </label>
                                                <input disabled="disabled" type="number" name="Assists[<?= $assist_id?>][half_day][0][hours]" class="form-control" id="worker-<?= $worker['ficha']?>-0-hours"
                                                 placeholder="0" min="1" max="8" data-hours="1">
                                            </div>
                                        </div>
                                        <div class="form-inline">
                                            <span style="padding-right:12px;">Faltó:</span>
                                            <div class="form-group">
                                                <label class="control-label" style="margin-left:10px;">Horas: </label>
                                                <input disabled="disabled" type="number" name="Assists[<?= $assist_id?>][half_day][1][hours]" class="form-control" id="worker-<?= $worker['ficha']?>-1-hours" placeholder="0" min="1" max="8" data-hours="1">
                                            </div>
                                            <?php //echo $this->Form->input('Assists.'.$assist_id.'.half_day.1.assist_type_id', ['disabled' => true, 'required' => false, 'options' => $assist_types, 'label' => false, 'empty' => 'Seleccione una opción', 'data-assist_type' => 1]); ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $this->Form->input('Assists.'.$assist_id.'.overtime', ['disabled' => $assist_disabled, 'value' => $assists[$worker['ficha']]['overtime'], 'label' => false, 'min' => 0, 'max' => 10, 'data-overtime' => 1]); ?> </td>
                                <td><?= $this->Form->input('Assists.'.$assist_id.'.delay', ['disabled' => $assist_disabled, 'value' => $assists[$worker['ficha']]['delay'], 'label' => false, 'min' => 0, 'max' => 10, 'data-delay' => 1]); ?> </td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            echo $this->Form->hidden('budget_id', ['value' => $budget_id]);
            echo $this->Form->hidden('assistance_date', ['value' => $assistance_date]);
            echo $this->Form->button(__('Guardar'));
            $group_id = $this->request->session()->read('Auth.User.group_id');
            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']);
            echo $this->Form->end();
        ?>
    </div>
</div>
<?= $this->Html->script('assists.edit'); ?>
