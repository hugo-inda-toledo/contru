$(document).ready(function() {
    autoNumericInit('init');
    $('.percent .form-control').each(function(index, el) {
        // var percent = $(this).val();
        var percent = $(this).autoNumeric('get');
        var unit_i = $(this).parent().parent().next().find('.form-control');
        // var quantity = unit_i.attr('max');
        var quantity = unit_i.data('v-max');
        var unit = ((percent/100) * quantity).toFixed(2);
        // unit_i.val(unit);
        unit_i.autoNumeric('set', unit);
    });
    $(document.body).on('focusin', '.unit .form-control', function(){
        if($(this).val() == '0.00' || $(this).val() == '0') $(this).val('');
    });
   $(document.body).on('focusout', '.unit .form-control', function(){
        if($(this).val() == '') $(this).val('0.00');
    });
    $(document.body).on('focusin', '.percent .form-control', function(){
        if($(this).val() == 0) $(this).val('');

    });
    $(document.body).on('focusout', '.percent .form-control', function(){
        if($(this).val() == '') $(this).val(0);

    });
    /** Calcula unidad a porcentaje en cada item */
    $(document.body).on('change', '.unit .form-control', function(){
        var el_amount_proyected = $(this).parents('tr').find('.monto_proyectado');
        // var unit = $(this).val();
        var unit = $(this).autoNumeric('get');
        var total = $(this).data('v-max');
        var percent_i = $(this).parent().parent().prev().find('.form-control');
        var per = ((unit/total) * 100).toFixed(2);
        /*if(isIE()){
            var per = per.replace('.', ',');
        }*/
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
    $(document.body).on('change', '.percent .form-control', function(){
        var el_amount_proyected = $(this).parents('tr').find('.monto_proyectado');
        // var percent = $(this).val();
        var percent = $(this).autoNumeric('get');
        var unit_i = $(this).parent().parent().next().find('.form-control');
        // var quantity = unit_i.attr('max');
        var quantity = unit_i.data('v-max');
        var unit = ((percent/100) * quantity).toFixed(2);
        /*if(isIE()){
            var unit = unit.replace('.', ',');
        }
        unit_i.val(unit);*/
        unit_i.autoNumeric('set', unit);
        var total_price = parseFloat(el_amount_proyected.data('total-price'));
        var amount_proyected = ((percent*total_price) / 100).toFixed(2);
        el_amount_proyected.autoNumeric('set', amount_proyected);
        // if(isIE) amount_proyected = amount_proyected.replace('.', ',');
        // el_amount_proyected.html(amount_proyected);
        updateTotales();
    });
    $(window).scroll(function(){
        var aTop = $('.table-progress').position().top;
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

    $('#items').change(function(){
    });
    $('.add_no_scheduled').click(function(e){
        e.preventDefault();
        if($('#items').val()!=""){
            var budgetItemId = $('#items').val();
            var optionSelected=$('#items').find('option[value="'+budgetItemId+'"]');
            var budgetItemDesc = optionSelected.text();
            var unit=optionSelected.data('unit');
            var quantity=optionSelected.data('quantity');
            var unity_price=optionSelected.data('unity-price');
            var total_price=optionSelected.data('total-price');
            var avance_real_monto=(parseInt(optionSelected.data('overall-progress'))*parseInt(total_price))/100;
            var max=optionSelected.data('max');
            var item=optionSelected.data('item');
            var description=optionSelected.data('description');
            if($('.table-progress').find('tr[data-item="'+budgetItemId+'"]').length<1){
                $('.table-progress .totales').before(
                    '<tr class="incomplete" data-item="'+budgetItemId+'">'+
                        '<td>'+
                            item+' <input class="form-control" type="hidden" name="BudgetItems['+budgetItemId+'][progress_id]" id="budgetitems-'+budgetItemId+'-progress-id">'+
                            '<input class="form-control" type="hidden" name="BudgetItems['+budgetItemId+'][proyected_progress_percent]" id="budgetitems-'+budgetItemId+'-proyected-progress-percent">'+
                        '</td>'+
                        '<td class="text-left">'+description+'</td>'+
                        '<td class="text-left">'+unit+'</td>'+
                        '<td class="text-right ldz_numeric_no_sign">'+quantity+'</td>'+
                        '<td class="text-right">'+unity_price+'</td>'+
                        '<td class="text-right ldz_numeric_no_sign">'+total_price+'</td>'+
                        '<td class="text-right">'+
                            '0'+
                            '<div class="progress">'+
                                '<div class="progress-bar progress-bar-material-orange-0" style="width: 0%"></div>'+
                            '</div>'+
                        '</td>'+
                        '<td class="text-right">'+
                            '0'+
                            '<div class="progress">'+
                                '<div class="progress-bar progress-bar-material-orange-0" style="width: 0%"></div>'+
                            '</div>'+
                        '</td>'+
                        '<td class="text-right">0</td>'+
                        '<td class="percent input-inline text-right">'+
                            '<div class="units text-right">'+
                                '<input class="form-control ldz_numeric_no_sign" type="text" name="BudgetItems['+budgetItemId+'][overall_progress_percent]" id="per_'+budgetItemId+'" data-v-min="0" data-v-max="100" value="0">'+
                                '<span>%</span>'+
                            '</div>'+
                        '</td>'+
                        '<td class="unit input-inline text-right">'+
                            '<div class="units text-right">'+
                                '<input class="form-control ldz_numeric_no_sign" type="text" name="BudgetItems['+budgetItemId+'][overall_progress_unit]" id="unit_'+budgetItemId+'" data-v-min="0.00" data-v-max="'+max+'" value="0" data-input="avance_real">'+
                                '<span></span>'+
                            '</div>'+
                        '</td>'+
                        '<td class="text-right monto_proyectado ldz_numeric_no_sign" data-total-price="'+total_price+'">'+avance_real_monto+'</td>'+
                    '</tr>'
                );
            }
        }
        autoNumericInit('init');
        autoNumericInit('update');
    });


    // Se hace como función para que se actualice dinámicamente y no copypastear un pedazo de código
    function updateTotales(){
        var suma_real = 0;
        var suma_real_monto = 0;
        var suma_proyectado = 0;
        $(document.body).find('.avance_proyectado').each(function(){
            // item = $(this).text().replace(/\./g, ',');
            item = $(this).autoNumeric('get');
            suma_proyectado += parseFloat(item);

        });
        // se buscan y suman los avances proyectados
        $(document.body).find('input[data-input="avance_real"]').each(function(){
            // item = ($(this).val()!="")?$(this).val():0;
            item = $(this).autoNumeric('get');
            suma_real += parseFloat(item);
        });
        $(document.body).find('.monto_proyectado').each(function(){
            // item = ($(this).text()!="")?$(this).text():0;
            // item = item.replace(/\./g, '');
            item = $(this).autoNumeric('get');
            suma_real_monto += parseFloat(item);
        });
        // se muestran los avances proyectados
        // $('.suma_real').html(suma_real.toFixed(2).replace(/\./g, ','));
        $('.suma_proyectado').autoNumeric('set', suma_proyectado.toFixed(2));
        $('.suma_real').autoNumeric('set', suma_real.toFixed(2));
        $('.suma_real_monto').autoNumeric('set', suma_real_monto.toFixed(2));
        // $('.suma_real_monto').html(suma_real_monto.toFixed(2).replace(/\./g, ','));
    }
    updateTotales();

    $('button[type="submit"]').click(function(e){
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