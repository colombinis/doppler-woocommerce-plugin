(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {

		$('#dplrwoo-form-connect').submit(function(e){

			e.preventDefault();

			var f = $(this);
			var button = f.children('button');
			var userfield = $('input[name="dplrwoo_user"]');
			var keyfield = $('input[name="dplrwoo_key"]');

			var data = {
				action: 'dplrwoo_connect',
				user: userfield.val(),
				key: keyfield.val()
			}

			$('.doppler-woo-settings .error').remove();
			$('#dplrwoo-messages').html('');

			if(data.user === ''){
				userfield.after('<span class="error">Mensaje de error</span>');
			}

			if(data.key === ''){
				keyfield.after('<span class="error">Mensaje de error</span>');
			}

			if( data.user === '' || data.key === '' ){
				return false;
			}
			
			button.attr('disabled','disabled');

			$.post( ajaxurl, data, function( response ) {
		
				if(response == 0){
					$("#dplrwoo-messages").html('Mensaje de datos incorrectos');
					return false;
				}else if(response == 1){
					
					var fields =  f.serialize();
					$.post( 'options.php', fields, function(obj){
						window.location.reload(false); 					
					});
				
				}

				button.attr('disabled','');

			})
		
		}); 
	
	});
	

})( jQuery );
