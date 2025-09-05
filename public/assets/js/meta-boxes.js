jQuery( document ).ready(function( $ ) {
	$('input[name="enlightenment_default_header_overlay"]').on('change', function() {
		if ( $(this).is(':checked') ) {
			$('#enlightenment_header_overlay').slideUp();
		} else {
			$('#enlightenment_header_overlay').slideDown();
		}
	});
});
