sumjq = function(selector) {
    var sum = 0;
    $(selector).each(function() {
        sum += Number($(this).val());
    });
    sum = 100 - sum;
    return sum;
}

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
            extraFormats: [ 'DD.MM.YY' ]
        });
    });
    var j = 0;
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
                $.each(charges, function(i, v) {
                    if(worker.Cargo.cod_cargo == i) {
                        max_bonus = v;
                        return false;
                    }
                });
                j = $('#workerstable >tbody:last >tr').length;
                $('#workerstable tr:last').after(
                    '<tr id="trWorker-' + this.value + '" worker="' + this. value + '" workername="' + worker.nombres + '">' +
                            '<td><button type="button" class="removebutton" title="Quitar trabajador del bono">X</button></td>' +
                            '<td>' + worker.rut + '</td>' +
                            '<td>' + worker.nombres + '</td>' +
                            '<td>' + worker.Cargo.nombre_cargo + '</td>' +
                            '<td>' +
                                '<input class="form-control" type="hidden" name="workers[' + j + '][worker_id]" id="state" value="' + worker.id + '">' +
                                '<input class="form-control ldz_numeric text-right" type="text" name="workers[' + j + '][amount]" required="required" id="amount-' + this.value + '" max="' + max_bonus + '">' +
                            '</td>' +
                            '<td id="tdWorker-' + worker.id + '-items"><a href="#" id="formItems" class="formItems btn btn-sm btn-primary" data-worker-id="' + worker.id + '">Agregar<div class="ripple-wrapper"></div></a></td>' +
                    '</tr>');
                $("#worker-id option[value=" + this.value + "]").remove();
                if($('#worker-id').has('option').length < 1) {
                    $('#modalWorkers').modal('hide');
                }
                j++;
                autoNumericInit('init');
                autoNumericInit('update');
            }
        }

    });
    $(document).on('click', "#formWorker", function () {
    //$("#formWorker").click(function( event ) {
        if($('#worker-id option').size() < 1) {
            $('#modalWorkers .form-group').hide();
            $('#noworkers').show();

        } else {
            $('#noworkers').hide();
            $('#modalWorkers .form-group').show();
        }
        $('#modalWorkers').modal('show');
        return false;
    });

    var i = 0;
    var divTarget = null;
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


    $(document).on('click', ".formItems", function () {
        var workerId = $(this).data('worker-id');
        $('input[name="worker_id"]').val(workerId);
        if($('#percent').parent().parent().attr('class') == "form-group has-error") {
                    $('#percent').parent().parent().attr('class',"form-group");
                }

        if($('#budgetitems-id').parent().parent().attr('class') == "form-group has-error") {
                    $('#budgetitems-id').parent().parent().attr('class',"form-group");
                }
        var sumPercent = sumjq($("#tdWorker-" + workerId + "-items :input[name*='itemPercent']"));
        $('#percent').attr('data-hint','El porcentaje no puede ser mayor a ' + sumPercent);
        $('#percent').attr('placeholder','El porcentaje no puede ser mayor a ' + sumPercent);
        $('#percent').next().html('El porcentaje no puede ser mayor a ' + sumPercent);

        $('#budgetitems-id').val([]);
        $('#percent').val([]);
        $('#modalItems').modal('show');
        return false;
    });

    $(document).on("contextmenu", '.workerItems', function(e) {
        event.preventDefault();
        divTarget = e.currentTarget;
        $('#modalRemoveItem').modal('show');
    });

    $("#budgetItemRemove").click(function( event ) {
        divTarget.remove();
    });

    $("#budgetItemAdd").click(function( event ) {
        var url = window.location.href.split('/');
        var workerId = $('input[name="worker_id"]').val();
        var itemId = $('#budgetitems-id').val();
        var itemText = $('#budgetitems-id').find("option:selected").text();
        var itemPercent = $('#percent').val();
        console.log('workerId: ' + workerId + ', itemId: ' + itemId + ', percent: ' + itemPercent);
        if (itemText && itemPercent) {
            var sumPercent = sumjq($("#tdWorker-" + workerId + "-items :input[name*='itemPercent']"));
            totalPercent = sumPercent - itemPercent;
            if (totalPercent < 0) {
                if($('#percent').parent().parent().attr('class') == "form-group") {
                    $('#percent').parent().parent().attr('class',"form-group has-error");
                    $('#percent').attr('data-hint','El porcentaje no puede ser mayor a ' + sumPercent);
                    $('#percent').next().html('El porcentaje no puede ser mayor a ' + sumPercent);
                }
                return false;
            }
            else
            {
                $('#percent').parent().attr('class', "form-group");
            }
            if ($('#div-worker' + workerId + '-item' + itemId).length) {
                if ($('#budgetitems-id').parent().parent().attr('class') == "form-group") {
                    $('#budgetitems-id').parent().parent().attr('class',"form-group has-error");
                    $('#budgetitems-id').attr('data-hint','La partida seleccionada ya esta asignada al trabajador.');
                    $('#budgetitems-id').next().html('La partida seleccionada ya esta asignada al trabajador.');
                }
                if ($('#budgetitems-id').parent().parent().attr('class') == "form-group has-error") {
                    $('#budgetitems-id').focus();
                }
                return false;
            }
            var itemOnly = itemText.split(" ");
            k = $('#tdWorker-' + workerId + '-items').parent().index();
            l = $('#tdWorker-' + workerId + '-items .workerItems').size();
            var path_name_url = window.location.pathname.split('/');
            var bugdet_item_url = '';
            ($.inArray('dev', url)) ? bugdet_item_url = window.location.origin + '/' + path_name_url[1] + '/' + path_name_url[2] + '/BudgetItems/view/' + itemId :
                bugdet_item_url = window.location.origin + '/BudgetItems/view/' + itemId;
            $('#tdWorker-' + workerId + '-items').append('<div id="div-worker' + workerId + '-item' + itemId + '"class="workerItems" workerId="' + workerId + '" itemId="' + itemId + '">' +
                '<input class="form-control" type="hidden" name="workers[' + k + '][partidas][' + l + '][itemId]" value="' + itemId + '">' +
                '<input class="form-control" type="hidden" name="workers[' + k + '][partidas][' + l + '][itemPercent]" value="' + itemPercent + '">' +
                '<input class="form-control" type="hidden" name="workers[' + k + '][partidas][' + l + '][itemOnly]" value="' + itemOnly[0] + '">' +
                '<a href="' + bugdet_item_url + '" id="worker-' + workerId + 'item-' + itemId + '" data-target="#modal_ajax" data-toggle="#modal_ajax"> P: ' + itemOnly[0] + ' / ' + itemPercent + '%</a></div>');
            i++;
            $('#modalItems').modal('hide');
        }
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
});