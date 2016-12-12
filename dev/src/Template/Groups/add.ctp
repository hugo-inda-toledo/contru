<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Perfiles'));
$this->assign('title_icon', 'groups');
$buttons = array();
$buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
	<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Perfil de Usuario</h3>
    </div>
    <div class="panel-body">
    	<!-- Panel content -->
    	<?= $this->Form->create($group); ?>
    	<div class="users form col-sm-6 col-md-6">
		    
		    <fieldset>
		        <?php
		            echo $this->Form->input('Groups.name',['label' => 'Nombre']);
		            echo $this->Form->input('Groups.description',['label' => 'Descripción']);
		            echo $this->Form->label('Groups.level', 'Nivel');
		        	echo $this->Form->select('Groups.level', $levels, ['empty' => 'Selecciona un nivel', 'required' => 'required']);
		        ?>
		    </fieldset>
		    <?= $this->Form->button(__('Guardar')) ?>
		    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link pull-right']) ?>
		    
		</div>
		<div class="users form col-sm-6 col-md-6">
			<fieldset>
			<?php echo $this->Form->label('GroupsPermissions.permission_id', 'Permisos');?>
			<?php echo $this->Form->select('GroupsPermissions.permission_id', $permissions, ['multiple' => true/*, 'default' => [1, 3]*/, 'class' => 'form-control', 'required' => 'required', 'style' => 'height: 165px;']);?>
			</fieldset>
		</div>
		<?= $this->Form->end() ?>
	</div>
</div>