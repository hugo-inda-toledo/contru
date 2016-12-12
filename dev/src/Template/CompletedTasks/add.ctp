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
        <h3 class="panel-title">Ingresar Trabajo Realizado</h3>
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
        <?= $this->Form->create($completedTask); ?>
        <table class="table table-striped table-hover">
            <col width="10%">
            <col width="40%">
            <col width="8%">
            <col width="8%">
            <col width="8%">
            <col width="26%">
            <tr>
                <th>N° Partida</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Avance Proyectado</th>
                <th>Trabajadores</th>
                <!-- <th>Falla</th> -->
            </tr>
            <?php foreach ($schedule['progress'] as $progress) : ?>
            <?= $this->Form->hidden('CompletedTasks.' . $progress['id'] . '.schedule_id', ['value' => $schedule['id']]) ?>
            <?= $this->Form->hidden('CompletedTasks.' . $progress['id'] . '.budget_item_id', ['value' => $progress['budget_item_id']]) ?>
            <tr>
                <td><?= $progress['budget_item']['item'] ?></td>
                <td><?= $progress['budget_item']['description'] ?></td>
                <td><?= (!empty($units[$progress['budget_item']['unit_id']])) ? h($units[$progress['budget_item']['unit_id']]) : '' ?></td>
                <td><?= $progress['budget_item']['quantity'] ?></td>
                <td>
                    <?= $progress['proyected_progress_percent'] ?>%
                    <div class="progress">
                      <?php if ($progress['proyected_progress_percent'] == 100) : ?>
                          <div class="progress-bar progress-bar-success" style="width: <?= $progress['proyected_progress_percent'] ?>%"></div>
                      <?php else : ?>
                          <div class="progress-bar progress-bar-material-orange-<?= substr($progress['proyected_progress_percent'], 0, 1) ?>00" style="width: <?= $progress['proyected_progress_percent'] ?>%"></div>
                      <?php endif; ?>
                    </div>
                </td>
                <td><?= $this->Form->input('CompletedTasks.' . $progress['id'] . '.worker_softland_id', ['multiple' => true, 'class' => 'select2', 'options' => $workers,
                    'label' => false]); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?= $this->Form->button(__('Guardar')) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->Html->script('completed_tasks.add'); ?>