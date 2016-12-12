jQuery(document).ready(function($) {
	$('#excel').on({
    'mouseenter': function() {
        var $row = $(this);
        if (!$row.data("bs.tooltip")) {
            $row.tooltip({
                    container: 'body',
                    html: true,
                    trigger: 'manual',
                    title: function() {
                        return $(this).attr('txt');
                    }
                });
        }
        $row.tooltip('show');
    },
    'mouseleave': function() {
        $(this).tooltip('hide');
    }
  },'tbody > tr.danger');

  $( "button.mdConfirm" ).click(function( event ) {
    event.preventDefault();
    $('#confirmDiag').modal('show');
    
  });
  $( "button.mdCancel" ).click(function( event ) {
    event.preventDefault();
    $('#cancelDiag').modal('show');    
  });

});