jQuery(document).ready(function($) {
    $('select.select2').select2({
        width: '500',
        language: 'es'
    });
    $(document).on('change', 'select#group-id', function(event) {
        event.preventDefault();
        $('select#building-id').siblings('label').css('display', 'block');
        $select_buildings = $('select#building-id').parent();
        switch($(this).find('option:selected').text()){
            case 'Visitador':
                $('select#building-id').prop({'disabled': false, 'multiple': true});
                $('select#building-id').parent().css('visibility', 'visible');
                break;
            case 'Admin Obra':
                $('select#building-id').prop({'disabled': false, 'multiple': false});
                $('select#building-id').parent().css('visibility', 'visible');
                break;
            case 'Asistente RRHH':
                $('select#building-id').prop({'disabled': false, 'multiple': false});
                $('select#building-id').parent().css('visibility', 'visible');
                break;
            case 'Oficina Técnica':
                $('select#building-id').prop({'disabled': false, 'multiple': false});
                $('select#building-id').parent().css('visibility', 'visible');
                break;
            default:
                $('select#building-id').prop('disabled', true);
                $('select#building-id').parent().css('visibility', 'hidden');
                break;
        }
    });
});