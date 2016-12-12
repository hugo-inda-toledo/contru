<!-- <h3>Cumpleañeros de hoy</h3> -->

<?php foreach ($users as $user) : ?>

	<?php // si no existe la foto, coloca una de reemplazo
	$ruta_imagen = WWW_ROOT . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
	if (! file_exists($ruta_imagen)) $ruta_imagen = DS . 'img' . DS . 'default_user' . DS . '32x32.png'; else $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
	?>
	<div class="row">
		<div class="cell colspan4">
			<div class="image-container image-format-cycle" style="width: 100%;">
        		<div class="frame">
        			<?= $this->Html->image($ruta_imagen, ['alt' => $user->photo, 'class' => 'imgCumple']); ?>
        		</div>
        	</div>
		</div>
		<div class="cell colspan8">
			<a href="mailto:<?= h($user->email) ?>"><?= h($user->name) ?> <?= h($user->last_name) ?></a>
			<small>Feliz  Cumpleaños te desea Achee</small>
		</div>
	</div>

<?php endforeach; ?>