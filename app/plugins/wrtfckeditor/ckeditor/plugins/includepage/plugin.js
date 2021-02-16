CKEDITOR.plugins.add( 'includepage', {
	requires: 'widget',
	init: function( editor ) {
		editor.widgets.add( 'inline-page', {
			upcast: function( element ) {
				if ( element.hasClass( 'inline-page' ) )
					return true;
			},
			init: function() {
			}
		});
	}
} );