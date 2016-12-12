<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Usuarios'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Cuentas usuarios'), 'class' => 'primary', 'icon' => 'users', 'link' => '/users/listEditStatus'];
// $buttons[] = ['title' => __('Editar'), 'class' => 'primary', 'icon' => 'pencil', 'link' => '/users/edit/' . $user->id];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Cambiar Contraseña Usuario</h3>
    </div>
    <div class="panel-body">
		<div class="panel panel-default">
		    <div class="panel-body">
				<div class="users form col-sm-6 col-md-6">
					<p>Nombre: <?= h($user->first_name) ?> <?= h($user->lastname_f) ?></p>
					<p>Tipo de Usuario: <?= $user->group['name'] . ' (' . $user->group['description'] . ')' ?></p>
					<p>Email: <?= h($user->email) ?></p>
					<p>Estado: <?= ($user->active) ? 'Activo' : 'Bloqueado'; ?></p>
					<p>Fecha Registro: <?= h($user->created) ?></p>
			    </div>
			</div>
		</div>
		<div class="users form col-sm-6 col-md-6">
		    <?= $this->Form->create($user, ['id' => 'update_password']); ?>
		    <?= $this->Form->input('password', array('type' => 'password', 'label' => __('Nueva Contraseña'), 'value' => '', 'autocomplete' => 'off'));?>
	        <?= $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirme contraseña'), 'value' => '',
	        	'autocomplete' => 'off')); ?>
	    	<?= $this->Form->button(__('Guardar', ['class' => 'asd'])) ?>
			<?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link pull-right']) ?></div>
		    <?= $this->Form->end() ?>
		</div>
	</div>
</div>
<?= $this->Html->script('users.update_password'); ?>