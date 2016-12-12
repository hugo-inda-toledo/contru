<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Perfiles'));
$this->assign('title_icon', 'groups');
$buttons = array();
$buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>
<style type="text/css">
	.lower {
	   text-transform: lowercase;
	}
</style>

<div class="panel panel-material-blue-grey-700">
	<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar nuevo permiso</h3>
    </div>
    <div class="panel-body">
    	<!-- Panel content -->
    	<?= $this->Form->create($permission); ?>
    	<div class="users form col-sm-6 col-md-6">
		    
		    <fieldset>
		        <?php
		            echo $this->Form->input('permission_name',['label' => 'Nombre']);
		            echo $this->Form->input('permission_description',['label' => 'Descripción']);
		            echo $this->Form->input('controller', ['label' => 'Controlador', 'class' => 'lower']);
		            echo $this->Form->input('action', ['label' => 'Acción', 'class' => 'lower']);
		        ?>
		    </fieldset>
		    <?= $this->Form->button(__('Guardar')) ?>
		    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link pull-right']) ?>
		    
		</div>
		<?= $this->Form->end() ?>
	</div>
</div>
