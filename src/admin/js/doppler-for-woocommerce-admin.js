(function( $ ) {
	'use strict';

	$(function() {

		easyValidator.init({
			invalid_email_message:ObjWCStr.invalidUser,
			empty_field_message:ObjWCStr.emptyField,
			event: 'keyup',
		});

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

			if(!easyValidator.isValidForm()){
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

		if($("#dprwoo-tbl-lists").length>0){
			loadLists(1);
		}

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

		$("#dplrwoo-create-lists").click(function(){
			var button = $(this);
			var loader = button.closest('#dplrwoo-createlist-div').find('img');
			button.closest('#dplrwoo-createlist-div').find('.error').remove();
			button.css('display','none');
			loader.css('display','inline-block');
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
					button.after('<div class="error">'+err+'</div>');
				}else{
					window.location.reload(false); 					
				}
				button.css('display','inline-block');
				loader.css('display','none');

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
						if(body.status >= 400){
							//body.status
							displayErrors(body.status,body.errorCode);
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
		$("#dplrwoo-new-list").on("click",null,{},newList);

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

	function displayErrors(status,code){
		var errorMsg = '';
		errorMsg = generateErrorMsg(status,code);
		$('#showErrorResponse').html(errorMsg);
	}

	function generateErrorMsg(status,code){
		var err = '';
		var errors = {	
			400 : { 1:'Ha ocurrido un error al validar los datos',
					2:'Its duplicated!',
					3:'Ha alcanzado el máximo de listas'},
			429 : { 0:'Too many requests!! Wait a minute...'}
		}
		if(typeof errors[status] === 'undefined')
			 err = 'Unexpected error';
		else
		   typeof errors[status][code] === 'undefined'? err='Unexpected error code' : err = errors[status][code];
		 return err;
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
						$("#showSuccessResponse").html(ObjWCStr.listSavedOk).css('display','block');
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
							//alert('No se puede eliminar lista.')
						}else{
							//alert('Error');
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

	var easyValidator = {
		strInvalidEmail: 'Email is invalid',
		strEmptyField: 'Field is empty',
		event: 'blur',
		emailRegex: /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
		init: function(data){
			easyValidator.config = {
				form: $('form[easy-validate]'),
			}
			easyValidator.config.form.attr('novalidate','novalidate');
			if(typeof data !== "undefined"){
				if(typeof data.invalid_email_message !== "undefined"){
					this.strInvalidEmail = data.invalid_email_message;
				}
				if(typeof data.empty_field_message !== "undefined"){
					this.strEmptyField = data.empty_field_message;
				}
				if(typeof data.event !== "undefined"){
					if( $.inArray(data.event,['keyup','blur']) === -1 ){
						console.log('Invalid event attribute, use keyup or blur');
						return false;
					}
					easyValidator.event = data.event;
				}
			}
			var emailFields = easyValidator.config.form.find('input[type="email"]');
			var emptyFields = easyValidator.config.form.find('input[required]');
			var fields = easyValidator.config.form.find('input[required],input[type="email"]');
			fields.on('focus',this.clearError);
			emptyFields.on(easyValidator.event,this.validateEmpty);
			emailFields.on(easyValidator.event,this.validateEmail);
		},
		isValidForm: function(){
			easyValidator.config.form.find('.ev-error').remove();
			var fields = easyValidator.config.form.find('input');
			$.each(fields,function(){
				easyValidator.validateField($(this));
			})
			if(easyValidator.config.form.find('.ev-error').length>0){
				return false;
			}
			return true;
		},
		validateField: function(field){
			if(field.attr("type") === 'email'){
				easyValidator.validateEmailField(field);
			}
			if(field.attr("required") !== null){
				easyValidator.validateEmptyField(field);
			} 
		},
		validateEmptyField: function(e){
			if(e.val()==""){	
				e.after('<span class="ev-error">'+easyValidator.strEmptyField+'</span>');
				return false;
			}
		},
		validateEmailField: function(e){
			if( !easyValidator.emailRegex.test(e.val()) && e.val()!==''){
				e.after('<span class="ev-error">'+easyValidator.strInvalidEmail+'</span>');
				return false;
			}
		},
		validateEmail: function(){
			var element = $(this);
			element.next('.ev-error').remove();
			easyValidator.validateEmailField(element);
		},
		validateEmpty: function(){
			var element = $(this);
			element.next('.ev-error').remove();
			easyValidator.validateEmptyField($(element));
		},
		clearError: function(){
			$(this).next('.ev-error').remove();
		}
	}

})( jQuery );