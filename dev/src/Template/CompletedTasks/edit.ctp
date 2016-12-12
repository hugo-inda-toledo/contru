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
        <h3 class="panel-title">Editar Trabajo Realizado</h3>
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
        <?= $this->Form->create(null, [
            'url' => ['controller' => 'CompletedTasks', 'action' => 'edit', $schedule['id']]
        ]); ?>
        <table class="table table-striped table-hover">
            <col width="10%">
            <col width="26%">
            <col width="6%">
            <col width="6%">
            <col width="20%">
            <col width="32%">
            <tr>
                <th>N° Partida</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Trabajadores</th>
                <th>Participación Partida (Horas)</th>
            </tr>
            <?php
            $workers_percentages = array();
            foreach ($completed_tasks_data as $budget_item_id => $completed_task) :
                echo $this->Form->hidden('CompletedTasks.' . $budget_item_id . '.schedule_id', ['value' => $schedule['id']]);
                $worker_softland_ids = '';
                $budget_item_info = array();
                $worker_inputs = array();
                foreach ($completed_task as $saved_data) :
                    $budget_item_info['item'] = $saved_data['budget_item']['item'];
                    $budget_item_info['quantity'] = $saved_data['budget_item']['quantity'];
                    $budget_item_info['unit_id'] = $saved_data['budget_item']['unit_id'];
                    $budget_item_info['description'] = $saved_data['budget_item']['description'];
                    $worker_softland_ids = $worker_softland_ids . $saved_data['worker']['softland_id'] . ',';
                    $worker_inputs[$saved_data['worker']['id']] = $this->Form->input('CompletedTasks.' . $budget_item_id . '.' . $saved_data['worker']['softland_id'] . '.worker_percentage',
                        ['label' => substr($workers[$saved_data['worker']['softland_id']], 0, 32), 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 45,
                         'data-type' => 'percentage', 'data-worker' => $saved_data['worker']['softland_id'], 'data-budget_item_id' => $budget_item_id,
                         'value' => ($saved_data['budget_item_percentage'] == 0) ? '' : $saved_data['budget_item_percentage']]);
                    (empty($workers_percentages[$saved_data['worker']['softland_id']])) ? $workers_percentages[$saved_data['worker']['softland_id']] = 0 : '';
                    $workers_percentages[$saved_data['worker']['softland_id']] += $saved_data['budget_item_percentage'];
                endforeach; ?>
                <tr>
                    <td><?= $budget_item_info['item'] ?></td>
                    <td><?= $budget_item_info['description'] ?></td>
                    <td><?= (!empty($units[$budget_item_info['unit_id']])) ? h($units[$budget_item_info['unit_id']]) : '' ?></td>
                    <td><?= moneda($budget_item_info['quantity']) ?></td>
                    <td>
                        <?= $this->Form->input('CompletedTasks.' . $budget_item_id . '.worker_softland_id',
                            ['multiple' => true, 'required' => true, 'class' => 'select2', 'options' => $workers, 'label' => false, 'data-value' => $worker_softland_ids,
                             'data-budget_item_id' => $budget_item_id]); ?>
                    </td>
                    <td>
                        <?php foreach ($worker_inputs as $worker_id => $worker_input) :
                            echo $worker_input;
                        endforeach; ?>
                     </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <table>
            <?php $completed = false;
            
            /*echo '<pre>';
            print_r($workers);
            echo '</pre>';

            echo '<pre>';
            print_r($workers_percentages);
            echo '</pre>';*/

            foreach ($workers as $ficha => $name) :
                if(isset($workers_percentages[$ficha])):
                    ($workers_percentages[$ficha] != 45) ? $completed = false : $completed = true; ?>
                    <tr>
                        <td><?= $ficha . ': ' . $name; ?></td>
                        <td class="worker_percentage <?= $ficha ?>"><span><?= (empty($workers_percentages[$ficha])) ? 0 : $workers_percentages[$ficha] ?> horas</span></td>
                    </tr>
                <?php endif;?>
        <?php endforeach; ?>
        </table>
        <div style="display:<?= ($completed) ? 'none' : 'block'?>;" class="alert alert-warning">Falta completar las 45 horas de los trabajadores en las partidas.</div>
        <?= $this->Form->button(__('Guardar')) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->Html->script('completed_tasks.edit'); ?>
