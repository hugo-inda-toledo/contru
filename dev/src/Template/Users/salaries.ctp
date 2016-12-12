<?php
// elementos estandares de la vista
$this->assign('title_text', __('Liquidaciones de sueldo'));
$this->assign('title_icon', 'users');
$buttons = array();
$this->set('buttons', $buttons);
?>

<table data-order='[[1, "asc"]]' class="dataTable">
    <thead>
        <tr>
            <th><?= __('Nombre') ?></th>
            <th><?= __('Apellido') ?></th>
            <th><?= __('Última liquidación') ?></th>
            <th class="actions"><?= __('Acciones') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= h($user->name) ?></td>
                <td><?= h($user->last_name) ?></td>
                <td><?= (empty($user['Salary']) ? h('No hay registro') : h($user['Salary'][0]['created'])) ?></td>
                <td class="actions">
                    <div class="split-button">
                        <?= $this->Html->link(__('Liquidaciones'), ['controller' => 'Salaries', 'action' => 'view', $user->id], ['class' => 'button small-button']) ?>
                        <button class="split dropdown-toggle "></button>
                        <ul class="split-content d-menu" data-role="dropdown">
                            <li><?= $this->Html->link(__('Cargar Liquidación'), ['controller' => 'Salaries', 'action' => 'add', $user->id]) ?></li>
                        </ul>
                    </div>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>