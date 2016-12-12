
<?php
    $this->assign('title_text', __('Módulo Usuarios'));
    $this->assign('title_icon', 'user');
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Cambiar mi contraseña</h3>
    </div>
    <div class="panel-body">
        <h4>
            Usuario: <strong><?= h($user->first_name) ?> <?= h($user->lastname_f) ?></strong>
        </h4>
        <div class="users form col-sm-6 col-md-6">
            <?= $this->Form->create($user, ['id' => 'update_password']); ?>
            <?= $this->Form->input('old_password', array('type'=>'password', 'label'=>'Contraseña Actual', 'value'=>'', 'autocomplete'=>'off')); ?>
            <?= $this->Form->input('password', array('type'=>'password', 'label'=>'Nueva Contraseña', 'value'=> '', 'autocomplete'=>'off')); ?>
            <?= $this->Form->input('confirm_password', array('type'=>'password', 'label'=>'Confirme nueva Contraseña', 'value'=>'', 'autocomplete'=>'off')); ?>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->Html->script('users.update_password'); ?>
