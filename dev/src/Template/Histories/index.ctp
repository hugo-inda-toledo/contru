<?php
// elementos estandares de la vista
$this->assign('title_text', __('Logs'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Obras</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('created', __('Fecha')) ?></th>
                    <th><?= $this->Paginator->sort('user_id', __('Usuario')) ?></th>
                    <th><?= $this->Paginator->sort('Group.name', __('Grupo')) ?></th>
                    <th><?= $this->Paginator->sort('model', __('Modelo')) ?></th>
                    <th><?= $this->Paginator->sort('method', __('Función')) ?></th>
                    <th><?= $this->Paginator->sort('text', __('URL')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($histories as $history): ?>
                    <tr>
                        <td><?= h($history->created->format('d-m-Y H:i')) ?></td>
                        <td><?= h($history->user['email']) ?></td>
                        <td><?= h($history->group['name']) ?></td>
                        <td><?= h($history->model) ?></td>
                        <td><?= h($history->method) ?></td>
                        <td><?= h($history->text) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?= $this->Element('paginador'); ?>
    </div>
</div>

<script>
    $('th a').append(' <i class=""></i>');
    $('th a.asc i').attr('class', 'mdi-hardware-keyboard-arrow-down');
    $('th a.desc i').attr('class', 'mdi-hardware-keyboard-arrow-up');
</script>
