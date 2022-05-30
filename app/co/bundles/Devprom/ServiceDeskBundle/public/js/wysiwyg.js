var cket = underi18n.MessageFactory(ckeditor_resources);

CKEDITOR.disableAutoInline = true;
CKEDITOR.disableNativeSpellChecker = true;

function setupDialogTable( dialogDefinition )
{
    var infoTab = dialogDefinition.getContents('info');
    var cellSpacing = infoTab.get('txtCellSpace');
    cellSpacing['default'] = "1";
    var cellPadding = infoTab.get('txtCellPad');
    cellPadding['default'] = "1";
    var tableWidth = infoTab.get('txtWidth');
    tableWidth['default'] = "100%";
    
    var advTab = dialogDefinition.getContents('advanced');
    var tableStyle = advTab.get('advStyles');
    tableStyle['default'] = "border-collapse:collapse;";
}

function setupEditorGlobal()
{
	CKEDITOR.on('dialogDefinition', function( ev ) 
	{
		  var dialogName = ev.data.name;
		  var dialogDefinition = ev.data.definition;
		  
		  if ( dialogName === 'table' ) {
			  setupDialogTable( dialogDefinition );
		  }
	});
}

function setupWysiwygEditor( editor_id, height, alertMessage )
{
	var editor = CKEDITOR.replace( document.getElementById(editor_id), {
		customConfig: 'support_config.js',
		contentsCss: '/plugins/wrtfckeditor/ckeditor/support_contents.css',
		toolbar: 'MiniToolbar',
		height: height,
		enterMode: CKEDITOR.ENTER_P,
		removePlugins : 'elementspath,mathjax',
		resize_enabled : true,
		language: 'ru'
	});

	editor.on('instanceReady', function(e) {
		e.editor.updateElement();

		e.editor.dataProcessor.writer.setRules('p', {
			breakAfterClose: false
		});

		var editableElement = $(e.editor.editable().$);
		buildPastable(editableElement);
	});

	editor.on( 'required', function( evt ) {
		var element = $('#cke_issue_form_description');
		element.popover({
			content: alertMessage,
			title: function() {
				return '';
			},
			template: '<div class="popover popover-required"><div class="arrow"></div>' +
						'<div class="popover-inner"><div class="popover-content"><p>' +
							'</p></div></div></div>',
			placement: 'top',
			trigger: 'custom'
		});
		element.popover('show');
		$(evt.editor.editable().$).keyup(function() {
			element.popover('hide');
		});
		evt.cancel();
	} );
}

function pasteImage(ev, data)
{
	var elem = $(ev.target);
	var originalImage = new Image;
	if ( data.blob.size > 1048576 ) {
		originalImage.onload = function() {
			var maxWidth = 1024;
			var canvas = document.createElement('canvas')
			var ctx = canvas.getContext('2d');
			var scaledWidth = Math.min(maxWidth, originalImage.width);
			var scaledHeight = (scaledWidth / originalImage.width ) * originalImage.height;
			canvas.width = scaledWidth;
			canvas.height = scaledHeight;
			ctx.drawImage(originalImage, 0, 0, scaledWidth, scaledHeight);

			var scaledImage = new Image;
			scaledImage.onload = function() {
				$(scaledImage)
					.attr("height",scaledImage.height)
					.attr("width",scaledImage.width);
				if ( CKEDITOR.currentInstance ) {
					CKEDITOR.currentInstance.insertHtml(scaledImage.outerHTML)
				}
				else {
					elem.canContainText()
						? elem.append(scaledImage)
						: $(scaledImage).insertAfter(elem);
				}
			};
			scaledImage.src = canvas.toDataURL();
		};
	}
	else {
		originalImage.onload = function() {
			$(originalImage)
				.attr("height",originalImage.height)
				.attr("width",originalImage.width);
			if ( CKEDITOR.currentInstance ) {
				CKEDITOR.currentInstance.insertHtml(originalImage.outerHTML);
			}
			else {
				elem.canContainText()
					? elem.append(originalImage)
					: $(originalImage).insertAfter(elem);
			}
		};
	}
	originalImage.src = data.dataURL;
}

function buildPastable(editableElement) {
	editableElement.pastableContenteditable();
	editableElement.on( 'pasteImage', pasteImage);
}
