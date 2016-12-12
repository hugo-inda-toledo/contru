jQuery(document).ready(function($) {
	jQuery.validator.setDefaults({
	  //debug: true,
	  success: "valid"
	});
	$( "#update_password" ).validate({
	  rules: {
	    password: "required",
	    confirm_password: {
	      equalTo: "#password"
	    }
	  }
	});
});