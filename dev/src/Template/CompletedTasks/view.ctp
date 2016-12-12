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
        <h3 class="panel-title">Ver Trabajo Realizado</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><strong>Planificación: </strong><?= $schedule['name'] ?> </h4>
                <h5><strong>Descripción: </strong><?= $schedule['description'] ?></h5>
                <h5><?= '<strong>Fecha Inicio: </strong>' . $schedule['start_date'] . ' - <strong>Fecha Término: </strong>' . $schedule['finish_date'] ?></h5>
            </div>
        </div>
        <?php 
        if ($approvals->count() > 0) : 
            echo '<div class="well well-sm"><p class="text-success">';
            foreach ($approvals as $approval) : 
                echo $approval['comment'] . ' | Fecha: ' . $approval['created']->nice();
            endforeach;
            echo '</p></div>';
            if ($rejects->count() > 0) :
                echo '<div class="shadow-z-1"><p class="text-danger">Rechazado: ';
                foreach ($rejects as $reject) : 
                    echo $reject['comment'] . ' | Fecha: ' . $reject['created']->nice();
                endforeach;
                echo '</p></div>';
            endif;
        elseif ($rejects->count() > 0) :
            echo '<div class="well well-sm"><p class="text-danger">Rechazado: ';
            foreach ($rejects as $reject) : 
                echo $reject['comment'] . ' | Fecha: ' . $reject['created']->nice();
            endforeach;
            echo '</p></div>';
            if ($this->request->session()->read('Auth.User.group_id') == USR_GRP_VISITADOR) :
                echo $this->Form->postLink(__('Aprobar Trabajo Realizado'),
                 ['action' => 'approve', $schedule['id']],
                 ['data' => ['schedule_id' => $schedule['id']], 'class' => 'btn btn-sm btn-success approve',
                  'confirm' => __('¿Está seguro que desea aprobar el trabajo realizado?')]);
            endif;  
        else :
            if ($this->request->session()->read('Auth.User.group_id') == USR_GRP_VISITADOR) :
                echo $this->Form->postLink(__('Aprobar Trabajo Realizado'),
                 ['action' => 'approve', $schedule['id']],
                 ['data' => ['schedule_id' => $schedule['id']], 'class' => 'btn btn-sm btn-success approve',
                  'confirm' => __('¿Está seguro que desea aprobar el trabajo realizado?')]);
                echo $this->Html->link(__('Rechazar Trabajo Realizado'), '#', 
                 ['data-toggle' => 'modal', 'data-target' => '#' . $schedule['id'], 'class' => 'btn btn-sm btn-danger']);
                echo $this->Element('reject_modal', ['schedule_id' => $schedule['id'], 'modal_title' => 'Rechazar Avance de Obra', 'controller' => 'completed_tasks', 'action' => 'reject']);
            endif;
        endif; ?>
        <h4>Detalle Trabajadores</h4>
         <?php foreach ($completed_tasks_data as $worker_id => $completed_task_data) : ?>
            <div class="collapse-card shadow-z-1">
                <div class="title">
                   <p style="display: inline;"><strong><?= $workers[$worker_id]['nombres'] ?></strong><?= ' / ' . $workers[$worker_id]['Cargo']['nombre_cargo'] ?></p>
                    <p class="text-muted" style="display: inline;"><?= ' / ' .  $workers[$worker_id]['rut'] ?></p>
                    <a class="pull-right" href="javascript:void(0)">Expandir</a>
                </div>
                <div class="body">
                    <table class="table table-striped table-hover">
                        <col width="10%">
                        <col width="50%">
                        <col width="8%">
                        <col width="8%">
                        <col width="12%">
                        <col width="12%">
                        <tr>
                            <th>N° Partida</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Cantidad</th>
                            <th>Participación Partida (Horas)</th>
                            <!--<th>Horas Trabajadas</th>-->
                        </tr>
                        <?php foreach ($completed_task_data as $completed_task) : ?>
                            <tr>
                                <td><?= $completed_task['budget_item']['item'] ?></td>
                                <td><?= $completed_task['budget_item']['description'] ?></td>
                                <td><?= (!empty($units[$completed_task['budget_item']['unit_id']])) ? h($units[$completed_task['budget_item']['unit_id']]) : '' ?></td>
                                <td><?= $completed_task['budget_item']['quantity'] ?></td>
                                <td><?= $completed_task['budget_item_percentage'] ?></td>
                                <!--<td><?= $completed_tasks_hours[$worker_id]['tasks_hours'][$completed_task['budget_item_id']] ?></td>-->
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>
<?= $this->Html->script('completed_tasks.view'); ?>