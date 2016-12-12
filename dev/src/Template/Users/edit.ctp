<?php
    $this->assign('title_text', __('Módulo Usuarios'));
    $this->assign('title_icon', 'users');
?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Editar Usuario</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
         <div class="col-sm-12 col-md-12">
            <?= $this->Form->create($user); ?>
            <div class="row">
                <div class="col-sm-6">        
                    <?= $this->Form->input('first_name', ['label' => 'Nombre']); ?>            
                    <?= $this->Form->input('lastname_f', ['label' => 'Apellido paterno']); ?>
                    <?= $this->Form->input('lastname_m', ['label' => 'Apellido materno']); ?>
                    <?php echo $this->Form->label('UsersGroups.group_id', 'Permisos');?>
                    <?php echo $this->Form->select('UsersGroups.group_id', $groups, ['multiple' => true, 'default' => $ids, 'class' => 'form-control', 'required' => 'required', 'style' => 'height: 165px;']);?>               
                </div>
                <div class="col-sm-6">
                    <?php $multiple = ($user->group_id == USR_GRP_VISITADOR) ? true : false; ?>
                    <?= $this->Form->input('building_id', ['multiple' => $multiple, 'class' => 'select2', 'options' => $buildings, 'label' => 'Asociar a Obra', 'empty' => 'Seleccione una Obra']); ?>
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