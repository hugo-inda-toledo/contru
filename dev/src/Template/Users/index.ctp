<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Usuarios'));
$this->assign('title_icon', 'users');
$buttons = array();
$buttons[] = ['title' => __('Nuevo usuario'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/users/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Usuarios</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
        <?= $this->Html->link(__('Nuevo Usuario'), ['action' => 'add'], ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('first_name', __('Usuario')) ?></th>
                    <th><?= $this->Paginator->sort('email', __('Email')) ?></th>
                    <th><?= $this->Paginator->sort('group_id', __('Perfil/es')) ?></th>
                    <th><?= $this->Paginator->sort('active', __('Estado')) ?></th>
                    <th class="actions"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= h($user->first_name) ?> <?= h($user->lastname_f) ?> <?= h($user->lastname_m) ?><br />
                        <small></small>
                    </td>
                    <td><?= h($user->email) ?></td>
                    <td>
                        <?php 
                            if(count($user->groups) > 1)
                            {
                                $tooltip = '';
                                $counter = 0;

                                foreach($user->groups as $group)
                                {
                                    $tooltip .= '- '.$group->name.'<br>';
                                    $counter++;
                                }

                                echo $this->Html->tag('span', $counter.' Perfiles', ['data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'data-html' => 'true', 'title' => $tooltip]);
                            }
                            else
                            {
                                foreach($user->groups as $group)
                                {
                                    echo  $group->name;
                                }
                            }
                        ?>
                    </td>
                    <td><?= ($user->active) ? 'Activo' : 'Bloqueado' ?></td>
                    <td class="actions">
                        <div class="btn-group">
                            <?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id], ['class' => 'btn btn-sm btn-material-orange-900']) ?>
                            <a href="#" data-target="#" class="btn btn-sm btn-material-orange-900 dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id]) ?></li>
                                <li><?= $this->Html->link(__('Cambiar Contraseña'), ['action' => 'updatePasswordAdmin', $user->id]) ?></li>
                                <?php if ($user->active): ?>
                                    <li><?= $this->Html->link(__('Bloquear Usuario'), ['action' => 'editStatus', $user->id]) ?></li>
                                <?php else: ?>
                                    <li><?= $this->Html->link(__('Activar Usuario'), ['action' => 'editStatus', $user->id]) ?></li>
                                <?php endif ?>
                                <li><?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id]) ?></li>
                                <li class="divider"></li>
                                <li><?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $user->id], ['confirm' => __('Seguro deseas eliminar el usuario "{0}". No se podrá recuperar la información', h($user->first_name) . ' ' . h($user->lastname_f))]) ?></li>
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