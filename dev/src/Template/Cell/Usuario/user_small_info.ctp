<?php
// si no existe la foto, coloca una de reemplazo
$ruta_imagen = WWW_ROOT . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
if (! file_exists($ruta_imagen)) $ruta_imagen = DS . 'img' . DS . 'default_user' . DS . '32x32.png'; else $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '32x32_' . $user->photo;
?>
<div class="row cells4">
	<div class="cell useravatar">
		<div class="image-container bordered">
			<div class="frame"><?= $this->Html->image($ruta_imagen, ['alt' => $user->photo, 'class' => 'profilpic']); ?></div>
		</div>
	</div>
	<div class="cell colspan3 userInfo">
		<div><?= h($user->name) ?> <?= h($user->last_name) ?></div>
		<small><?= h($user->email) ?></small>
	</div>
</div>