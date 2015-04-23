// Register the plugin with the editor.
// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.plugins.html
CKEDITOR.plugins.add( 'linkex',
{
	// The plugin initialization logic goes inside this method.
	// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.pluginDefinition.html#init
	init: function( editor )
	{
		// Add context menu support.
		if ( editor.contextMenu )
		{
			// Register a new context menu group.
			editor.addMenuGroup( 'linkExGroup', 0.1 );
			// Register a new context menu item.
			editor.addMenuItem( 'openTabItem',
			{
				// Item label.
				label : cket('open-link-new-tab'),
				// Context menu group that this entry belongs to.
				group : 'linkExGroup',
				onClick: function(item) {
					window.open(this.editor.getSelection().getStartElement().getAttribute('href'), (new Date()).getMilliseconds());
					/*
					var el = this.editor.getSelection().getStartElement().$;
					el.setAttribute('target', '_blank');
					el.dispatchEvent((function(e){
						  e.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
						  return e;
					}(document.createEvent('MouseEvents'))));
					*/
				}
			});
			editor.addMenuItem( 'openWindowItem',
			{
				// Item label.
				label : cket('open-link-new-window'),
				// Context menu group that this entry belongs to.
				group : 'linkExGroup',
				onClick: function(item) {
					window.open(this.editor.getSelection().getStartElement().getAttribute('href'), (new Date()).getMilliseconds());
				}
			});
			// Enable the context menu only for an <img> element.
			editor.contextMenu.addListener( function( element )
			{
				// Return a context menu object in an enabled, but not active state.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.html#.TRISTATE_OFF
				if ( element && element.getName() == 'a' ) {
		 			return { 
						openTabItem: CKEDITOR.TRISTATE_OFF,
						openWindowItem: CKEDITOR.TRISTATE_OFF,
						linkExGroupSeparator: CKEDITOR.TRISTATE_OFF
					};
				}
				// Return nothing if the conditions are not met.
		 		return null;
			});
		}
	}
});