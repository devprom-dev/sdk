CKEDITOR.plugins.add( 'searchartifacts',
{
	init: function( editor )
	{
		if ( editor.element.hasClass('wysiwyg-input') ) return false;

		var iconPath = this.path + 'images/icon.png';
		editor.addCommand( 'searchArtifact', new CKEDITOR.dialogCommand( 'searchArtifactDialog' ));
		editor.ui.addButton( 'searchArtifact', {
			label: cket('search-artifact-short'),
			command: 'searchArtifact',
			icon: iconPath
		} );
		if ( editor.contextMenu ) {
			editor.addMenuGroup( 'searchArtifactsGroup' );
			editor.addMenuItem( 'searchArtifactItem', {
				label : cket('search-artifact'),
				icon : iconPath,
				command : 'searchArtifact',
				group : 'searchArtifactsGroup'
			});
			editor.contextMenu.addListener( function( element ) {
				if ( element && $.inArray(element.getName(), ['img','a']) >= 0 ) {
					return null;
				}
				return { searchArtifactItem : CKEDITOR.TRISTATE_OFF };
			});
		}

		var url = '/pm/'+editor.element.getAttribute('project')+'/module/wrtfckeditor/searchartifacts?formonly=true';
		CKEDITOR.dialog.addIframe(
			'searchArtifactDialog',
			cket('search-artifact-dlg'),
			url,
			$(window).width() * 3/6,
			$(window).height() * 3/5,
			function() {
				var dialog = this.getDialog();
				var uiElement = dialog.getElement('iframe');
				var iframe = $(uiElement.$).find('iframe').contents();
				iframe.keydown(function(e) {
					if (e.ctrlKey && e.keyCode == 13) {
						dialog.getButton('ok').click();
					}
					if (e.keyCode == 27) {
						dialog.getButton('cancel').click();
					}
				});
				iframe.focus();
				var form = iframe.find('form');
				form.attr('action', url)
					.find('[name=pm_ChangeRequestaction]')
					.attr('value', 'add');
				focusField(form);
			},
			{
				onOk: function() {
					var focusedEditor = this.getParentEditor();
					var iframe = $(this.getElement('iframe').$).find('iframe');

					iframe.contents().find('form').ajaxSubmit({
						dataType: 'html',
						cache: false,
						beforeSubmit: function(data, form, options) {
							options.url += '&_=' + (new Date().getTime()).toString();
						},
						success: function(response) {
							try	{
								data = jQuery.parseJSON(response);
							}
							catch( e ) {
								if ( (new RegExp('Internal Server Error')).exec( response ) != null ) {
									window.location = '/500';
								}
								return;
							}
							var element = CKEDITOR.dom.element.createFromHtml(data.Url);
							focusedEditor.insertElement(element);
						},
						complete: function(xhr) {
						},
						error: function( xhr )
						{
						},
						statusCode:
						{
							500: function(xhr) {
								window.location = '/500';
							}
						}
					});
				}
			}
		);
	}
});