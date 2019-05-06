(function( $ ) {
	'use strict';

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
					button.removeAttr('disabled');

				
				}else if(response == 1){
					
					var fields =  f.serialize();
					$.post( 'options.php', fields, function(obj){
						window.location.reload(false); 					
					});
				
				}

			})
		
		}); 

		$('.dplrwoo-mapping-fields').focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),'type':$('option:selected', this).attr('data-type'),'name':$(this).attr('name')});
		}).change(function(){
			
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			
			$(this).data('val', current);

			if(prevData.val!==''){
				$('.dplrwoo-mapping-fields').each(function(){
					if( checkFieldType(prevData.type,$(this).attr('data-type')) && (prevData.name !== $(this).attr('name')) ){
						$(this).append('<option value="'+prevData.val+'">'+prevData.val+'</option>');
					}
				});
			}

			if(current!==''){
				var s = $('.dplrwoo-mapping-fields').not(this);
				s.find('option[value="'+current+'"]').remove();
			}

		});

		if($("#dprwoo-tbl-lists").length>0){
			loadLists(1);
		}

		$("#dplrwoo-form-list select").change(function(){
			$(this).closest('tr').find('td span').html($('option:selected', this).attr('data-subscriptors'));
		});

		$("#btn-synch").click(function(){
			var link = $(this);
			link.css('display','none');
			$('.doing-synch').css('display', 'inline-block');
			var synchBuyers = $.post(ajaxurl, {action:'dplrwoo_ajax_synch_buyers'}, function(response){
				console.log(response);
			});
			var synchContacts = $.post(ajaxurl, {action: 'dplrwoo_ajax_synch_registered'}, function(response){
				console.log(response);
			});
			$.when(synchBuyers, synchContacts).then(function(response){
				link.css('display','inline-block');
				$('.doing-synch').css('display', 'none');
			});
		});

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
						html+='<td>'+body.createdResourceId+'</td><td><strong>'+listName+'</strong></td>';
						html+='<td>0</td>';
						html+='<td><a href="#" class="text-dark-red" data-list-id="'+body.createdResourceId+'">Delete</a></td>'
						html+='</tr>';

						$("#dprwoo-tbl-lists tbody").prepend(html);

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
					
					html += '<tr>';
					html += '<td>'+value.listId+'</td>';
					html += '<td><strong>'+value.name+'</strong></td>';
					html += '<td>'+value.subscribersCount+'</td>';
					html += '<td><a href="#" class="text-dark-red" data-list-id="'+value.listId+'">Delete</a></td>'
					html += '</tr>';
					
				}

				$("#dprwoo-tbl-lists tbody").prepend(html);
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

	function checkFieldType(dplrType, wcType){

		var types = {
			'string':['string','state'],
			'gender':['radio'],
			'email':['email'],
			'country':['country'],
			'phone':['tel'],
			'number':['number'],
			'date':['date','datetime','datetime-local'],
			'boolean':['checkbox'],
		}

		if( $.inArray(wcType,types[dplrType]) !== -1 || (dplrType === 'string' && wcType === '') ) {
			return true;
		}

		return false;
	}

})( jQuery );