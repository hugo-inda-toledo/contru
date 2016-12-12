<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Permisos'));
?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Permisos
        </h3>
    </div>
    <div class="panel-body">
        <?= $this->Html->link(__('Nuevo Permiso'), ['controller' => 'permissions', 'action' => 'add'],['class' => 'btn btn-primary pull-right btn-md']) ?>
        <?= $this->Html->link(__('Volver a Perfiles'), ['controller' => 'groups', 'action' => 'index'], ['class' => 'btn btn-material-orange-900 pull-left btn-xs']) ?>
    <!-- Panel content -->

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('name','Nombre') ?></th>
                    <th><?= $this->Paginator->sort('controller','Controlador') ?></th>
                    <th><?= $this->Paginator->sort('action','Acción') ?></th>
                    <th class="actions"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($permissions as $perm): ?>
                <tr>
                    <td><?= h($perm->permission_name) ?></td>
                    <td><?= h(ucwords($perm->controller)); ?></td>
                    <td><?= h($perm->action) ?></td>
                    <td class="actions">
                        <div class="btn-group">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $perm->id],['class' => 'btn btn-sm btn-info']) ?>
                            <a href="#" data-target="#" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><?= $this->Html->link(__('Editar'), ['action' => 'edit', $perm->id]) ?></li>
                                <li>
                                    <?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $perm->id], ['confirm' => __('Estas seguro de eliminar {0}?', $perm->permission_name)]) ?>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?= $this->Element('paginador'); ?>
    </div>  
</div>
<?php //debug($permissions);?>