(function($) {
    $.fn.onEnter = function(func) {
        this.bind('keypress', function(e) {
            if (e.keyCode == 13) func.apply(this, [e]);    
        });               
        return this; 
     };
})(jQuery);

(function( $ ) {
	'use strict';

	$(function() {

		var mappingFieldsSelects = $('.dplrwoo-mapping-fields');
		var listsSelect = $('.dplrwoo-lists-sel');
		var contactListSelect = $("#contacts-list");
		var buyersListSelect = $("#buyers-list");
		var syncListsButton = $("#dplrwoo-lists-btn");
		var clearListButton = $("#dplrwoo-clear");
		var listsForm = $("#dplrwoo-form-list");

		mappingFieldsSelects.focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),
				'type':$('option:selected', this).attr('data-type'),
				'name':$(this).attr('name')
			});
		}).change(function(){
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			$(this).data('val', current);
			if(prevData.val!==''){
				mappingFieldsSelects.each(function(){
					if( checkFieldType(prevData.type, $(this).attr('data-type')) && (prevData.name !== $(this).attr('name')) ){
						$(this).append('<option value="'+prevData.val+'">'+prevData.val+'</option>');
					}
				});
			}
			if(current!==''){
				var s = mappingFieldsSelects.not(this);
				s.find('option[value="'+current+'"]').remove();
			}
		});

		listsSelect.focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),
				'name':$(this).attr('name'),
				'selectedName':$(this).children("option:selected").text()
			});
		}).change(function(){
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			checkEnableListsButtons();
			if(prevData.val!==''){
				listsSelect.each(function(){
					if( prevData.name !== $(this).attr('name') ){
						$(this).find('option:first-child').after('<option value="'+prevData.val+'">'+prevData.selectedName+'</option>');
					}
				});
			}
			if(current!==''){
				var s = listsSelect.not(this);
				s.find('option[value="'+current+'"]').remove();
			}
			$(this).closest('tr').find('td span').html(
				$('option:selected', this).attr('data-subscriptors')
			);
		});

		var synchBuyers = function(buyersList){
			if(buyersList==='') return false;
			$.post( ajaxurl, {action:'dplrwoo_ajax_synch',list_type: 'buyers', list_id: buyersList});
		}
		
		var synchContacts = function(contactsList){
			if(contactsList==='') return false;
			$.post(ajaxurl, {action: 'dplrwoo_ajax_synch', list_type: 'contacts', list_id: contactsList});
		}

		syncListsButton.click(function(e){
			e.preventDefault();
			var buyersList = buyersListSelect.val();
			var contactsList = contactListSelect.val();
			$(this).attr('disabled','disabled').addClass("button--loading");
			$("#dplr-settings-text").html(ObjWCStr.Synchronizing);
			$.when(synchBuyers(buyersList),synchContacts(contactsList)).done(function(){
				listsForm.submit();
			});
		});

		$("#dplrwoo-form-list-new input[type=text]").keyup(function(){
			var button = $(this).closest('form').find('button');
			if($(this).val().length>0){
				button.removeAttr('disabled');
				return false;
			}
			button.attr('disabled',true);
		});

		$("#dplrwoo-save-list").click(function(e){
			e.preventDefault();
			clearResponseMessages();
			var button = $(this);
			var listInput = $(this).closest('form').find('input[type="text"]');
			var listName = listInput.val();
			if(listName=='') return false;
			button.attr('disabled',true).addClass("button--loading");
			var data = {
				action: 'dplrwoo_ajax_save_list',
				listName: listName
			}
			$.post( ajaxurl, data, function( response ){
				var body = 	JSON.parse(response);
				if(body.createdResourceId){		
					var html ='<option value="'+body.createdResourceId+'">'+listName+'</option>';
					listsForm.find('select option:first-child').after(html);
					listInput.val('');
					button.attr('disabled',true);
					displaySuccess(ObjWCStr.listSavedOk);
				}else if(body.status >= 400){
					displayErrors(body);
				}
				button.removeAttr('disabled').removeClass("button--loading");
			})
		});

		clearListButton.click(function(e){
			e.preventDefault();
			clearResponseMessages();
			var button = $(this);
			button.attr('disabled','disabled').addClass("button--loading");
			var data = {
				action: 'dplrwoo_ajax_clear_lists',
			}
			$.post( ajaxurl, data, function(response){
				if(response=='1') {
					listsForm.find("select").val('');
					syncListsButton.attr('disabled',true);
					$("#dplr-settings-text").html(ObjWCStr.selectAList);
					button.removeClass("button--loading");
				}
			})
		});

		/**
		 * Create default lists
		 */
		$("#dplrwoo-create-lists").click(function(){
			var button = $(this);
			button.closest('#dplrwoo-createlist-div').find('.error').remove();
			button.addClass('button--loading').css('pointer-events','none');
			clearResponseMessages();
			$.post(ajaxurl,{action: 'dplrwoo_ajax_create_lists'}, function(response){
				var obj = JSON.parse(response);
				if(typeof obj.buyers.response.createdResourceId==="undefined"||typeof obj.contacts.response.createdResourceId==="undefined"){
					var err = '';
					if(typeof obj.buyers.response.title!=="undefined"){
						displayErrors(obj.buyers.response);
					}
					if(typeof obj.contacts.response.title!=="undefined" && err===''){
						displayErrors(obj.contacts.response);
					}
					button.removeClass('button--loading').css('pointer-events','initial');
					return false;
				}	
				window.location.reload(false);				
			});
		});

		function checkEnableListsButtons(){
			if(contactListSelect.val()==='' && buyersListSelect.val()===''){
				listsForm.find("button").attr('disabled',true);
				return;
			}
			listsForm.find("button").removeAttr('disabled');
		}
		
	});

	function checkFieldType(dplrType, wcType){
		var types = {
			'string': ['string','state','country'],
			'gender': ['radio'],
			'email' : ['email'],
			'country':['country','string'],
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