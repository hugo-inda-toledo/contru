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
        <h3 class="panel-title">Lista de Trabajos Realizados</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-lg-6">
              <?php $group_id = $this->request->session()->read('Auth.User.group_id');
              if ($group_id != USR_GRP_ADMIN_OBRA && $group_id != USR_GRP_ASIS_RRHH && $group_id != USR_GRP_OFI_TEC) :
                  echo $this->Element('building_filter'); // coloca un menu
               endif; ?>
            </div>
            <div class="col-lg-6"></div>
        </div>
        <div class="row">
            <div class="col-lg-12">
            <?php if (!empty($schedules->toArray())) : ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha Planificación</th>
                            <th>Nombre Planificación</th>
                            <th>Número Partidas</th>
                            <!-- <th>Número Trabajadores</th> -->
                            <th>Estado</th>
                            <th class="actions"><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($schedules->toArray() as $schedule) :
                        $schedule_state = 'Pendiente Aprobación';
                        $class = 'text-info';
                        if (!empty($approvals[$schedule['id']])) :
                            if (count($approvals[$schedule['id']]) > 0) :
                                foreach ($approvals[$schedule['id']] as $approval) :
                                    $schedule_state = $approval['comment'];
                                    $class = 'text-success';
                                endforeach;
                            else :
                                if (count($rejects[$schedule['id']]) > 0) :
                                    foreach ($rejects[$schedule['id']] as $reject) :
                                        $schedule_state = 'Rechazado: ' . $reject['comment'] . ' ';
                                        $class = 'text-danger';
                                    endforeach;
                                endif;
                            endif;
                        endif; ?>
                        <tr>
                            <td><?= $schedule['start_date']->nice() ?></td>
                            <td><?= $schedule['name'] ?></td>
                            <td><?= count($schedule['progress']); ?></td>
                            <!-- <td></td> -->
                            <td><p class="<?= $class ?>"><?= $schedule_state ?></p></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Ver'), ['action' => 'view', $schedule['id']],  ['class' => 'btn btn-xs btn-material-orange-900']) ?>
                                <?php if ($approvals[$schedule['id']]->count() == 0) :
                                    echo $this->Html->link(__('Editar'), ['action' => 'edit', $schedule['id']], ['class' => 'btn btn-xs btn-material-orange-900']);
                                endif; ?>
                            <?php
                            if ($this->Access->verifyLevel(6) == true) :
                                if (count($approvals[$schedule['id']]) == 0) :
                                    echo 'tiene mas de 1 approvals';
                                    if (count($rejects[$schedule['id']]) == 0) :
                                        //echo 'tiene mas de 1 trejects';
                                        echo $this->Form->postLink(__('Aprobar'),
                                         ['action' => 'approve'],
                                         ['data' => ['schedule_id' => $schedule['id']], 'class' => 'btn btn-xs btn-success approve']);
                                        echo $this->Html->link(__('Rechazar'), '#',
                                         ['data-toggle' => 'modal', 'data-target' => '#' . $schedule['id'], 'class' => 'btn btn-xs btn-danger']);
                                    else :
                                         echo $this->Form->postLink(__('Aprobar'),
                                         ['action' => 'approve'],
                                         ['data' => ['schedule_id' => $schedule['id']], 'class' => 'btn btn-xs btn-success approve']);
                                    endif;
                                endif;
                            endif; ?>
                            </td>
                        </tr>
                        <?php echo $this->Element('reject_modal', ['schedule_id' => $schedule['id'], 'title_modal' => 'Rechazar Trabajo Realizado', 'controller' => 'completed_tasks', 'action' => 'reject']); ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->Element('paginador'); ?>
            <?php else: ?>
                <h4>No hay Trabajos realizados disponibles.</h4>
            <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!--<pre>
<?php //print_r($approvals);?>
</pre>

<pre>
<?php //print_r($rejects);?>
</pre>-->