function removeWorker(tWorker) {
    var $val = $(tWorker).closest('tr').attr('worker');
    var $nam = $(tWorker).closest('tr').attr('workername');
    $("#worker-id").append("<option value='" + $val + "'>" + $nam + "</option>");
    $(tWorker).closest('tr').remove();
    $('#worker-id').change();
    $('#workerstable  input').each(function() {
        var digits  = $(this).attr('name').match(/\d+/g);
        var newName = '';
        var j = $(this).closest('tr').index();
        if(digits.length < 2) {
            newName = $(this).attr('name').replace(/\d+/,j);
        } else {
            k = $(this).closest('td').find('.workerItems').index($(this).closest('.workerItems'));
            newName = $(this).attr('name').replace(/\d+/,j);
            newName = newName.replace(/(\d+)(?!.*\d)/,k);
        }
        $(this).attr('name',newName);
    });
}

$( document ).ready(function() {
    autoNumericGetSignByValue(1);
    autoNumericInit('init');
    $(document).on('click', '#btnModalCancelar', function () {
        $('#modalCancel').modal('show');
    });

    $("#worker-id").select2({
        placeholder: "Seleccione trabajadores",
        allowClear: true,
        dropdownAutoWidth : true,
        containerCss : {"display":"block"}
        });
    $i = 0;

    $(function () {
        $('input[data-type="datetimepicker"]').datetimepicker({
            locale: 'es',
            // inline: true,
            format: 'DD/MM/YYYY',
            extraFormats: [ 'DD.MM.YY' ]

        });
    });

    $('#start-date').on('change', function(e, date){
        var day = date.toDate().getDay();
        var isWeekend = (day == 6) || (day == 0);
        if(!isWeekend) {
            $('#modalDate').modal('show');
        }
    });

    $(document).on('click', '#nonfestive', function () {
        $('#start-date').val("");
    });

    $('#worker-id').on('change', function() {
        if(this.value) {
            if($('#trWorker-' + this.value).length > 0 ) {

            }
            else {
                var worker = null;
                var trabId = null;
                var max_bonus = null;
                trabId = this.value;
                $.each(fichas, function(i, v) {
                    if(v.ficha == trabId) {
                        worker = v;
                        return false;
                    }
                });
                // console.log(charges);
                $.each(charges, function(i, v) {
                    if(worker.Cargo.cod_cargo == i) {
                        max_bonus = v;
                        return false;
                    }
                });
                $('#workerstable tr:last').after(
                '<tr id="trWorker-' + this.value + '" worker="' + this. value + '" workername="' + worker.nombres + '">' +
                    '<td><button type="button" class="removebutton" title="Quitar a trabajador del bono">X</button></td>' +
                    '<td>' + worker.rut + '</td>' +
                    '<td>' + worker.nombres + '</td>' +
                    '<td>' + worker.Cargo.nombre_cargo + '</td>' +
                    '<td><input class="form-control" type="hidden" name="workers[' + $i + '][id]" id="state" value="' + this.value + '">' +
                        '<input class="form-control ldz_numeric text-right" type="text" name="workers[' + $i + '][amount]" required="required" id="amount-' + this.value + '" max="' + max_bonus + '">' +
                    '</td></tr>');
                $("#worker-id option[value=" + this.value + "]").remove();
                if($('#worker-id option').size() < 1) {
                    $('#modalWorkers').modal('hide');
                 }
                $i++;
                autoNumericInit('init');
                autoNumericInit('update');
            }
        }

    });
    var TargetWorker = null;
    $(document).on('click', 'button.removebutton', function () {
        TargetWorker = $(this);
        $('#modalRemoveWorker').modal('show');
        return false;
    });

    $(document).on('click', '#workerRemove', function () {
        $('#modalRemoveWorker').modal('hide');
        removeWorker(TargetWorker);
        return false;
    });

    $( "#formWorker" ).click(function( event ) {
        event.preventDefault();
        if($('#worker-id option').size() < 1) {
            $('#modalWorkers .form-group').hide();
            $('#noworkers').show();

        } else {
            $('#noworkers').hide();
            $('#modalWorkers .form-group').show();
        }
        $('#modalWorkers').modal('show');
    });

    $('button[data-send-form="true"]').click(function(e){
        e.preventDefault();
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
        // console.log($(this).parent().serialize());
        $(this).parent().submit();
    })


    /*var table = $('table.table-item').DataTable({
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
    });*/


});

