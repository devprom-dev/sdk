var actxt = underi18n.MessageFactory(account_text);

function showAccountForm(url) 
{
	$('#modal-form').remove();

	$('body').append(
			'<div id="modal-form" style="display:none;" title="'+actxt('form-title')+'">'+
			'<iframe id="modal-frame" src="'+url+'" initial="'+url+'"></iframe>'+
			'</div>'
		);

	$('#modal-form').dialog({
		width: 750,
		height: $(window).height()*2/3,
		modal: true,
		resizable: true,
		open: function() {
			$("#modal-frame").css("minHeight", "97%");
		},
		buttons: [
			{
				tabindex: 10,
				text: actxt('form-ok-btn'),
				id: 'SubmitBtn',
			 	click: function() 
			 	{
					$('#modal-form').parent().find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
					$('#modal-frame').contents().find('form').ajaxSubmit({
						dataType: 'html',
						success: function(response) 
						{
							try	{
								data = jQuery.parseJSON(response);
							}
							catch( e ) {
					 			if ( (new RegExp('Internal Server Error')).exec( response ) != null ) {
					 				window.location = '/500';
				 				}
					 			$('#modal-frame').contents().find('#result')
									.removeClass('alert-success alert-error').addClass('alert alert-error').html(response);
					 			return;
							}
							
							$('#modal-frame').contents().find('#result').removeClass('alert alert-success alert-error').html('');
							
							var state = data.state;
							var message = data.message;
							
							if ( state == 'redirect' ) {
								$('#modal-frame').load(function() {buildProcessingForm();}).attr('src', data.object);
							}

							if ( message != '' ) {
								$('#modal-frame').contents().find('#result')
									.removeClass('alert-success alert-error')
									.addClass('alert alert-'+state)
									.html(message);
								$('#modal-form').parent().find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
							}
						},
						complete: function(xhr) {
						},
						error: function( xhr )
						{
							$('#modal-form').parent()
								.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
						},
						statusCode:
						{
						  500: function(xhr) {
						    	  window.location = '/500';
						       }
						}
					});
				}
			},
			{
				tabindex: 11,
				text: actxt('form-cancel-btn'),
				id: 'CancelBtn',
				click: function() {
					$(this).dialog('close');
				}
			}
		]
	});
}

function buildProcessingForm()
{
	$('#modal-form').dialog('option', 'height', $(window).height() * 5/6);
	$('#modal-form').dialog('option', 'position', { my: "center", at: "center", of: window });
	$('#modal-form').dialog('option', 'buttons', [
  			{
				tabindex: 10,
				text: actxt('form-return-btn'),
				id: 'ReturnBtn',
				click: function() {
					showAccountForm($("#modal-frame").attr('initial'));
				}
			},
			{
				tabindex: 11,
				text: actxt('form-close-btn'),
				id: 'CloseBtn',
				click: function() {
					$(this).dialog('close');
				}
			}
	]);

	$('#modal-form').parent().find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
}