<h3>Restricción Vehicular</h3>

Normal:
<?php foreach ($restriccion->normal as $digito_restriccion) : ?>
	<?= $digito_restriccion ?> &nbsp;
<?php endforeach; ?>
<br />

Normal mañana:
<?php foreach ($restriccion->normal_maniana as $digito_restriccion) : ?>
	<?= $digito_restriccion ?> &nbsp;
<?php endforeach; ?>
<br />

Catalitico:
<?php foreach ($restriccion->catalitico as $digito_restriccion) : ?>
	<?= $digito_restriccion ?> &nbsp;
<?php endforeach; ?>
<br />