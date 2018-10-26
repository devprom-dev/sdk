CKEDITOR.plugins.add( 'embedhtml',
{
	init: function( editor )
	{
		var iconPath = this.path + 'images/icon.png';
		editor.addCommand( 'embedHtmlDialog',new CKEDITOR.dialogCommand( 'embedHtmlDialog' ) );
		editor.ui.addButton( 'EmbedHTML',
		{
			label: cket('embedhtml-dialog'),
			command: 'embedHtmlDialog',
			icon: iconPath
		} );
		
		CKEDITOR.dialog.add( 'embedHtmlDialog', function ( editor )
		{
			return {
				title : cket('embedhtml-dialog'),
				minWidth : 350,
				minHeight : $(window).height() * 1/6,
				contents :
				[
					{
						id : 'tab1',
						elements :
						[
							{
								type: 'vbox',
								children: [
									{
										type : 'html',
										html : cket('embedhtml-text'),
									},
									{
										type : 'textarea',
										rows : 10,
										cols : 120,
										id : 'embeddedhtml',
										timer: 0
									},
								]
							}
						]
					},
				],
				onOk : function()
				{
					editor.insertHtml(this.getValueOf('tab1','embeddedhtml'));
                }
			};
		});
	}
});
