jQuery(document).ready(function($) {


     $(document).on('click', '#marcartodos', function(event) {
        event.preventDefault();
        if($(this).hasClass('btn-warning')) {
            $(this).addClass('btn-primary').removeClass('btn-warning');
            $('input[label-data="Asistió"]').each(function() {
                this.checked = false;
                $(this).trigger("change");
            });
            $(this).text('Seleccionar todos');

        } else {
            $(this).addClass('btn-warning').removeClass('btn-primary');
            $('input[label-data="Asistió"]').each(function() {
                this.checked = true;
                $(this).trigger("change");
            });
            $(this).text('Seleccionar ninguno');
        }
    });

    $('input[display-data="none"]').parent().parent().hide();
    $(document).on('change', 'input[data-all_day="1"]', function(event) {
        event.preventDefault();
        if ($(this).is(':checked')) {
            //habilitar form controls para para jornada completa
            $(this).parent().parent().siblings('.checkbox').find('input').prop('disabled', false);
            $(this).parent().parent().siblings('.checkbox').show();
            $(this).parent().parent().parent().next('td').find('select').prop({'disabled': false, 'required': true});
            $(this).parent().parent().parent().next('td').show(400);
            //deshabilitar form controls para media jornada
            $(this).parent().parent().parent().next('td').next('td').hide();
            $(this).parent().parent().parent().next('td').next('td').find('input').prop('disabled', true);
            $(this).parent().parent().parent().next('td').next('td').find('select').prop({'disabled': true, 'required': false});
            $(this).parent().parent().parent().next('td').next('td').find('input[data-hours="1"]').prop({'disabled': true, 'required': false});
        } else {
            //deshabilitar form controls para jornada completa
            $(this).parent().parent().siblings('.checkbox').find('input').prop('disabled', true);
            $(this).parent().parent().siblings('.checkbox').css('display', 'none');
            $(this).parent().parent().parent().next('td').find('select').prop({'disabled': true, 'required': false});
            $(this).parent().parent().parent().next('td').hide();
            //habilitar form controls para media jornada
            $(this).parent().parent().parent().next('td').next('td').show();
            $(this).parent().parent().parent().next('td').next('td').find('input').prop('disabled', false);
            $(this).parent().parent().parent().next('td').next('td').find('select').prop({'disabled': false, 'required': true});
            $(this).parent().parent().parent().next('td').next('td').find('input[data-hours="1"]').prop({'disabled': false, 'required': true});
        }
    });
    $(document).on('change', 'input[data-assistance="1"]', function(event) {
        event.preventDefault();
        if ($(this).parent().parent().parent().parent().parent().hasClass('half-day')) {
            if ($(this).is(':checked')) {
                $(this).parent().parent().parent().siblings().find('select').prop({'disabled': true, 'required': false});
            } else {
                $(this).parent().parent().parent().siblings().find('select').prop({'disabled': false, 'required': true});
            }
        } else {
            if ($(this).is(':checked')) {
                $(this).parent().parent().parent().next('td').find('select').prop({'disabled': true, 'required': false});
            } else {
                $(this).parent().parent().parent().next('td').find('select').prop({'disabled': false, 'required': true});
            }
        }
    });
    //select
    $(document).on('change', 'select[data-assist_type="1"]', function(event) {
        event.preventDefault();
        if ($(this).parent().parent().parent().hasClass('half-day')) {
            if ($(this).val() > 1) {
                $(this).parent().siblings().find('input[data-assistance="1"]').prop({'disabled': true, 'required': false});
            } else {
                $(this).parent().siblings().find('input[data-assistance="1"]').prop({'disabled': false, 'required': true});
            }
        } else {
            if ($(this).val() > 1) {
                $(this).parent().parent().prev('td').find('input[data-assistance="1"]').prop({'disabled': true, 'required': false});
                $(this).parent().parent().next('td').next('td').find('input[data-overtime="1"]').prop({'disabled': true});
                $(this).parent().parent().next('td').next('td').next('td').find('input[data-delay="1"]').prop({'disabled': true});
            } else {
                $(this).parent().parent().prev('td').find('input[data-assistance="1"]').prop({'disabled': false, 'required': true});
                $(this).parent().parent().next('td').next('td').find('input[data-overtime="1"]').prop({'disabled': false});
                $(this).parent().parent().next('td').next('td').next('td').find('input[data-delay="1"]').prop({'disabled': false});
            }
        }
    });
    $(document).on('change', 'input[data-hours="1"]', function(event) {
        event.preventDefault();
        $btnSubmit = $('button[type="submit"]');
        parent_hour = ($(this).parent().parent().siblings().find('input[data-hours="1"]').val() == '') ? parseInt(0) :
         parseInt($(this).parent().parent().siblings().find('input[data-hours="1"]').val());
        total_hours = parent_hour + parseInt($(this).val());
        $(this).parent().parent().siblings().find('input[data-hours="1"]').attr('placeholder', 9 - total_hours);
        $(this).parent().parent().parent('td.half-day').find('.form-group').removeClass('has-error');
        $(this).parent().parent().parent('td.half-day').find('p').detach();
        if (total_hours > 9) {
            $(this).parent().addClass('has-error');
            $(this).parent().parent().parent('td.half-day').append('<p class="text-danger">Suma más de 9 horas</p>');
            $btnSubmit.prop('disabled', true);
        } else if (total_hours < 9) {
            $(this).parent().parent().parent('td.half-day').append('<p class="text-info">Suma menos de 9 horas</p>');
            $btnSubmit.prop('disabled', true);
        } else {
            $btnSubmit.prop('disabled', false);
        }
    });


    var table = $('table.table-item').DataTable({
        "paging": false,
        //pageLength: 100,
        "ordering" :false,
        // orderable: false,
        // "deferRender": true, //sólo ajax
        "fixedHeader": false,
        "processing": true,
        "dom": '<"top"fi>rt',
        "columnDefs": [ {
            "targets": [0, 2, 3, 4, 5, 6],
            "searchable": false
        } ],
        // "search": {
        //     "search": "1.1 Inst"
        // },
        language: { //http://datatables.net/reference/option/language
            "search": "Buscar Trabajadores: ",
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

    $(document).on('click', 'button[type="submit"]', function(e){
        e.preventDefault();
        // Limpiar busqueda, porque datatables "borra" las filas al buscar, y por ende no las envía
        table.search('').draw();
        $('.sendAssists').submit();
    });
});
