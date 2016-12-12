$(document).ready(function() {
	$(document).on('click', '.collapse-card', function(event) {
		event.preventDefault();
		if ($(this).hasClass('active')) {
			$(this).find('.title a.pull-right').text('Cerrar');
		} else {
			$(this).find('.title a.pull-right').text('Expandir');
		}
	});
});