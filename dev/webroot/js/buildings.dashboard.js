$(document).ready(function(){
    $( "#formComment" ).click(function( event ) {
        event.preventDefault();
        $('#modalComment').modal('show');
    });
    $( "#formState" ).click(function( event ) {
        event.preventDefault();
        $('#modalState').modal('show');
    });
    $( "#formDelete" ).click(function( event ) {
        event.preventDefault();
        $('#modalDelete').modal('show');
    });
    $( "#formDeleteItems" ).click(function( event ) {
        event.preventDefault();
        $('#modalDeleteItems').modal('show');
    });
});

