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
				action: 'dplrwoo_ajax_connect',
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

		$('.dplrwoo-mapping-fields').focus(function(){
			$(this).data('val', $(this).val());
		}).change(function(previous){
			
			var prev = $(this).data('val');
			var current = $(this).val();
			
			$(this).data('val', current);

			if(prev!==''){
				$('.dplrwoo-mapping-fields').not(this).append('<option value="'+prev+'">'+prev+'</option>');
			}

			if(current!==''){
				var s = $('.dplrwoo-mapping-fields').not(this);
				s.find('option[value="'+current+'"]').remove();
			}

		});

		if($("#dprwoo-tbl-lists").length>0){
			loadLists(1);
		}

		$("#dplrwoo-save-list").click(function(e){

			e.preventDefault();			
			var listName = $(this).closest('form').find('input[type="text"]').val();

			if(listName!==''){
				
				var data = {
					action: 'dplrwoo_ajax_save_list',
					listName: listName
				};

				listsLoading();

				$.post( ajaxurl, data, function( response ) {

					var body = 	JSON.parse(response);
					
					if(body.createdResourceId){
						
						var html ='<tr>';
						html+='<td>'+body.createdResourceId+'</td><td>'+listName+'</td>';
						html+='<td>0</td>';
						html+='<td><a href="#" data-list-id="'+body.createdResourceId+'">Delete</a></td>'
						html+='</tr>';

						$("#dprwoo-tbl-lists tbody").prepend(html);
						//$("#dprwoo-tbl-lists tbody tr a").on("click",deleteList);

					}else{
						
						if(body.status == '400'){
							alert(body.title);
						}
					}

					listsLoaded();

				});
			
			}

		});

		
		if($('#dplr-dialog-confirm').length>0){
			
			$("#dplr-dialog-confirm").dialog({
				autoOpen: false,
				resizable: false,
				height: "auto",
				width: 400,
				modal: true
			});
		
		}

		$("#dprwoo-tbl-lists tbody").on("click","tr a",deleteList);

	});
	
	function listsLoading(){
		$('form input, form button').prop('disabled', true);
		$('#dplrwoo-crud').addClass('loading');
	}

	function listsLoaded(){
		$('form input, form button').prop('disabled', false);
		$('form input').val('');
		$('#dplrwoo-crud').removeClass('loading');
	}

	function loadLists( page ){

		var data = {
			action: 'dplrwoo_ajax_get_lists',
			page: page
		};
		
		listsLoading();

		$("#dprwoo-tbl-lists tbody tr").remove();

		$.post( ajaxurl, data, function( response ) {
	
			if(response.length>0){

				var obj = JSON.parse(response);
				var html = '';
				
				for (const key in obj) {
					
					var value = obj[key];
					
					html+='<tr>';
					html+='<td>'+value.listId+'</td><td>'+value.name+'</td>';
					html+='<td>'+value.subscribersCount+'</td>';
					html+='<td><a href="#" data-list-id="'+value.listId+'">Delete</a></td>'
					html+='</tr>';
					
				}

				$("#dprwoo-tbl-lists tbody").append(html);
				$("#dprwoo-tbl-lists").attr('data-page','1');
				
				listsLoaded();
			}

		})
	}

	function deleteList(e){

		e.preventDefault();

		var a = $(this);
		var tr = a.closest('tr');
		var listId = a.attr('data-list-id');
		var data = {
			action: 'dplrwoo_ajax_delete_list',
			listId : listId
		};
		
		$("#dplr-dialog-confirm").dialog("option", "buttons", [{
			text: 'Delete',
			click: function() {
				$(this).dialog("close");
				tr.addClass('deleting');
				$.post( ajaxurl, data, function( response ) {
					var obj = JSON.parse(response);
					if(obj.response.code == 200){
						tr.remove();
					}else{
						if(obj.response.code == 0){
							alert('No se puede eliminar lista.')
						}else{
							alert('Error');
						}
						tr.removeClass('deleting');
					}
				});
			}
		  }, 
		  {
			text: 'Cancel',
			click: function() {
			  $(this).dialog("close");
			}
		  }]);
  
		  $("#dplr-dialog-confirm").dialog("open");

	}

})( jQuery );