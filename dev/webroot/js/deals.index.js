$(document).ready(function(){
    //cambiamos el atributo onclick de los postlink con la clase approve
    //guardamos el nombre del form en un nuevo atributo data-form
    $('a.confirm_reason').each(function(index, el) {
        var btn_onclick = $(this).attr('onclick').split('.');
        $(this).attr('data-form', btn_onclick[1]);
        $(this).attr('onclick', false);
    });
    $(document).on('click', '.confirm_reason', function(event) {
        var _this = $(this);
        event.preventDefault();
        bootbox.confirm({
            title: "Confirme por favor",
            message: '<p>¿Está seguro que desea cambiar el estado de este registro?</p>' +
                '<div class="row"><div class="col-xs-12 col-md-12">'+
                    '<label for="'+_this.attr('data-form')+'_comment" class="col-md-12 col-xs-12">Favor indicar razón de rechazo</label>'+
                    '<textarea id="'+_this.attr('data-form')+'_comment" name="comment" class="col-md-12 col-xs-12" style="resize: none;"></textarea>'+
                '</div></div>',
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
                    inputComment='<input name="comment" type="hidden" value="'+$('#'+_this.attr('data-form')+'_comment').val()+'">';
                    $('form[name="' + _this.attr('data-form') + '"').append(inputComment);
                    $('form[name="' + _this.attr('data-form') + '"').submit();
                    // _this.closest('form').submit();
                }
            }
        });
    });
});