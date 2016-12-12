$(function(){		
	//mostrar modal al hacer click en boton aprobar
    $('.approve-progress').on('click',function(e){
        e.preventDefault();
        //Set Id
        $('#approval-id').val($(this).data('schedule-id'));
        $('#approval-modal').modal('show');
    });
    $('.reject-progress').on('click',function(e){
    	e.preventDefault();        	
    	//show modal with textarea
    	$('#reject-progress-id').val($(this).data('schedule-id'));
    	$('#reject-modal').modal('show');
    });       
});


