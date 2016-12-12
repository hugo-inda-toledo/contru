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
        <h3 class="panel-title">Ver Asistencia de Trabajadores</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?php
        if (!empty($approval)) :
            echo '<div class="well well-sm"><p class="text-success">';
            echo $approval['comment'] . ' | Fecha: ' . $approval['created']->nice();
            echo '</p></div>';
            if (!empty($reject)) :
                echo '<div class="shadow-z-1"><p class="text-danger">Rechazado: ';
                echo $reject['comment'] . ' | Fecha: ' . $reject['created']->nice();
                echo '</p></div>';
            endif;
        elseif (!empty($reject)) :
            echo '<div class="well well-sm"><p class="text-danger">Rechazado: ';
            echo $reject['comment'] . ' | Fecha: ' . $reject['created']->nice();
            echo '</p></div>';
            if ($this->request->session()->read('Auth.User.group_id') == USR_GRP_JEFE_RRHH) :
                echo $this->Form->postLink(__('Aprobar Asistencia'),
                 ['action' => 'approve'],
                 ['data' => ['budget_id' => $budget_id, 'assistance_date' => $assistance_date->format('Y-m-d')],
                  'class' => 'btn btn-sm btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar el trabajo realizado?')]);
            endif;
        else :
            if ($this->request->session()->read('Auth.User.group_id') == USR_GRP_JEFE_RRHH) :
                echo $this->Form->postLink(__('Aprobar Asistencia'),
                 ['action' => 'approve'],
                 ['data' => ['budget_id' => $budget_id, 'assistance_date' => $assistance_date->format('Y-m-d')],
                  'class' => 'btn btn-sm btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar la Asistencia?')]);
                echo $this->Html->link(__('Rechazar Asistencia'), '#', ['data-toggle' => 'modal', 'data-target' => '#' . $assistance_date->format('Y-m-d'),
                  'class' => 'btn btn-sm btn-danger']);
                echo $this->Element('reject_assist_modal', ['budget_id' => $budget_id, 'assistance_date' => $assistance_date->format('Y-m-d')]);
            endif;
        endif; ?>
        <h4>
            Fecha Asistencia: <strong><?php //echo $assistance_date->nice(); ?><?= convertMonthToSpanish($assistance_date->format('l, j F Y')) ?></strong>
        </h4>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>°</th>
                    <th>Nombre Trabajador</th>
                    <th class="text-right">Rut</th>
                    <th>Cargo</th>
                    <th>Asistencia</th>
                    <th>Horas extra</th>
                    <th>Horas Atraso</th>
                </tr>
            </thead>
            <tbody>
                <?php $c=0; foreach ($workers as $worker) : $c++;?>
                    <tr>
                        <td><?=$c;?></td>
                        <td><?= $worker['nombres'] ?></td>
                        <td style="white-space: nowrap;" class="text-right"><?= $worker['rut'] ?></td>
                        <td><?= $worker['Cargo']['nombre_cargo'] ?></td>
                        <td>
                            <?php
                            if((count($assists[$worker['ficha']]['assist_types']) == 1)){
                                foreach ($assists[$worker['ficha']]['assist_types'] as $assist_type) :
                                    echo $assist_type['name'] . ': ';
                                    if ($assist_type['_joinData']['assist_type_id'] != 2 && $assist_type['_joinData']['assist_type_id'] != 6) :
                                        echo '<span class="label label-success pull-right">' . $assist_type['_joinData']['hours'] . ' horas</span><br>';
                                    else :
                                        echo '<span class="label label-danger pull-right"> ' . $assist_type['_joinData']['hours'] . ' horas</span><br>';
                                    endif;
                                endforeach ;
                            } ?>
                        </td>
                        <td><?= $assists[$worker['ficha']]['overtime'] ?></td>
                        <td><?= $assists[$worker['ficha']]['delay'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            $group_id = $this->request->session()->read('Auth.User.group_id');
            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             $this->Html->link(__('Volver'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
             $this->Html->link(__('Volver'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']); ?>
    </div>
</div>
