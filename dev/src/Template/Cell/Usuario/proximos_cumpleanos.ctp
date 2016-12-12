<h3>Próximos cumpleaños</h3>

<?php foreach ($users as $user) : ?>

	<?php // si no existe la foto, coloca una de reemplazo
	$ruta_imagen = WWW_ROOT . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
	if (! file_exists($ruta_imagen)) $ruta_imagen = DS . 'img' . DS . 'default_user' . DS . '32x32.png'; else $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
	?>

	<div>
		<?= $this->Html->image($ruta_imagen, ['alt' => $user->photo, 'class' => 'place-left']); ?>
		<?= h($user->name) ?> <?= h($user->last_name) ?> <br />
		<small style="margin-left: 10px;"><?= h($user->email) ?></small>
		<?= h($user->birth_date) ?>
	</div>

	<br />
	<br />

<?php endforeach; ?>