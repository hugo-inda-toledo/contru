<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Avance de Obra'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Avances de Obra
        </h3>
    </div>
    <div class="panel-body">
        <?php // $this->Html->link(__('Nueva Planificación Semanal'), ['action' => 'add',$budget_id],['class' => 'btn btn-primary pull-right btn-md']) ?>
    <!-- Panel content -->
        <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('name','Nombre') ?></th>
                <th><?= $this->Paginator->sort('description','Descripción') ?></th>
                <th><?= $this->Paginator->sort('total_days','Dias') ?></th>
                <th><?= $this->Paginator->sort('start_date','Día de inicio') ?></th>
                <th><?= $this->Paginator->sort('finish_date','Día de fin') ?></th>
                <th class="actions"><?= __('Acciones') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?= h($schedule->name) ?></td>
                <td><?= h($schedule->description) ?></td>
                <td><?= h($schedule->budget->name) ?></td>                                
                <td><?= h($schedule->total_days) ?></td>
                <td><?= h($schedule->start_date) ?></td>
                <td><?= h($schedule->finish_date) ?></td>
                
                <td class="actions">
                    <div class="split-button">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $schedule->id],['class' => 'btn btn-material-orange-900 ']) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $schedule->id],['class' => 'btn btn-material-orange-900 ']) ?>
                        <?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $schedule->id], ['class' => 'btn btn-material-orange-900 ', 'confirm' => __('Estas seguro de eliminar {0}?', $schedule->name)]) ?>                        
                    </div>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
        </table>
    </div>  
</div>
