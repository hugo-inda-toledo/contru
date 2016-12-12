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

    $(function () {
        $('input[data-type="datetimepicker"]').datetimepicker({
            locale: 'es',
            useCurrent: false,
            // inline: true,
            format: 'DD-MM-YYYY',
            extraFormats: [ 'DD.MM.YY' ],
            // daysOfWeekDisabled: [1 ,2, 3, 4, 5],

        });
    });

    $i = 0;

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
                var max_deal = null;
                trabId = this.value;
                $.each(fichas, function(i, v) {
                    if(v.ficha == trabId) {
                        worker = v;
                        return false;
                    }
                });
                $.each(charges, function(i, v) {
                    if(worker.Cargo.cod_cargo == i) {
                        max_deal = v;
                        return false;
                    }
                });
                $('#workerstable tbody').append(
                '<tr id="trWorker-' + this.value + '" worker="' + this. value + '" workername="' + worker.nombres + '">' +
                    '<td><button type="button" class="removebutton" title="Quitar trabajador del trato">X</button></td>' +
                    '<td>' + worker.rut + '</td>' +
                    '<td>' + worker.nombres + '</td>' +
                    '<td>' + worker.Cargo.nombre_cargo + '</td>' +
                    '<td><input class="form-control" type="hidden" name="workers[' + $i + '][id]" id="state" value="' + this.value + '">' +
                        '<input class="form-control ldz_numeric" type="text" name="workers[' + $i + '][amount]" required id="amount-' + this.value + '" max="' + max_deal + '">' +
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
        $('.error-message').hide();
        if($('#worker-id option').size() < 1) {
            $('#modalWorkers .form-group').hide();
            $('#noworkers').show();

        } else {
            $('#noworkers').hide();
            $('#modalWorkers .form-group').show();
        }
        $('#modalWorkers').modal('show');
    });

    $('.form_submit').submit(function(e){
        e.preventDefault();
        var submit = true;

        if($('#workerstable tbody tr').length == 0){
            submit = false;
            // alert('agrega a alguien po larry');
            $('.error-message').html('Para continuar debe agregar a algún trabajador.');
            $('.error-message').show();
        }
        // validaciones html5
        if( !$(this)[0].checkValidity()){
            submit = false;
            $('.form_submit').find(':submit').click()
        }


        if( submit ){
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
            this.submit();
        }

        return false;
    })

    /*$('button[data-send-form="true"]').click(function(e){
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

        if (!$('.form_submit')[0].checkValidity()) {
              // If the form is invalid, submit it. The form won't actually submit;
              // this will just cause the browser to display the native HTML5 error messages.
              $('.form_submit').find(':submit').click()
        }
        // console.log($(this).parent().serialize());
        $(this).parent().submit();
    })*/

});

