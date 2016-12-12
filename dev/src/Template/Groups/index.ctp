<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Perfiles'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Perfiles
        </h3>
    </div>
    <div class="panel-body">
        <?= $this->Html->link(__('Nuevo Perfil'), ['action' => 'add'],['class' => 'btn btn-primary pull-right btn-md']) ?>
        <?= $this->Html->link(__('Administrar Permisos'), ['controller' => 'permissions', 'action' => 'index'], ['class' => 'btn btn-material-orange-900 pull-right btn-xs']) ?>
    <!-- Panel content -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('name','Nombre') ?></th>
                    <th><?= $this->Paginator->sort('level','Nivel') ?></th>
                    <th><?= $this->Paginator->sort('created','Creado') ?></th>
                    <th><?= $this->Paginator->sort('status','Estado') ?></th>
                    <th class="actions"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?= h($group->name) ?></td>
                    <td><?= h($group->level) ?></td>
                    <td><?= h($group->created) ?></td>
                    <td><?= ($group->status)? 'Activo' : 'Bloqueado' ?></td>
                    <td class="actions">
                        <div class="btn-group">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $group->id],['class' => 'btn btn-sm btn-material-orange-900']) ?>
                            <a href="#" data-target="#" class="btn btn-sm btn-material-orange-900 dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><?= $this->Html->link(__('Ver'), ['action' => 'view', $group->id]) ?></li>
                                <li><?= $this->Html->link(__('Editar'), ['action' => 'edit', $group->id]) ?></li>
                                <li>
                                    <?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $group->id], ['confirm' => __('Estas seguro de eliminar {0}?', $group->name)]) ?>
                                </li>
                                <li><?= ($group->status)? $this->Html->link(__('Desactivar'), ['action' => 'deactivate', $group->id]) :
                                 $this->Html->link(__('Activar'), ['action' => 'activate', $group->id]) ?></li>
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
<?php //debug($groups);?>