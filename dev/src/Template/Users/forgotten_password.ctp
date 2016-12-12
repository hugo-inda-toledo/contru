<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-lg-4 col-sm-offset-3 col-lg-offset-4">
			<div class="logo animated fadeIn">
				<?= $this->Html->image('logo_login.png', ['alt' => 'LDZ', 'class' => 'img-responsive']); ?>
			</div>
			<div class="shadow-box animated fadeIn">
				<?php $this->assign('title', '&nbsp;');?>
				<?= $this->Flash->render('auth') ?>
				<?= $this->Form->create() ?>
				<div class="row">
					<div class="col-xs-12 cont-box shadow-z-1">
						<h2 class="title animated fadeInUp"><?= __('Recuperar Contraseña') ?></h2>
						<p class="subtitle animated fadeInUp">&nbsp;</p>
						<div class="col-xs-12">
							<?= $this->Form->input('email', array('type'=>'email', 'autocomplete'=>'off', 'required' => true, 'placeholder' => 'Ingrese su Email')); ?>
							<div class="btnAction pull-right">
								<?= $this->Html->link('Atras', '/users/login', array('escape' => false, 'class' => 'btn btn-primary')); ?>
								<?= $this->Form->button(__('Enviar'), ['class' => 'btn btn-warning', 'escape' => false]); ?>
								<br>
							</div>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		$('body').css('background-color', 'transparent');
	});
</script>