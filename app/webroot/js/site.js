/* site.js */

$(document).ready(function() {
	
	// menu drop down
	$('.dropdown-toggle').dropdown();

	// bootstrap additions
	$(".alert").alert();
	// restyle some of cake css
	$('.message').addClass('alert').prepend('<button type="button" class="close" data-dismiss="alert">×</button>').alert();	
	$('.error-message').addClass('label label-important');
	$('.input').addClass('control-group');	
});