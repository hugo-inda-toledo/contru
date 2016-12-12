$(document).ready(function() {
    autoNumericInit('init');
    $(function () {
        $('input[data-type="datetimepicker"]').datetimepicker({
            locale: 'es',
            useCurrent: false,
            // inline: true,
            format: 'DD-MM-YYYY',
            extraFormats: [ 'DD.MM.YY' ],
            daysOfWeekDisabled: [0, 2, 3, 4, 5, 6],

        });
        $('table.table-item').DataTable({
            "paging": false,
            //pageLength: 100,
            "ordering" :false,
            // orderable: false,
            // "deferRender": true, //sólo ajax
            "fixedHeader": false,
            "processing": true,
            "dom": '<"top"fi>rt',
            "columnDefs": [ {
                "targets": [0, 2, 3, 4, 5],
                "searchable": false
            } ],
            // "search": {
            //     "search": "1.1 Inst"
            // },
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
        /** Habilita/Deshabilita input al hacer check */
        $('input[type=checkbox]').on('click',function(){
            autoNumericInit('update');
            var id = $(this).attr('data-disabled');
            var per = "#per"+ id;
            var unit = "#unit"+ id;
            if($(this).prop('checked')){
                $(per).prop('disabled',false);
                $(unit).prop('disabled',false);
                // if($(per).val() == '0') $(per).val('');
                if($(per).val() == '0.00' || $(per).val() == '0') $(per).val('');
                if($(unit).val() == '0.00' || $(unit).val() == '0') $(unit).val('');
                // console.log(id);
            } else {
                $(per).prop('disabled',true);
                $(unit).prop('disabled',true);
                if($(per).val() == '') $(per).val('0.00');
                if($(unit).val() == '') $(unit).val('0.00');
            }
            updateTotales();
        });
        /** Calcula unidad a porcentaje en cada item */
        $('.unit').on('change','.form-control',function(){
            var el_amount_proyected = $(this).parents('tr').find('.monto_proyectado');
            // var unit = $(this).val();
            var unit = $(this).autoNumeric('get');

            var total = $(this).data('v-max');
            var percent_i = $(this).parent().parent().parent().prev().find('.form-control');
            var per = ((unit/total) * 100).toFixed(2);
            // if(isIE()){
                // var per = per.replace('.', ',');
            // }
            // console.log(per);
            // percent_i.val(per);
            percent_i.autoNumeric('set', per);
            var total_price = parseFloat(el_amount_proyected.data('total-price'));
            var amount_proyected = ((per*total_price) / 100).toFixed(2);
            // if(isIE) amount_proyected = amount_proyected.replace('.', ',');
            // el_amount_proyected.html(amount_proyected);
            el_amount_proyected.autoNumeric('set', amount_proyected);
            updateTotales();
        });
        /** Calcula porcentaje a unidad en cada item */
        $('.percent').on('change','.form-control',function(){
            var el_amount_proyected = $(this).parents('tr').find('.monto_proyectado');
            // var percent = $(this).val();
            var percent = $(this).autoNumeric('get');
            var unit_i = $(this).parent().parent().parent().next().find('.form-control');
            var quantity = unit_i.data('v-max');
            var total_price = parseFloat(el_amount_proyected.data('total-price'));
            var unit = ((percent/100) * quantity).toFixed(2);
            var amount_proyected = ((percent*total_price) / 100).toFixed(2);
            /*if(isIE()){
                var unit = unit.replace('.', ',');
                var amount_proyected = amount_proyected.replace('.', ',');
            }*/
            // unit_i.val(unit);
            unit_i.autoNumeric('set', unit);
            // el_amount_proyected.html(amount_proyected);
            el_amount_proyected.autoNumeric('set', amount_proyected);
            updateTotales();
        });
        /** Muestra/Esconde items completados y deshabiltados */
        $('#show-all').click(function(){
            $('.table .done, .table .disabled').toggleClass('hidden');
        });
    });
    $(window).scroll(function(){
        var aTop = $('.dataTable').offset().top;
        if($(this).scrollTop()>=aTop){
            $('#header_budget').css({
                'position': 'fixed',
                'top': '0',
                'background-color': '#fff',
                'z-index':  '1'
            });
        }else{
            $('#header_budget').css({
                'position': 'relative'
            });
        }
    });
    // Se hace como función para que se actualice dinámicamente y no copypastear un pedazo de código
    function updateTotales(){
        var suma_proyectado = 0;
        var suma_real = 0;
        var suma_real_monto = 0;
        // se buscan y suman los avances proyectados
        $(document.body).find('.monto_proyectado').each(function(){
            // item = $(this).text().replace(/\./g, ',');
            item = $(this).autoNumeric('get');
            suma_proyectado += parseFloat(item);

        });

        /*$(document.body).find('input[data-input="avance_proyectado"]').each(function(){
            item = ($(this).val()!="")?$(this).val():0;
            if($(this).is(':disabled') == true)
                item=0;
            suma_proyectado += parseFloat(item);
        });*/
        // se muestran los avances proyectados
        $('.suma_proyectado').autoNumeric('set', suma_proyectado.toFixed(2));
        // $('.suma_proyectado').html(suma_proyectado.toFixed(2).replace(/\./g, ','));
        // se buscan y suman los avances reales
        $(document.body).find('td[data-avance-real-cantidad]').each(function(){
            suma_real += parseFloat($(this).data('avance-real-cantidad'));
        });
        $(document.body).find('td[data-avance-real-monto]').each(function(){
            suma_real_monto += parseFloat($(this).data('avance-real-monto'));
        });
        // se muestran los avances reales
        $('.suma_real').autoNumeric('set', suma_real.toFixed(2));
        $('.suma_real_monto').autoNumeric('set', suma_real_monto.toFixed(2));
    }
    updateTotales();

    /*$('button[type="submit"]').click(function(e){
        e.preventDefault();
        console.log('enviar form');
        console.log($('form'));
        if (!$('form')[0].checkValidity()) {
            $('form').find('input[required="required"]').each(function(){
                if($(this).val()==""){

                }
            })
        }else{
            $('form').submit();
            return false;
        }
    });*/



    $('form').submit(function(e){
        e.preventDefault();
        var submit = true;

        // validaciones html5
        if( !$(this)[0].checkValidity())
            submit = false;

        // Validaciones extras
        $(this).find('input:checked').each(function(){
            $(this).parents('tr').find('input[type="number"]').each(function(){
                if($(this).val()=="" || $(this).val()==0){
                    submit = false;
                    $(this).parent().addClass('has-error');
                    $(this).next().next().html('Completar campo.');
                }
            })
        });

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
});