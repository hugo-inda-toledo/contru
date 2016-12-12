jQuery(document).ready(function($) {
	$("#formsubmit2").click(function( event ) {
        $('#excelform').submit();
    });
    $( "#formcancel2" ).click(function( event ) {
        event.preventDefault();
        $('#confirmDiag').modal('show');

    });
    $( "#formcancel" ).click(function( event ) {
        event.preventDefault();
        $('#confirmDiag').modal('show');
    });
    $( "#confirmcancel" ).click(function( event ) {
        $('#confirmDiag').modal('hide');
    });
    var totalParent = 0;
    var sumParentsWithChildrens = {};
    $('#excel').find('tr').each(function(){
        if(typeof $(this).data('type') != undefined){
            if($(this).data('type') == "parent_with_childrens" || $(this).data('type') == "parent-0"){
                totalParent = 0;
                totalParentTarget = 0;
            }
            if($(this).data('type') == "childrens"){
                $(this).find('.total_price').each(function(){
                    if($(this).text()!=""){
                        var realTotal = $(this).data('real-value');
                        totalParent += parseFloat(realTotal);
                    }
                });
                $(this).find('.target_value').each(function(){
                    if($(this).text()!=""){
                        var realTotal = $(this).data('real-value');
                        totalParentTarget += parseFloat(realTotal);
                    }
                });
                if($(this).prevAll('tr[data-type="parent-0"]:first').position().top > $(this).prevAll('tr[data-type="parent_with_childrens"]:first').position().top){
                    var prevTr = $(this).prevAll('tr[data-type="parent-0"]:first');
                }else{
                    var prevTr = $(this).prevAll('tr[data-type="parent_with_childrens"]:first');
                }


                prevTr.attr('data-real-value', totalParent);
                prevTr.attr('data-real-value-target', totalParentTarget);
                convertToFloat(prevTr, totalParent);
                convertToFloat(prevTr, totalParentTarget, 'target_value');
                if(prevTr.prev().data('type') == "parent_with_childrens"){
                    sumParentsWithChildrens[prevTr.prev().data('item')] = ""+prevTr.prev().data('item');
                }
            }
        }
    });
    if(Object.keys(sumParentsWithChildrens).length>0){
        $.each(sumParentsWithChildrens, function(i, value){
            var hijos = $('#excel').find('tr[data-item^="'+value+'"][data-type="parent_with_childrens"]');
            if(hijos.length>0){
                var suma = 0;
                var sumaTarget = 0;
                hijos.each(function(){
                    if($(this).find('.total_price').text()!=""){
                        suma += parseFloat($(this).find('.total_price').text().replace('.', '').replace(',', '.'))
                    }
                    if($(this).find('.target_value').text()!=""){
                        sumaTarget += parseFloat($(this).find('.target_value').text().replace('.', '').replace(',', '.'))
                    }
                });
            }
            $('tr[data-item="'+value+'"]').attr('data-real-value', suma);
            convertToFloat($('tr[data-item="'+value+'"]'), suma);
            $('tr[data-item="'+value+'"]').attr('data-real-value-target', sumaTarget);
            convertToFloat($('tr[data-item="'+value+'"]'), sumaTarget, 'target_value');
            $('tr[data-item="'+value+'"]').attr('data-type', 'parent_with_childrens_of_'+value);
        });
    }
    $('#excel').find('tr[data-type="parent-0"]').each(function(){
        value = $(this).data('item');
        var suma = 0;
        var sumaTarget = 0;
        $('#excel').find('tr[data-item^="'+value+'"][data-type="parent_with_childrens"]').each(function(){
            if($(this).find('.total_price').text()!=""){
                var realTotal = $(this).data('real-value');
                suma += parseFloat(realTotal);
                // suma += parseFloat($(this).find('.total_price').text().replace('.', '').replace(',', '.'))
            }
            if($(this).find('.target_value').text()!=""){
                var realTotal = $(this).data('real-value-target');
                sumaTarget += parseFloat(realTotal);
            }
        });
        if($('tr[data-item="'+value+'"]').find('td.total_price').html()==""){
            convertToFloat($('tr[data-item="'+value+'"]'), suma);
        }
        if($('tr[data-item="'+value+'"]').find('td.target_value').html()=="" || $('tr[data-item="'+value+'"]').find('td.target_value').html()=="0,00"){
            convertToFloat($('tr[data-item="'+value+'"]'), sumaTarget, 'target_value');
        }
    });
    function convertToFloat(el, suma, clase){
        if(typeof clase == "undefined"){
            clase = "total_price";
        }
        el.find('td.'+clase).html(suma.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1\."));
        // el.find('td').eq(5).html((Math.round(suma * 100)/100).toString().replace('.', ',')/*.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1\.")*/);
    }
});