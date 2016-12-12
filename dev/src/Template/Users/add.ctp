<?php
$this->assign('title_text', __('Módulo Usuarios'));
$this->assign('title_icon', 'users');
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar un Usuario</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
            <?= $this->Form->create($user, ['type' => 'file']); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $this->Form->input('group_id', ['options' => $groups, 'label' => 'Perfil de Usuario', 'empty' => 'Seleccione un Perfil de Usuario']); ?>
                    <?= $this->Form->input('first_name', ['label' => 'Nombre']); ?>
                    <?= $this->Form->input('lastname_f', ['label' => 'Apellido paterno']); ?>
                    <?= $this->Form->input('lastname_m', ['label' => 'Apellido materno']); ?>
                    <?= $this->Form->input('password',['label' => 'Contraseña']); ?>
                    <?= $this->Form->input('confirm_password', ['type'=>'password', 'label'=>'Confirmar contraseña', 'value'=>'', 'autocomplete'=>'off']); ?>
                </div>
                <div class="col-sm-6">
                    <?= $this->Form->input('building_id', ['multiple' => false, 'class' => 'select2', 'options' => $buildings, 'label' => 'Asociar a Obra', 'empty' => 'Seleccione una Obra']); ?>
                    <?= $this->Form->input('email', ['label' => 'Email','type' => 'email','required']); ?>
                    <?= $this->Form->input('celphone', ['label' => 'Teléfono']); ?>
                    <?= $this->Form->input('address', ['label' => 'Dirección']); ?>
                </div>
            </div>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->Html->script('users.add'); ?>