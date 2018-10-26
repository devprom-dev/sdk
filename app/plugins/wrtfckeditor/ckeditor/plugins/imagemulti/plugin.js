CKEDITOR.plugins.add( 'imagemulti',
{
	init: function( editor )
	{
		var iconPath = this.path + 'images/icon.png';
        var container = $('<span></span>').appendTo($(editor.element.$.parentElement));
        var flow = new Flow({
            target: '/',
            testChunks: false
        });
        flow.assignBrowse(container.get());
        flow.on("filesAdded", function(files, event) {
            var htmlToBeInserted = [];
            $.each(files, function(index, file) {
                var reader = new FileReader();
                reader.onloadend = function () {
                    htmlToBeInserted.push('<p><img src="'+reader.result+'"></p>');
                    if ( htmlToBeInserted.length >= files.length ) {
                        editor.insertHtml(htmlToBeInserted.join(''));
                    }
                }
                reader.readAsDataURL(file.file);
            })
            return false;
        });

        editor.addCommand( 'insertMultipleImage', {
            exec: function( editor ) {
                container.find('input[type="file"]').click();
            }
        });
        editor.ui.addButton( 'InsertMultipleImages', {
			label: cket('imagemulti-button'),
			command: 'insertMultipleImage',
			icon: iconPath
		} );
	}
});
