<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Obras'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Obras</h3>
    </div>
    <div class="panel-body">
        <?= $this->Html->link(__('Nueva Obra'), ['action' => 'add'],['class' => 'btn btn-primary pull-right btn-md']) ?>
    <!-- Panel content -->
        <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('name','Nombre') ?></th>
                <th><?= $this->Paginator->sort('description','Descripción') ?></th>
                <th><?= __('Estado Presupuesto') ?></th>
                <th><?= $this->Paginator->sort('created','Fecha inicio obra') ?></th>
                <th class="actions"><?= __('Acciones') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($buildings as $building): ?>
            <tr>
                <td><?= h($building->name) ?></td>
                <td><?= h($building->description) ?></td>
                <td><?php if (empty($building->budgets)) : ?>
                    Configurar Obra
                <?php else : ?>
                <?= empty($building->budgets) ? $this->Html->link(__('Importar Excel'), ['controller' => 'budgets', 'action' => 'import_excel', $building->budgets[0]->id], ['class' => 'btn btn-sm btn-primary pull-right btn-md']) : 
                    $this->Html->link(__('Ver Detalle'), ['controller' => 'budgets', 'action' => 'review', $building->budgets[0]->id], ['class' => 'btn btn-sm btn-primary pull-right btn-md']) ;?>

                <?php     endif;?>
                </td>
                <td><?= h($building->created) ?></td>

                <td class="actions">
                    <div class="split-button">
                        <?php if (!empty($building->budgets)): ?>
                            <?= $this->Html->link(__('Planificaciones'), ['controller' => 'schedules','action' => 'index', $building->budgets[0]->id],['class' => 'btn btn-sm btn-material-orange-900 ']) ?>
                            <?php // echo $this->Html->link(__('Estados de pago'), ['controller' => 'payment_statements','action' => 'index', $building->budgets[0]->id],['class' => 'btn btn-sm btn-material-orange-900 ']) ?>
                        <?php endif ?>
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $building->id],['class' => 'btn btn-sm btn-material-orange-900 ']) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $building->id],['class' => 'btn btn-sm btn-material-orange-900 ']) ?>
                        <?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $building->id], ['class' => 'btn btn-sm btn-material-orange-900 ', 'confirm' => __('Estas seguro de eliminar {0}?', $building->name)]) ?>
                    </div>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
        </table>
    </div>
</div>
