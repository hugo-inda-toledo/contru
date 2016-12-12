$(document).ready(function(){
    $(document).on('click', '#last_building_selected span', function(event) {
        window.location.href = $(this).data('url');
    })

    // http://daneden.github.io/animate.css/
    $('.message').addClass('animated fadeIn');
    $('.login').on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
        $('.login label').addClass('animated fadeIn');
        $('.login .input-control').addClass('animated fadeIn');
        $('.login .form-actions .button').addClass('animated fadeIn');
        $('.login .grid.btnAction').addClass('animated fadeIn');
    });
    //Jquery validator options
    // jQuery.validator.setDefaults({
    //   // debug: true,
    //   success: "valid"
    // });

    /////////////
    //momentJS //
    /////////////
    moment.locale('es');

    // $('.select2').select2();

    //Dismiss alerts
    $('.container-fluid').find('.alert.alert-dismissable').each(function(index, el) {
       $alert = $(this);
        setTimeout(function() { $alert.detach(); }, 5000);
    });
    $(function () {
      $('dt[data-toggle="tooltip"]').tooltip({ container: 'body' });
      $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover' });
    });

    // AVOID DOBLE FORM SUBMIT
    $(document).on('click', 'button[type="submit"]', function(event) {
        $btnSubmit = $(this);
        setTimeout(function(){ $btnSubmit.prop('disabled', true); }, 10);
        setTimeout(function(){ $btnSubmit.prop('disabled', false); }, 3000);
    })

    /////////////////
    // MODAL AJAX  //
    /////////////////

    $(document).on('click', 'a[data-target="#modal_ajax"]', function(e) {
        e.preventDefault();
        /* Act on the event */
        var url = $(this).attr('href');
        $("#modal_ajax .modal-body" ).load( url, function() {
          $('#modal_ajax').modal('toggle');
        });
    });

    $('input[data-type="datepick"]').each(function(index, el) {
        var format;
        var time;
        if ($(this).attr('data-type') == 'datepick') {
            format = $(this).data('format');
            time = $(this).data('time');
            if(time!=undefined){
                 if(format!=undefined){
                     $(this).bootstrapMaterialDatePicker({ weekStart : 1, time: false, format: format});
                 }
                 else{
                     $(this).bootstrapMaterialDatePicker({ weekStart : 1, time: false});
                 }
            }
            else{
                $(this).bootstrapMaterialDatePicker({ weekStart : 1, format : 'DD-MM-YYYY', time: false});
            }
        }
    });

    //Formato label para checkbox material bootstrap
    $('input[label-data]').each(function(index, el) {
        $(this).parent().append(' ' + $(this).attr('label-data'));
    });

    // add class btn a links de actions table
    // $('table .actions a').addClass('button  small-button');
    // https://metroui.org.ua/datatables.html
    $('table').addClass('table striped hovered border');

    $('.dataTable').dataTable({
	    'searching' : true,
        'language': {
            'emptyTable':       'No hay datos en la tabla',
            'info':             'Mostrando página _PAGE_ de _PAGES_',
            'infoEmpty':        'No hay registros',
            'infoFiltered':     '(filtrando de un total de _MAX_ registros)',
            'infoPostFix':      '',
            'thousands':        '.',
            'lengthMenu':       'Mostrando _MENU_ registros por página',
            'loadingRecords':   'Cargando...',
            'processing':       'Procesando...',
            'search':           'Buscar:',
            'zeroRecords':      'No se ha encontrado nada',
            'paginate': {
                'first':    'Primera',
                'last':     'Última',
                'next':     'Siguiente',
                'previous': 'Anterior'
            },
            'aria': {
                'sortAscending':    ': presionar la columna para orden ascendente',
                'sortDescending':   ': presionar la columna para orden descendente'
            }
        }
	});

    //bootbox confirm
    $(document).on('click', 'a.confirm', function(e) {
        e.preventDefault();
        var btnUrl = $(this).attr('href');
        bootbox.confirm({
            message: "¿Está seguro que desea cambiar el estado de este registro?",
            title: "Confirmación",
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
                if (result) {
                    window.location.href = btnUrl;
                }
            }
        });
    });

    //bootbox approve
    //cambiamos el atributo onclick de los postlink con la clase approve
    //guardamos el nombre del form en un nuevo atributo data-form
    $('a.approve').each(function(index, el) {
        var btn_onclick = $(this).attr('onclick').split('.');
        $(this).attr('data-form', btn_onclick[1]);
        $(this).attr('onclick', false);
    });
    //si el bootbox se confirma realizamos el submit del postlink
    $(document).on('click', 'a.approve', function(e) {
        e.preventDefault();
        $btn = $(this);
        bootbox.confirm({
            message: "¿Está seguro que desea aprobar este registro?",
            title: "Confirmación",
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
                if (result) {
                    $('form[name="' + $btn.attr('data-form') + '"').submit();
                }
            }
        });
    });
});

function numberWithPoints(x) {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return parts.join(".");
}

var theSign = "$ ";
function autoNumericInit(operation){
    $('input.ldz_numeric').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: theSign
        // aSign: '€ '
    });  //autoNumeric with options being passed

    $('.ldz_numeric_no_sign').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: ''
    });  //autoNumeric with options being passed
}

function autoNumericGetSignByValue(value){
    if( value == 1 ){
        //PESO
        theSign = "$ ";
    }else if( value == 2 ){
        //DOLAR
        theSign = "USD ";
    }else if( value == 3 ){
        //UF
        theSign = "UF ";
    }
}

function isIE(userAgent) {
  userAgent = userAgent || navigator.userAgent;
  return userAgent.indexOf("MSIE ") > -1 || userAgent.indexOf("Trident/") > -1 || userAgent.indexOf("Edge/") > -1;
}

function moneda(n) {
    var parts=n.toString().split(".");
    return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".") + (parts[1] ? "," + parts[1] : "");
}