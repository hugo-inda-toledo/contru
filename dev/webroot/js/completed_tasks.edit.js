jQuery(document).ready(function($) {
    $('.select2').select2({
        width: '300',
        language: 'es'
    });
    // variable con todos los inputs de porcentajes para manipular en el DOM
    var $inputs_percentages = $('input[data-type="percentage"]').parent();
    $('select.select2').each(function(index, el) {
        $(this).select2('val', $(this).attr('data-value').split(','));
    });
	if ($('form div.alert.alert-warning').is(':visible')) {
		$('form button.btn.btn-md').prop('disabled', true);
	}
    $('select.select2').on('change', function(e) {
    	e.preventDefault();
    	if (typeof e.added != "undefined") {
		    if ($inputs_percentages.find('input[data-worker="' + e.added.id + '"][data-budget_item_id="' + $(this).attr('data-budget_item_id') + '"]').length == 0) {
    			var new_input = '<div class="form-group">' +
    			'<label class="control-label">' + e.added.text.substr(0, 32) + '</label>' +
    			'<input class="form-control" type="number" name="CompletedTasks[' + $(this).attr('data-budget_item_id') + '][' + e.added.id + '][worker_percentage]"' +
    			'min="0" max="100" data-type="percentage" data-worker="' + e.added.id + '" data-budget_item_id="' + $(this).attr('data-budget_item_id') + '"></div>';
    			$(this).parent().parent().next().append(new_input);
    		} else{
	    		$(this).parent().parent().next().append($inputs_percentages.find('input[data-worker="' +
	    			e.added.id + '"][data-budget_item_id="' + $(this).attr('data-budget_item_id') + '"]').parent());
    		}
		} else {
			$(this).parent().parent().next().find('input[data-worker="' + e.removed.id + '"]').parent().detach()
		}
    });
	$(document).on('change', 'input[data-type="percentage"]', function(event) {
		event.preventDefault();
		$changed_input = $(this);
		total_percentage = 0;
		$('input[data-type="percentage"][data-worker="' + $(this).attr('data-worker') + '"]').each(function(index, el) {
			//alert($(this).val());
			($(this).val() == '') ?  '' : total_percentage += parseInt($(this).val());
		});
		$changed_input.parent().removeClass('has-error');
		$changed_input.siblings('p.text-danger').detach();
		if (total_percentage > 45) {
			$changed_input.parent().addClass('has-error');
			$changed_input.parent().append('<p class="text-danger">El trabajador no puede sobrepasar las 45 horas semanales.</p>');
			$('table td.worker_percentage.' + $(this).attr('data-worker')).children('span').text(total_percentage + '%');
            $('form button.btn.btn-md').prop('disabled', true);
			$('form div.alert.alert-warning').hide();
		} else if (total_percentage < 45) {
			$('form button.btn.btn-md').prop('disabled', true);
			var total = total_percentage;
			var rest = 0;
			$('input[data-type="percentage"][data-worker="' + $(this).attr('data-worker') + '"]').each(function(index, el) {
				if ($(this).val() == 0) {
					rest = 45 - total_percentage;
					$(this).attr('placeholder', rest);
					$(this).attr('value', rest);
				}
			});

			var total =  rest + total_percentage;
			$('table td.worker_percentage.' + $(this).attr('data-worker')).children('span').text(total + ' horas');
			$('form div.alert.alert-warning').show();
		} else {
			$('form button.btn.btn-md').prop('disabled', false);
			$('table td.worker_percentage.' + $(this).attr('data-worker')).children('span').text(total_percentage + ' horas');
			$('form div.alert.alert-warning').hide();
		}
	});
});
