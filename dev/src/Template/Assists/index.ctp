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
        <h3 class="panel-title">Lista de Asistencias de Trabajadores</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?php if(!empty($budget)) echo $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-lg-6">
            <?php
            $group_id = $this->request->session()->read('Auth.User.group_id');
            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) : ?>
                <?php echo $this->Form->create('Budgets', ['class' => 'form-horizontal', 'type' => 'get']); ?>
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <?php echo $this->Form->input('months', ['label' => 'Mes', 'empty' => 'Seleccione un mes', 'options' => $months,
                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : '')]); ?>
                        </div>
                        <div class="col-lg-6">
                            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
            <?php else : ?>
                <?php echo $this->Form->create('Budgets', ['class' => 'form-horizontal', 'type' => 'get']); ?>
                    <div class="col-lg-12">
                        <!-- <div class="col-lg-6"> -->
                            <?php
                            $currentValue = (!empty($budget))?$budget->building_id:'';
                            echo $this->Form->hidden('building_id', ['value' => $currentValue]); ?>
                        <!-- </div> -->
                        <div class="col-lg-4">
                            <?php echo $this->Form->input('months', ['label' => 'Mes', 'empty' => 'Seleccione una Fecha', 'options' => $months,
                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : '')]); ?>
                        </div>
                        <div class="col-lg-2">
                            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
                        </div>
                    </div>
                <?= $this->Form->end(); ?>
           <?php endif; ?>
            </div>
            <div class="col-lg-6"></div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if (!empty($assists_data)) : ?>
                    <table cellpadding="0" cellspacing="0" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha Asistencia</th>
                                <th>Número Trabajadores</th>
                                <th>Estado</th>
                                <th class="actions"><?= __('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($assists_data as $date => $assist) :
                            $date_format = date_create($date); ?>
                            <tr>
                                <td><?= (!empty($assist['assistance_date'])) ? convertMonthToSpanish($assist['assistance_date']->format('l, j-F-Y H:m:s')) : convertMonthToSpanish(date_format($date_format,'l, j-F-Y')) ?></td>
                                <td><?= (!empty($assist['total_workers'])) ? $assist['total_workers'] : 'S/I' ?></td>
                                <td>
                                <?php
                                    ($assist['status'] == 'Sin ingresar') ? $assist_state = 'Sin información' : $assist_state = 'Pendiente Aprobación';
                                    ($assist['status'] == 'Sin ingresar') ? $class = 'text-warning' : $class = 'text-info';
                                    if (!empty($assist['approval'])) :
                                        $class = 'text-success';
                                        $assist_state = $assist['approval']['comment'];
                                    else :
                                        if (!empty($assist['reject'])) :
                                            $class = 'text-danger';
                                            $assist_state = 'Rechazado: ' . $assist['reject']['comment'];
                                        endif;
                                    endif;  ?>
                                    <p class="<?=$class?>"><?=$assist_state?></p>
                                </td>
                                <td class="actions">
                                <?php
                                if ($assist['status'] != 'Sin ingresar') :
                                    echo $this->Html->link(__('Ver'),
                                     ['action' => 'view', $assist['budget_id'], $assist['assistance_date']->format('Y-m-d')],
                                     ['class' => 'btn btn-xs btn-material-orange-900']);
                                    if (empty($assist['approval']) && $group_id == USR_GRP_JEFE_RRHH || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :
                                        echo $this->Html->link(__('Editar'),
                                         ['action' => 'edit', $assist['budget_id'], $assist['assistance_date']->format('Y-m-d')],
                                         ['class' => 'btn btn-xs btn-material-orange-900']);
                                        if ($group_id == USR_GRP_JEFE_RRHH || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :
                                            if (empty($assist['reject'])) :
                                                echo $this->Form->postLink(__('Aprobar'),
                                                 ['action' => 'approve'],
                                                 ['data' => ['budget_id' => $assist['budget_id'], 'assistance_date' => $assist['assistance_date']->format('Y-m-d')],
                                                  'class' => 'btn btn-xs btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar la Asistencia?')]);
                                                echo $this->Html->link(__('Rechazar'), '#', ['data-toggle' => 'modal', 'data-target' => '#' . $assist['assistance_date']->format('Y-m-d'),
                                                  'class' => 'btn btn-xs btn-danger']);
                                                echo $this->Element('reject_assist_modal', ['budget_id' => $assist['budget_id'], 'assistance_date' => $assist['assistance_date']->format('Y-m-d')]);
                                            else :
                                                echo $this->Form->postLink(__('Aprobar'),
                                                 ['action' => 'approve'],
                                                 ['data' => ['budget_id' => $assist['budget_id'], 'assistance_date' => $assist['assistance_date']->format('Y-m-d')],
                                                 'class' => 'btn btn-xs btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar la Asistencia?')]);
                                            endif;
                                        endif;
                                    endif;
                                else :
                                    echo ($group_id == USR_GRP_JEFE_RRHH || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_GE_GRAL) ?
                                     $this->Html->link(__('Ingresar'),
                                     ['action' => 'add', $budget->id, $date],
                                     ['class' => 'btn btn-xs btn-material-orange-900']) : '';
                                endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h4>No hay información de asistencias disponible.</h4>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
