<h3>Avisos Claisificados</h3>

<?php foreach ($posts as $post) : ?>

	<?php // si no existe la foto, coloca una de reemplazo
	$ruta_imagen = WWW_ROOT . 'files' . DS . 'posts' . DS . 'photo' . DS . $post->photo_dir . DS . '330x180_' . $post->photo;
	if (! file_exists($ruta_imagen)) $ruta_imagen = DS . 'img' . DS . 'default_post' . DS . '330x180.png'; else $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'posts' . DS . 'photo' . DS . $post->photo_dir . DS . '330x180_' . $post->photo;
	?>

	<div>
		<?= $this->Html->image($ruta_imagen, ['alt' => $post->photo]); ?>
		<?= h($post->title) ?> <?= h($post->title) ?> <br />
	</div>

	<br />
	<br />

<?php endforeach; ?>