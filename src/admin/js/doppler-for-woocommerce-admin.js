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

		$(".dplrwoo-lists-sel").focus(function(){
			$(this).data('fieldData', {'val':$(this).val(),
				'name':$(this).attr('name'),
				'selectedName':$(this).children("option:selected").text()
			});
		}).change(function(){
			var prevData = $(this).data('fieldData');
			var current = $(this).val();
			$('#dplrwoo-btn-synch,.synch-ok').css('display','none');
			if(prevData.val!==''){
				$('.dplrwoo-lists-sel').each(function(){
					if( prevData.name !== $(this).attr('name') ){
						$(this).append('<option value="'+prevData.val+'">'+prevData.selectedName+'</option>');
					}
				});
			}
			if(current!==''){
				var s = $('.dplrwoo-lists-sel').not(this);
				s.find('option[value="'+current+'"]').remove();
			}
			$(this).closest('tr').find('td span').html(
				$('option:selected', this).attr('data-subscriptors')
			);
		});

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

		$("#dplrwoo-btn-synch").click(function(){
			var link = $(this);
			var synchOk = $('.synch-ok');
			var bc = $('#buyers-count');
			var cc = $('#contacts-count');
			var syncBuyersOk = false;
			var syncContactsOk = false;
			link.css('pointer-events','none');
			synchOk.css('opacity','0');
			$('.doing-synch').css('display', 'inline-block');
			$('#displayErrorMessage,#displaySuccessMessage').css('display','none'); 

			synchBuyers().then(function(responseBuyers){
				var obj = JSON.parse(responseBuyers);
				(!obj.createdResourceId)? syncBuyersOk = false : syncBuyersOk = true;
				synchContacts().then(function(responseContacts){
					var obj = JSON.parse(responseContacts);
					(!obj.createdResourceId)? syncContactsOk = false : syncContactsOk = true;
					$.post(ajaxurl,{action: 'dplrwoo_ajax_update_counter'}, function(response){
						var obj = JSON.parse(response);
						if(bc.html()!=''){
							bc.html(obj.buyers);
						}
						if(cc.html()!=''){
							cc.html(obj.contacts);
						}
						console.log(syncBuyersOk);
						console.log(syncContactsOk);
						if(!syncBuyersOk && !syncContactsOk){
							$("#showErrorResponse").html('<p>'+ObjWCStr.listsSyncError+'</p>').css('display','flex');
						}else{
							$("#showSuccessResponse").html('<p>'+ObjWCStr.listsSyncOk+'</p>').css('display','flex');
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
			clearResponseMessages();
			$('#displayErrorMessage,#displaySuccessMessage').css('display','none');

			$.post(ajaxurl,{action: 'dplrwoo_ajax_create_lists'}, function(response){
				var obj = JSON.parse(response);
				console.log(obj.buyers);
				console.log(obj.contacts);
				if(typeof obj.buyers.response.createdResourceId==="undefined"||typeof obj.contacts.response.createdResourceId==="undefined"){
					var err = '';
					console.log('showing Erros');
					if(typeof obj.buyers.response.title!=="undefined"){
						//err+=obj.buyers.response.title;
						displayErrors(obj.buyers.response);
					}
					if(typeof obj.contacts.response.title!=="undefined" && err===''){
						//err+=obj.buyers.response.title;
						displayErrors(obj.contacts.response);
					}
					button.removeClass('button--loading').css('pointer-events','initial');
					return false;
				}	
				console.log('should reload');
				window.location.reload(false);				
			});
		});
		
		var dialog = $("#dplr-dialog-confirm").dialog({close:function(){
			$(this).find('input[type=text]').val('');
			$(this).find(".text-red").remove();
		}});

		dialog.dialog("option","buttons",[
			{ text: ObjWCStr.Save, click: saveList },
			{ text: ObjWCStr.Cancel, click: function(){
				dialog.dialog('close');
			}}
		]);
		
		$("#dplrwoo-new-list").click(function(){
			clearResponseMessages();
			$('#displayErrorMessage,#displaySuccessMessage').css('display','none');
			dialog.dialog("open");
		});
		
		$("#dplr-dialog-confirm input[type=text]").onEnter(function(e){
			e.preventDefault();
			if($(this).val()!=''){
				$("#dplr-dialog-confirm").find(".text-red").remove();
				saveList();
			}
			return false;
		});

		function saveList(){
			var dialog = $("#dplr-dialog-confirm");
			var inputField = dialog.find('input[type="text"]');
			var button = dialog.closest('.ui-dialog ').find('button');
			var loader = dialog.find('img');
			var listName = inputField.val();
			if(listName=='') return false;
			
			button.attr('disabled','disabled');
			loader.css('display','inline-block');
			var data = {
				action: 'dplrwoo_ajax_save_list',
				listName : listName
			};
			$.post( ajaxurl, data, function( response ) {
				var obj = JSON.parse(response);
				if(typeof obj.createdResourceId !== "undefined"){
					$(".dplrwoo-lists-sel").append('<option value="'+obj.createdResourceId+'" data-subscriptors="0">'+listName+'</option>');
					$("#showSuccessResponse").html('<p>'+ObjWCStr.listSavedOk+'</p>').css('display','flex');
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

	});

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