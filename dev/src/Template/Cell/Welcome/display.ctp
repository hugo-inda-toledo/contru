<div data-role="dialog" id="dialog" class="padding20 dialog" data-close-button="true" data-overlay="true" data-overlay-color="op-dark" style="width: auto; height: auto; display: block; left: 318px; top: 210px">
	<h1>Bienvenido</h1>
	<p>
		<?php echo $welcome_mensaje; ?>
	</p><span class="dialog-close-button"></span>
</div>

<script >
	$(document).ready(function() {
		<?php if($welcome_mensaje != false) :?>
			var dialog = $('#dialog').data('dialog');
			dialog.open();
		<?php endif; ?>
	});
</script>