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
						<h2 class="title animated fadeInUp"><?= __('Inicio de Sesión') ?></h2>
						<p class="subtitle animated fadeInUp">&nbsp;</p>
						<div class="col-xs-12">
							<?= $this->Form->input('email', ['label' => __('Email'), 'placeholder'=>'Ingrese su Email']) ?>
							<?= $this->Form->input('password', ['label' => __('Contraseña'), 'placeholder' => 'Ingrese su Contraseña']) ?>
							<div class="btnAction">
									<?= $this->Form->hidden('remember', ['value' => true]) ?>
									<?= $this->Html->link($this->Html->tag('small', '¿Olvidaste tu contraseña?'), '/users/forgottenPassword', array('escape' => false)); ?>
								<div class="enter">
									<?= $this->Form->button(__('Entrar'), ['class' => 'btn btn-warning', 'escape' => false]); ?>
								</div>
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