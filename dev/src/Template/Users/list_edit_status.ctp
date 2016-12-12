<?php
// elementos estandares de la vista
$this->assign('title_text', __('Cuentas de usuarios'));
$this->assign('title_icon', 'users');
$buttons = array();
$buttons[] = ['title' => __('Nuevo usuario'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/users/add'];
$this->set('buttons', $buttons);
?>

<div class="users index large-10 medium-9 columns">
    <!-- <table data-order='[[0, "asc"]]' class="dataTable"> -->
    <table data-order='[[0, "asc"]]' class="dataTable">
    <thead>
        <tr>
            <th><?= __('Nombre') ?></th>
            <th><?= __('Email') ?></th>
            <th><?= __('Grupo de Usuario') ?></th>
            <th class="actions"><?= __('Acciones') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td>
                <?= activo($user->status == 1) ?> <?= h($user->firstname) ?> <?= h($user->lastname_f) ?> <br />
            </td>
            <td><?= h($user->email) ?></td>

            <td><?= h($user->group->name) ?></td>

            <td class="actions">
                <div class="split-button">
                    <?= $this->Html->link(__('Ver'), ['action' => 'view', $user->id], ['class' => 'button small-button']) ?>
                    <button class="split dropdown-toggle "></button>
                    <ul class="split-content d-menu" data-role="dropdown">
                        <li><?= $this->Html->link(__('Cambiar contraseña'), ['action' => 'updatePasswordAdmin', $user->id]) ?></li>
                        <li><?= $this->Html->link(__('Cambiar estado'), ['action' => 'editStatus', $user->id]) ?></li>
                        <li><?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id]) ?></li>
                        <li><?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $user->id], ['confirm' => __('Seguro deseas eliminar el usuario "{0}". No hay vuelta atrás!', h($user->name) . ' ' . h($user->last_name))]) ?></li>
                    </ul>
                </div>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
</div>
