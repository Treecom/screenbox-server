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

	// pick files button
	$('button[class*="files"]')
		.click(function(){
			console.log('files click');	
			$('#filesmodal').modal('show');
			$('#filesmodal')
				.find('.modal-body')
				.load('/files/filer:video', null, function(){
					$(this).find('li a').click(function(){
						var id = $(this).attr('data-fileid');
						$('#filesmodal').modal('hide');
						$('input[name*="file_id"]').attr('value',id);
					})
				});
		})
		.attr('data-toggle','modal')
		.attr('data-target','filesmodal')
		.after('<div id="filesmodal" class="modal hide fade"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
				+'<h3>Browse files</h3></div>'
				+'<div class="modal-body"><p>Loading file browser…</p></div>'
				+'<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a><a href="#" class="btn btn-primary">Pick file</a></div></div>');
});