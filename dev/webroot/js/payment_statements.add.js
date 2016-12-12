var theSign = "$ ";
function autoNumericInit(operation){
    $('.ldz_numeric').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: theSign
        // aSign: '€ '
    });  //autoNumeric with options being passed

    $('.total_price, .ldz_numeric_no_sign').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: ''
    });  //autoNumeric with options being passed
}

function actualizaCampos( reference ){

    var progress_percent = parseFloat(reference.val());
    var previousProgressValue   = parseFloat(reference.parent().parent().siblings('td.previous_progress_value').children('input[data-type="previous_progress_value"]').val());
    var previousProgress        = parseFloat(reference.parent().parent().siblings('td.previous_progress').children('input[data-type="previous_progress"]').val());

    if( !isNaN(progress_percent) && progress_percent <= 100)
    {
        var progress                = (parseFloat(reference.parent().parent().siblings('td.progress_value').children('input').attr('data-total_price')) * (progress_percent / 100));
        var overallProgress = progress_percent - previousProgress;
        var overallProgressValue = (overallProgress / 100) * parseFloat(reference.parent().parent().siblings('td.progress_value').children('input').attr('data-total_price'));

        if((previousProgress + progress_percent) <= 100 && progress_percent >= previousProgress)
        {
            reference.parent().parent().siblings('td.progress_value').children('input').val(progress.toFixed(2));
            reference.parent().parent().siblings('td.progress_value').find('span').text(moneda(progress.toFixed(2)));

            reference.parent().parent().siblings('td.overall_progress').children().children('input[data-type="overall_progress"]').val(overallProgress.toFixed(2));
            reference.parent().parent().siblings('td.overall_progress').find('span').text(moneda(overallProgress.toFixed(2)+'%'));

            reference.parent().parent().siblings('td.overall_progress_value').find('input[data-type="overall_progress_value"]').val(overallProgressValue.toFixed(2));
            reference.parent().parent().siblings('td.overall_progress_value').find('span').text(moneda(overallProgressValue.toFixed(2)));
        }
        else
        {
            if(progress_percent <= previousProgress)
            {
                alert('El valor debe ser mayor al % de avance anterior ['+previousProgress.toFixed(2)+'%] y no sobrepasar el 100%');

                reference.val('');
                reference.parent().parent().siblings('td.progress_value').children('input[data-type="progress_value"]').val('');
                reference.parent().parent().siblings('td.progress_value').find('span').html('');

                reference.parent().parent().siblings('td.overall_progress').children().children('input[data-type="overall_progress"]').val('');
                reference.parent().parent().siblings('td.overall_progress').find('span').text('');

                reference.parent().parent().siblings('td.overall_progress_value').find('input[data-type="overall_progress_value"]').val('');
                reference.parent().parent().siblings('td.overall_progress_value').find('span').text('');
            }
            else
            {
                reference.parent().parent().siblings('td.progress_value').children('input').val(progress.toFixed(2));
                reference.parent().parent().siblings('td.progress_value').find('span').text(moneda(progress.toFixed(2)));

                reference.parent().parent().siblings('td.overall_progress').children().children('input[data-type="overall_progress"]').val(overallProgress.toFixed(2));
                reference.parent().parent().siblings('td.overall_progress').find('span').text(moneda(overallProgress.toFixed(2)+'%'));

                reference.parent().parent().siblings('td.overall_progress_value').find('input[data-type="overall_progress_value"]').val(overallProgressValue.toFixed(2));
                reference.parent().parent().siblings('td.overall_progress_value').find('span').text(moneda(overallProgressValue.toFixed(2)));
            }
        }
    }
    else
    {
        if(!isNaN(progress_percent))
        {
            alert('El valor no puede sobrepasar el 100%');
        }

        reference.val('');
        reference.parent().parent().siblings('td.progress_value').children('input[data-type="progress_value"]').val('');
        reference.parent().parent().siblings('td.progress_value').find('span').html('');

        reference.parent().parent().siblings('td.overall_progress').children().children('input[data-type="overall_progress"]').val('');
        reference.parent().parent().siblings('td.overall_progress').find('span').text('');

        reference.parent().parent().siblings('td.overall_progress_value').find('input[data-type="overall_progress_value"]').val('');
        reference.parent().parent().siblings('td.overall_progress_value').find('span').text('');
    }
}

function sugerido(target, value){
    $(target).val(value);
    $(target).change();
}

var mustAskSubmitConfirm = true;

$(function(){
    autoNumericInit('init');

    //$("#presentation-date").bootstrapMaterialDatePicker({ clearButton: true, clearText: 'Limpiar', lang: 'es', weekStart : 1, format : 'DD-MM-YYYY', time: false});
    //$("#billing-date").bootstrapMaterialDatePicker({ clearButton: true, clearText: 'Limpiar', lang: 'es', weekStart : 1, format : 'DD-MM-YYYY', time: false});
    //$("#estimation-pay-date").bootstrapMaterialDatePicker({ clearButton: true, clearText: 'Limpiar', lang: 'es', weekStart : 1, format : 'DD-MM-YYYY', time: false});

    /*$("#presentation-date").on('change', function(event, fecha){
        if( !$("#billing-date").val() )
            $("#billing-date").bootstrapMaterialDatePicker('setDate', fecha).val(fecha.format('DD-MM-YYYY'));
        if( !$("#estimation-pay-date").val() )
            $("#estimation-pay-date").bootstrapMaterialDatePicker('setDate', fecha).val(fecha.format('DD-MM-YYYY'));
    });*/

    $('form').on('submit', function(event) {
        if( mustAskSubmitConfirm ){
            //bootbox confirm
            var _this = $(this);
            event.preventDefault();
            bootbox.confirm({
                title: "Confirme por favor",
                message: 'Va a guardar el estado de pago con los datos que ve en pantalla',
                buttons: {
                    confirm: {
                        label: "Aceptar",
                        className: "btn btn-material-orange-900",
                    },
                    cancel: {
                        label: "Cancelar",
                        className: "btn btn-flat btn-link",
                    }
                },
                callback: function(result){
                    if(result) {
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
                        mustAskSubmitConfirm = false;
                        _this.submit();
                    }
                }
            });
        }
    });

    $(document).ready(function() {
        $(document).on('click', '#btn-toggle-items', function(event) {
            if($('#btn-toggle-items').html() == "Items Originales") {
                $('#btn-toggle-items').html('Items Adicionales');
                $('#originales').hide();
                $('#originales input[data-type="progress"]').val("");
                $('#originales input[data-type="progress_value"]').val("");
                $('#adicionales').show();
            } else {
                $('#btn-toggle-items').html("Items Originales");
                $('#adicionales').hide();
                $('#adicionales input[data-type="progress"]').val("");
                $('#adicionales input[data-type="progress_value"]').val("");
                $('#originales').show();
            }
        });



        //datetimepicker
        $('input[data-type="datetimepicker"]').datetimepicker({
            locale: 'es',
            useCurrent: false,
            // inline: true,
            format: 'DD-MM-YYYY',
            extraFormats: [ 'DD-MM-YYYY' ],
            //daysOfWeekDisabled: [0, 2, 3, 4, 5, 6],

        });

        $('#presentation-date').on("dp.change", function (e) {
            if( !$("#billing-date").val() )
                $("#billing-date").val(e.date.format('DD-MM-YYYY'));
            if( !$("#estimation-pay-date").val() )
                $("#estimation-pay-date").val(e.date.format('DD-MM-YYYY'));
        });

        //toggle currency
        $('a[data-toggle="currency-total"]').click(function(e) {
            e.preventDefault();
            if ($("input[name='currency_value_to_date']").val() == "") {
                bootbox.dialog({
                    message: "Por favor ingrese un valor de moneda",
                    title: "Ingresar valor Moneda",
                    buttons: {
                        main: {
                            label: "Aceptar",
                            className: "btn btn-material-orange-900",
                            callback: function() {
                                $("input[name='currency_value_to_date']").focus();
                            }
                        }
                    }
                });
            }
        });
        $(document).on('change', 'input[data-type="progress"]', function(event) {
            event.preventDefault();
            if ($("input[name='currency_value_to_date']").val() == "") {
                bootbox.dialog({
                    message: "Por favor ingrese un valor de moneda",
                    title: "Ingresar valor Moneda",
                    buttons: {
                        main: {
                            label: "Aceptar",
                            className: "btn btn-material-orange-900",
                            callback: function() {
                                $("input[name='value_to_pay']").focus();
                            }
                        }
                    }
                });
            } else {
                actualizaCampos( $(this) );
            }
        });
        //datatables
        $('table.table.edp').DataTable({
            "paging": false,
            //pageLength: 100,
            "ordering" :false,
            // orderable: false,
            // "deferRender": true, //sólo ajax
            "fixedHeader": false,
            "processing": true,
            "dom": '<"top"fi>rt',
            "columnDefs": [ {
                "targets": [2, 3, 4, 5, 6],
                "searchable": false
            }],
            language: { //http://datatables.net/reference/option/language
                "search": "Buscar Partidas: ",
                "processing": "Cargando",
                "emptyTable":     "No se encontraron resultados",
                "info":           "Mostrando _START_ de _TOTAL_ resultados",
                "infoEmpty":      "Mostrando 0 de 0 resultados",
                "infoFiltered":   "(Filtrados de _MAX_ resultados)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Ver _MENU_ resultados",
                "loadingRecords": "Cargando...",
                "zeroRecords":    "No se encontraron resultados",
            }
        });

    });
});
