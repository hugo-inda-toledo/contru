$(function(){
    $(document).ready(function() {
        $("tr.with_data td").each( function(index, el){
            $(el).parentsUntil(".panel-heading").parent().addClass("panel_with_data");
        } );

        $('.highlight_with_data').click(function(e) {
            $("tr.with_data td").each( function(index, el){
                $(el).toggleClass("bg-success");
            });

            $(".panel.panel_with_data").each( function(index, el){
                $(el).toggleClass("panel-success").toggleClass("panel-material-blue-grey-700");
            });
        });

        //toggle currency
        $('a[data-toggle="currency-total"]').click(function(e) {
            e.preventDefault();
            // $tableSpans = $('span.total_price');
            // $tableSpans.each(function(index, el) {
            //     if ($(this).hasClass('currency')) {
            //         $(this).text($(this).attr('data-original'));
            //         $(this).removeClass('currency');
            //         $('a[data-toggle="currency-total"]').text('moneda');
            //     } else {
            //         var currency_value_to_date = $(this).attr('data-value') / $("input[name='currency_value_to_date']").val();
            //         $(this).text(currency_value_to_date.toFixed(2));
            //         $(this).addClass('currency');
            //         $('a[data-toggle="currency-total"]').text('pesos');
            //     }
            // });
        });
    });
    // No enviar form con Enter
    $(document).on("keypress", "#send_edp", function(event) {
        return event.keyCode != 13;
    });

    // Revisar correo
    function checkEmail(email){
        var email = $('#email').val();
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }

    // Mostrar modal o no
    $('#showAgreeBtn').click(function(e){
        var email = $('#email');
        var error = $('#error-correo');
        if(checkEmail(email.val())){
            email.parent().removeClass('has-error');
            error.addClass('hidden');
            $('#showAgree').modal({backdrop: 'static', keyboard: false});
            $('#showAgree').modal('show');
        }
        else{
            //correo invalido
           email.parent().addClass('has-error');
           error.removeClass('hidden');
        }

    });
    // Enviar correo
    $('.btn-enviar').click(function(){
        $('#body-prev').slideUp();
        $('#foot-buttons').hide();
        $('#close-button').hide();
        $('#body-load').show();
        $('#send_edp').submit();
    });
});
