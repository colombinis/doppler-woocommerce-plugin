(function( $ ) {
	'use strict';

	$(function() {
		$('.dplrwoo-mapping-fields').focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),
				'type':$('option:selected', this).attr('data-type'),
				'name':$(this).attr('name')
			});
		}).change(function(){
			
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			
			$(this).data('val', current);

			if(prevData.val!==''){
				$('.dplrwoo-mapping-fields').each(function(){
					if( checkFieldType(prevData.type,
						$(this).attr('data-type')) && (prevData.name !== $(this).attr('name')) ){
						$(this).append('<option value="'+prevData.val+'">'+prevData.val+'</option>');
					}
				});
			}

			if(current!==''){
				var s = $('.dplrwoo-mapping-fields').not(this);
				s.find('option[value="'+current+'"]').remove();
			}

		});

		$(".dplr-lists-sel").focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),
				'name':$(this).attr('name'),
				'selectedName':$(this).children("option:selected").text()
			});
		}).change(function(){
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			$('#dplrwoo-btn-synch,.synch-ok').css('display','none');
			if(prevData.val!==''){
				$('.dplr-lists-sel').each(function(){
					if( prevData.name !== $(this).attr('name') ){
						$(this).append('<option value="'+prevData.val+'">'+prevData.selectedName+'</option>');
					}
				});
			}
			if(current!==''){
				var s = $('.dplr-lists-sel').not(this);
				s.find('option[value="'+current+'"]').remove();
			}
			$(this).closest('tr').find('td span').html(
				$('option:selected', this).attr('data-subscriptors')
			);
		});

		$("#dplrwoo-btn-synch").click(function(){
			var link = $(this);
			var synchOk = $('.synch-ok');
			var bc = $('#buyers-count');
			var cc = $('#contacts-count');
			link.css('pointer-events','none');
			synchOk.css('opacity','0');
			$('.doing-synch').css('display', 'inline-block');
			$('#displayErrorMessage,#displaySuccessMessage').css('display','none');

			var synchBuyers = function(){
				var deferred = new $.Deferred();
				$.post( ajaxurl, {action:'dplrwoo_ajax_synch',list_type: 'buyers'}, function( response ){
					deferred.resolve(response);
				})
				return deferred.promise();
			}
			
			var synchContacts = function(){
				var deferred = new $.Deferred();
				$.post(ajaxurl, {action: 'dplrwoo_ajax_synch', list_type: 'contacts'}, function(response){
					deferred.resolve(response);
				});
				return deferred.promise();
			} 

			synchBuyers().then(function(responseBuyers){
				var obj = JSON.parse(responseBuyers);
				if(!obj.createdResourceId){
					displayErrors(obj.status,obj.errorCode);
					$('.doing-synch').css('display', 'none');
					return false;
				}
				synchContacts().then(function(responseContacts){
					var obj = JSON.parse(responseContacts);
					if(!obj.createdResourceId){
						displayErrors(obj.status,obj.errorCode);
						$('.doing-synch').css('display', 'none');
						return false;
					}
					$.post(ajaxurl,{action: 'dplrwoo_ajax_update_counter'}, function(response){
						var obj = JSON.parse(response);
						if(bc.html()!=''){
							bc.html(obj.buyers);
						}
						if(cc.html()!=''){
							cc.html(obj.contacts);
						}
						link.css('pointer-events','initial');
						$('.doing-synch').css('display', 'none');
						synchOk.css('opacity','.9');
					})
				})
			});

		});

		/**
		 * Create default lists
		 */
		$("#dplrwoo-create-lists").click(function(){
			var button = $(this);
			button.closest('#dplrwoo-createlist-div').find('.error').remove();
			button.addClass('button--loading').css('pointer-events','none');
			$('.notice').remove();
			$('#displayErrorMessage,#displaySuccessMessage').css('display','none');

			$.post(ajaxurl,{action: 'dplrwoo_ajax_create_lists'}, function(response){
				var obj = JSON.parse(response);
				if(typeof obj.buyers.createdResourceId==="undefined"||typeof obj.contacts.createdResourceId==="undefined"){
					var err = '';
					if(typeof obj.buyers.response.title!=="undefined"){
						err+=obj.buyers.response.title;
					}
					if(typeof obj.contacts.response.title!=="undefined" && err===''){
						err+=obj.buyers.response.title;
					}
				}
				if(err!=''){
					button.after('<div class="notice notice-error">'+err+'</div>');
				}else{
					window.location.reload(false); 					
				}

				button.removeClass('button--loading').css('pointer-events','initial');

			});
		});

		/*
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
						if(body.status >= 400){
							//body.status
							displayErrors(body.status,body.errorCode);
						}
					}
					listsLoaded();
				});
			
			}

		});
		*/
		
		if($('#dplr-dialog-confirm').length>0){
			
			$("#dplr-dialog-confirm").dialog({
				autoOpen: false,
				resizable: false,
				height: "auto",
				width: 400,
				modal: true
			});
		
		}

		//$("#dprwoo-tbl-lists tbody").on("click","tr a",deleteList);
		$("#dplrwoo-new-list").on("click",null,{},newList);

	});

	function displayErrors(status,code){
		var errorMsg = '';
		errorMsg = generateErrorMsg(status,code);
		$('#showErrorResponse').css('display','block').html('<p>'+errorMsg+'</p>');
	}

	function generateErrorMsg(status,code){
		var err = '';
		var errors = {	
			400 : { 1: ObjWCStr.validationError,
					2: ObjWCStr.duplicatedName,
					3: ObjWCStr.maxListsReached},
			429 : { 0: ObjWCStr.tooManyConn}
		}
		if(typeof errors[status] === 'undefined')
			 err = 'Unexpected error';
		else
		   typeof errors[status][code] === 'undefined'? err='Unexpected error code' : err = errors[status][code];
		 return err;
	}

	function clearResponseMessages(){
		$('#showSuccessResponse,#showErrorResponse').html('').css('display','none');
	}

	function newList(e){
		e.preventDefault();
		clearResponseMessages();
		$('#displayErrorMessage,#displaySuccessMessage').css('display','none');
		var inputField = $("#dplr-dialog-confirm").find('input[type="text"]');
		var span = $("#dplr-dialog-confirm").find('span.text-red');
		inputField.val('');
		span.remove();
		$("#dplr-dialog-confirm").dialog("option", "buttons", [{
			text: 'New List',
			click: function() {
				var dialog = $(this);
				var button = dialog.closest('.ui-dialog ').find('button');
				var loader = dialog.find('img');
				var listName = inputField.val();
				var data = {
					action: 'dplrwoo_ajax_save_list',
					listName : listName
				};
				if(listName === '') return false;
				button.attr('disabled','disabled');
				loader.css('display','inline-block');
				$.post( ajaxurl, data, function( response ) {
					var obj = JSON.parse(response);
					if(typeof obj.createdResourceId !== "undefined"){
						$(".dplr-lists-sel").append('<option value="'+obj.createdResourceId+'">'+listName+'</option>');
						$("#showSuccessResponse").html('<p>'+ObjWCStr.listSavedOk+'</p>').css('display','block');
						button.removeAttr('disabled');
						loader.css('display','none');
						dialog.dialog("close");
					}else{
						button.removeAttr('disabled');
						loader.css('display','none');
						if(obj.status>=400){
							inputField.after('<span class="text-red">'+generateErrorMsg(obj.status,obj.errorCode))+'</span>';
						}
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
			'string': ['string','state'],
			'gender': ['radio'],
			'email' : ['email'],
			'country':['country'],
			'phone' : ['tel'],
			'number': ['number'],
			'date'  : ['date','datetime','datetime-local'],
			'boolean':['checkbox'],
		}

		if( $.inArray(wcType,types[dplrType]) !== -1 || (dplrType === 'string' && wcType === '') ) {
			return true;
		}

		return false;
	}

})( jQuery );