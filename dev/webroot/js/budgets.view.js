$(document).ready(function(){
    autoNumericGetSignByValue($('input[name="currency_value_id"]').val());
    autoNumericInit('init');

    $( "#formComment" ).click(function( event ) {
        event.preventDefault();
        $('#modalComment').modal('show');
    });
    $( "#formState" ).click(function( event ) {
        event.preventDefault();
        $('#modalState').modal('show');
    });
    $( "#formDelete" ).click(function( event ) {
        event.preventDefault();
        $('#modalDelete').modal('show');
    });
    $( "#formDeleteItems" ).click(function( event ) {
        event.preventDefault();
        $('#modalDeleteItems').modal('show');
    });
});

$(document).on('click', '.toggle-open', function(event) {
    event.preventDefault();
    $('.panel-collapse').collapse('toggle');
    setTimeout(toggleOpenBtnText, 1000);
});

function toggleOpenBtnText () {
    if ($('.panel-collapse').first().hasClass('in')) {
        $('.toggle-open').text('Contraer Partidas');
    } else {
        $('.toggle-open').text('Expandir Partidas');
    }
}

$(document).on('click', '.btn.inline-button.target-value', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var total = $(this).attr('data-total');
    var budget_item_id = $(this).attr('data-id');
    var target_value = $(this).attr('data-value');
    setTimeout(function () {
        $('.bootbox-input.bootbox-input-text.form-control').attr({'type': 'number', 'placeholder': target_value, 'min': 0});
        $('button[data-bb-handler="confirm"]').removeClass('btn-primary').addClass('btn-material-orange-900');
        $('button[data-bb-handler="confirm"]').text('Guardar');
        $('button[data-bb-handler="cancel"]').text('Cancelar');
    }, 200);
    bootbox.prompt("Ingresar Valor Meta para la Partida", function(result) {
        if (result === null) {
            // Example.show("Prompt dismissed");
        } else {
            if ((result > total) || (result < 0)) {
                bootbox.alert('Valor Meta no puede ser menor a 0, o superar el monto total de la Partida: ' + total);
                setTimeout(function () {
                    $('button[data-bb-handler="ok"]').removeClass('btn-primary').addClass('btn-material-orange-900');
                    $('button[data-bb-handler="ok"]').text('Cerrar');
                }, 200);
            } else {
                $.ajax({
                    method: "POST",
                    url: JSCFG_URL + '/budget_items/target_value',
                    data: { target_value: result, budget_item_id: budget_item_id }
                }).done(function( data ) {
                    response = $.parseJSON(data);
                    if (response.status == 'ok') {
                        $btn.parent().parent().parent().find('span.target-value').text('$ ' + numberWithPoints(result));
                        bootbox.alert(response.message + ' : $' + result);
                        setTimeout(function () {
                            $('button[data-bb-handler="ok"]').removeClass('btn-primary').addClass('btn-material-orange-900');
                            $('button[data-bb-handler="ok"]').text('Cerrar');
                        }, 200);
                        $btn.attr('data-value', result);
                    } else {
                        $('body .alert.alert-dismissible.alert-danger').append('<p>' + response.message  + '. día: ' + response.date + '</p>').show();
                    }
                    // alert( "Data Saved: " + msg );
                }).fail(function() {
                    $('body .alert.alert-dismissible.alert-danger').append('<p>Ocurrió un error al conectar con el servidor. Por favor, inténtenlo nuevamente</p>').show();
                });
            }
        }
    });
});

$(document.body).on('click', '.save_all_target_value', function(e){
    e.preventDefault();
    var inputsToChange = {};
    var inputsWithErrors = [];
    $(document.body).find('input[data-class="input_multiple_target_value"]').each(function(){
        var total = $(this).attr('data-total');
        var meta = $(this).autoNumeric('get');
        var metaold = $(this).data('oldvalue');
        if((typeof metaold != 'undefined') && (meta != metaold)){
            if ((parseFloat(meta) > parseFloat(total)) || (parseFloat(meta) < 0)) {
                inputsWithErrors.push($(this).prop('id'));
            }else{
                // console.log('cambiar '+$(this).prop('id'));
                inputsToChange[$(this).data('budgetitemid')] = {'id': $(this).data('budgetitemid'), 'target_value': meta};
            }
        }
    });
    if(inputsWithErrors.length > 0){
        bootbox.alert('Hay montos menores a 0 o que superan el monto total de partida');
        setTimeout(function () {
            $('button[data-bb-handler="ok"]').removeClass('btn-primary').addClass('btn-material-orange-900');
            $('button[data-bb-handler="ok"]').text('Cerrar');
        }, 200);
        jQuery.each(inputsWithErrors, function(i, value){
            $('#'+value).css('border-bottom', '1px solid red');
        });
    }else if(!jQuery.isEmptyObject(inputsToChange)){
        bootbox.confirm("¿Está seguro de actualizar los valores objetivos?", function(result) {
            if(result){
                $.ajax({
                    method: "POST",
                    url: JSCFG_URL + '/budget_items/multiple_target_value',
                    dataType: 'json',
                    data: inputsToChange,
                    beforeSend: function(){
                        $('input.ldz_numeric, input.ldz_numeric_no_sign').each(function(i){
                            var self = $(this);
                            try{
                                var v = self.autoNumeric('get');
                                self.autoNumeric('destroy');
                                self.val(v);
                            }catch(err){
                                console.log("Not an autonumeric field: " + self.attr("name"));
                            }
                        });
                    },
                }).done(function( response ) {
                    bootbox.alert(response.message, function() {
                        location.reload();
                    });
                    ids = $.parseJSON(response.ids);
                    setTimeout(function () {
                        $('button[data-bb-handler="ok"]').removeClass('btn-primary').addClass('btn-material-orange-900');
                        $('button[data-bb-handler="ok"]').text('Cerrar');
                    }, 200);
                    if (response.status == 'ok') {
                        jQuery.each(ids, function(i, value){
                            $('#budgetitems-'+value+'-target-value').css('border-bottom', 'none');
                            $('#budgetitems-'+value+'-target-value').attr('value', $('#budgetitems-'+value+'-target-value').val());
                            $('#budgetitems-'+value+'-target-value').attr('data-oldvalue', $('#budgetitems-'+value+'-target-value').val());
                        });
                    } else {
                        jQuery.each(ids, function(i, value){
                            $('#budgetitems-'+value+'-target-value').css('border-bottom', '1px solid red');
                        });
                    }
                }).fail(function() {
                    $('body .alert.alert-dismissible.alert-danger').append('<p>Ocurrió un error al conectar con el servidor. Por favor, inténtenlo nuevamente</p>').show();
                });
            }
        });
    }else{
        bootbox.alert('Para guardar debe realizar algún cambio.');
    }
});

// bootbox-input bootbox-input-text form-control
