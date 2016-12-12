<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuestos'));
$this->assign('title_icon', 'users');
$buttons = array();
$buttons[] = ['title' => __('Nuevo Presupuesto'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/budgets/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Usuarios</h3>
    </div>
    <div class="panel-body">
        <div class="budgets index large-10 medium-9 columns">
            <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('building_id') ?></th>
                    <th><?= $this->Paginator->sort('duration') ?></th>
                    <th><?= $this->Paginator->sort('uf_value') ?></th>
                    <th><?= $this->Paginator->sort('total_cost_uf') ?></th>
                    <th><?= $this->Paginator->sort('comments') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($budgets as $budget): ?>
                <tr>
                    <td><?= $this->Number->format($budget->id) ?></td>
                    <td>
                        <?= $budget->has('building') ? $this->Html->link($budget->building->name, ['controller' => 'Buildings', 'action' => 'view', $budget->building->id]) : '' ?>
                    </td>
                    <td><?= $this->Number->format($budget->duration) ?></td>
                    <td><?= $this->Number->format($budget->uf_value) ?></td>
                    <td><?= $this->Number->format($budget->total_cost_uf) ?></td>
                    <td><?= h($budget->comments) ?></td>
                    <td><?= h($budget->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $budget->id]) ?>
                        <?= $this->Html->link(__('Comentar'), ['action' => 'comment', $budget->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $budget->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $budget->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budget->id)]) ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
            </table>
            <?= $this->Element('paginador'); ?>
        </div>
    </div>
</div>