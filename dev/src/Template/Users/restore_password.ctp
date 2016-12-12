<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-lg-4 col-sm-offset-3 col-lg-offset-4">
			<div class="logo animated fadeIn">
				<?= $this->Html->image('logo_login.png', ['alt' => 'LDZ', 'class' => 'img-responsive']); ?>
			</div>
			<div class="shadow-box animated fadeIn">
				<?php $this->assign('title', '&nbsp;');?>
				<?= $this->Flash->render('auth') ?>
				<?= $this->Form->create('User'); ?>
				<div class="row">
					<div class="col-xs-12 cont-box shadow-z-1">
						<h2 class="title animated fadeInUp"><?= __('Reestablece tu Contraseña') ?></h2>
						<p class="subtitle animated fadeInUp">&nbsp;</p>
						<div class="col-xs-12">
							<?= $this->Form->input('password', array('type' => 'password', 'label' => 'Nueva contraseña', 'autocomplete'=>'off', 'required' => true, 'placeholder' => 'Escribe una contraseña nueva')); ?>
							<?= $this->Form->input('password2', array('type' => 'password', 'value' => '', 'label' => 'Confirme contraseña', 'autocomplete'=>'off', 'required' => true, 'placeholder' => 'Repite la contraseña nueva')); ?>
							<div class="btnAction">
									<?= $this->Form->hidden('temp_pass', ['value' => $user->temp_pass]) ?>
								<div class="enter">
									<?= $this->Form->button(__('Cambiar contraseña')) ?>
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