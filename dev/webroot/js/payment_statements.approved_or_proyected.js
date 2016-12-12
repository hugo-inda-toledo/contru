$(function(){
	$('.approve-btn').click(function(e){
		e.preventDefault();
		$('#modal-approve').modal('show');
	});

	$('.reject-btn').click(function(e){
		e.preventDefault();
		$('#modal-reject').modal('show');
	});
});