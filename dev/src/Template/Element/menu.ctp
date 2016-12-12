<?php use Cake\Core\Configure; ?>
<div class="navbar navbar-material-blue-grey-700 shadow-z-2" style="margin-bottom:0px;">
	<div class="container-fluid">
		<div class="navbar-header">
	        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-inverse-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	        </button>
			<a class="navbar-brand" href="javascript:void(0)">
				<span class="menu-toggle pull-left"><?= $this->Html->image('menu_icon.png', ['alt' => 'LDZ Logo']); ?></span>
			</a>
	    </div>
	    <div class="navbar-collapse navbar-inverse-collapse">
	        <ul class="nav navbar-nav">
			<li><?= $this->Html->image('logo_light.png', ['alt' => 'LDZ', 'class' => 'img-responsive']); ?></li>
		        <li>
		        	<h4>Control de Presupuesto de Obra</h4>
		        </li>
		        <li>

		        	<?php if(!empty($last_building_info)): ?>

		        		<div id="last_building_selected">
							<button class="btn btn-default"><span class="label label-default"><?= $last_building_info .' ×'; ?></span></button>
						</div>
		        	<?php endif; ?>
		        </li>
	        </ul>
	        <ul class="nav navbar-nav navbar-right">
	            <li class="dropdown">
        			<?= $this->Html->image('user_icon.png', ['alt' => 'Usuario']); ?>
				    <h5 class="list-group-item-heading">
				    	<?php $full_name = $this->request->session()->read('Auth.User.first_name') . ' ' . $this->request->session()->read('Auth.User.lastname_f'); ?>
				    	<?= $this->Html->link((strlen($full_name) > 24) ? substr($full_name, 0, 20) + '...' : $full_name,
				    		['controller' => 'users', 'action' => 'view', $this->request->session()->read('Auth.User.id')]); ?>
		    		</h5>
				    <p class="list-group-item-text clearfix"><?= $this->request->session()->read('Auth.User.group_name')?></p>
            	   	<a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?= $this->Html->image('account_icon.png', ['alt' => 'Cuenta de Usuario']); ?></a>
                    <ul class="dropdown-menu">
                        <li><?= $this->Html->link(__('Cambiar Contraseña'), ['controller' => 'users', 'action' => 'updatePassword']) ?></li>
                        <li><?= $this->Html->link(__('Salir'), ['controller' => 'users', 'action' => 'logout'], ['escape' => false]); ?></li>
    				</ul>
	            </li>
	        </ul>
    	</div>
    </div>
</div>

