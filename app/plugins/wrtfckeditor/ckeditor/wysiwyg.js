var cket = underi18n.MessageFactory(ckeditor_resources);
hljs.initHighlightingOnLoad();
var mentions = [];

CKEDITOR.disableAutoInline = true;
CKEDITOR.disableNativeSpellChecker = true;

function addImagesAutomatically() 
 {
	$('div.embeddedRow a.modify_image').each(function() 
	{
		$(this).click(function() 
		{
	    	var dialog = CKEDITOR.dialog.getCurrent();
	    	
	    	if ( dialog == null ) return;
	    	if ( typeof dialog == 'undefined' ) return;
	    	
			$('div.embeddedRow a.modify_image').css('font-weight', 'normal');
			
			$(this).css('font-weight', 'bold');
			
			var title = typeof $(this).attr('name') != 'undefined' ? $(this).attr('name').replace(/\[/,"(").replace(/\]/,")") : "";
			
			dialog.setValueOf( 'info', 'src', $(this).attr('href') ); 
			dialog.setValueOf( 'info', 'alt', title );
			
			return false;
		});
	});
};

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

function html_entity_decode(str)
{
	var tarea=document.createElement('textarea');
  	tarea.innerHTML = str;
	var value = tarea.value;
	$(tarea).remove();
	return value;
}
 
function setupDialogImage( filesTitle, dialogDefinition )
{
	var contents = dialogDefinition.getContents('uploadImage');
	
	if ( contents == null )
	{
	 	dialogDefinition.addContents({
	        id : 'uploadImage',
	        label : filesTitle,
	        accessKey : 'U',
	        elements : [
	           {
	              id : 'myFile',
	              type : 'html',
	              html : ''
	           }
	        ]
		}, 'info');
	}
}
 
function setupEditor( editor )
{
	editor.on( 'dialogShow', function(e) {
		var dialog = CKEDITOR.dialog.getCurrent();

		if ( dialog.getName() == 'image' || dialog.getName() == 'image2' )
		{
			var element = dialog.getContentElement('uploadImage', 'myFile');

			if ( element != null ) 
			{
				element.getElement().setHtml(e.editor.custom.attachmentsHtml);
			}
			
			addImagesAutomatically();
		}
	});
	editor.on( 'dialogHide', function(e) {
		var dialog = CKEDITOR.dialog.getCurrent();
		if ( dialog.getName() == 'image' || dialog.getName() == 'image2' ) {
			var element = dialog.getContentElement('uploadImage', 'myFile');
			if ( element != null ) {
				e.editor.custom.attachmentsHtml = element.getElement().getHtml();
			}
		}
	});
	editor.on( 'focus', function( e ) {
		$('.wysiwyg-welcome[for-id='+e.editor.custom.id+']').hide();
		$('.wysiwyg-hover').removeClass('wysiwyg-hover');
	});
    editor.on( 'key', function( event ) {
        if ( event.data.keyCode == 13 ) {
            var textComplete = $(this.editable().$).data('textComplete');
            if( textComplete && textComplete.dropdown && textComplete.dropdown.shown ) {
                textComplete.dropdown._enter(event.data);
                event.cancel();
            }
        }
    });
}

function setupEditorGlobal( filesTitle )
{
	CKEDITOR.on('dialogDefinition', function( ev ) 
	{
		  var dialogName = ev.data.name;
		  var dialogDefinition = ev.data.definition;
		  
		  if ( dialogName === 'table' ) 
		  {
			  setupDialogTable( dialogDefinition );
		  }

	      if ( dialogName == 'image' || dialogName == 'image2' )
	      {
			  setupDialogImage( filesTitle, dialogDefinition );
	      }
	});
	
	$('.wysiwyg-welcome').click(function() {
		$('#' + $(this).attr('for-id')).focus();
	});
}

function setupWysiwygEditor( editor_id, toolbar, rows, modify_url, attachmentsHtml, appVersion, project )
{
	if ( typeof CKEDITOR == 'undefined' ) return;
	CKEDITOR.timestamp = appVersion;
		
	var element = document.getElementById(editor_id);

	if ( $(element).hasClass('cke_editable') ) return true;
		
	if ( $(element).attr('contenteditable') != 'true' )
	{
		var editor = CKEDITOR.replace( element, {
			toolbar: toolbar,
			height: rows,
			enterMode: CKEDITOR.ENTER_P,
			removePlugins : 'elementspath',
			resize_enabled : toolbar == 'FullToolbar',
			language: devpromOpts.language == '' ? 'en' : devpromOpts.language,
			contentsCss: ['/pm/'+project+'/scripts/css?v='+appVersion],
			startupFocus: $(element).is(':focus')
		});
		if ( editor == null ) {
			reportBrowserError(element);
			return;
		}

		editor.on('instanceReady', function(e) 
		{
			e.editor.config.autoGrow_minHeight = e.editor.config.height;

			e.editor.updateElement();
			
	      	registerBeforeUnloadHandler($(element).parents('form').attr('id'), function() 
	      	{
		      	e.editor.updateElement(); 
		      	return true;
	      	});

	      	registerFormValidator($(element).parents('form').attr('id'), function() 
	      	{ 
	      		e.editor.custom.updateForm();
		      	e.editor.updateElement();
		      	return true; 
	      	});
	      	
	      	registerFormDestructorHandler($(element).parents('form').attr('id'), function () {
	      		e.editor.destroy();
	      	});
	      	
			e.editor.dataProcessor.writer.setRules('p', {
                breakAfterClose: false
            });

            var editableElement = $(e.editor.editable().$);
            editableElement.on( 'paste', pasteImage);
			makeupEditor(editableElement, 'body', project, $('#cke_'+editor_id+' .cke_wysiwyg_frame').offset());
		});
	}
	else
	{
		var editor = CKEDITOR.inline( element, {
			removePlugins: toolbar == '' ? 'toolbar' : '',
			toolbar: toolbar,
			enterMode: toolbar == '' ? CKEDITOR.ENTER_BR : CKEDITOR.ENTER_P,
			allowedContent: toolbar != '',
			language: devpromOpts.language == '' ? 'en' : devpromOpts.language
		});
		if ( editor == null ) {
			reportBrowserError(element);
			return;
		}

		editor.purgeTimeoutValue = 180000;
		editor.persist = function()
		{
			var element = $('#' + this.name ); 
			var editorInstance = this;
				
			if ( typeof $(element).attr('objectClass') == 'undefined' )
			{ 
				$('#'+$(element).attr('id')+'Value').val( editorInstance.getData() );
			}
			else if ( editorInstance.checkDirty() )
			{
				var purgeFunc = function() {
					runMethod(modify_url,
						{
							'class': $(element).attr('objectClass'),
							'object': $(element).attr('objectId'),
							'attribute': $(element).attr('attributeName'),
							'value': editorInstance.getData(),
							'parms': {
								ContentEditor: 'WikiRtfCKEditor'
							}
						},
						function(result) {
							editorInstance.resetDirty();
							var resultJson = jQuery.parseJSON(result);
							if ( typeof resultJson.modified != 'undefined' ) {
								$(element).parents('[modified]').attr('modified', resultJson.modified);
							}
						}, '', false);
				};
				if ( typeof editorInstance.purgeTimeout == 'number' ) {
					clearTimeout(editorInstance.purgeTimeout);
				}
				if ( editorInstance.purgeTimeoutValue > 0 ) {
					editorInstance.purgeTimeout = setTimeout(purgeFunc, editorInstance.purgeTimeoutValue);
				} else {
					purgeFunc();
				}
			}
		};
		
		editor.on('blur', function(e) 
		{
			e.editor.persist();
		});

		editor.on('instanceReady', function(e) 
		{
	      	registerBeforeUnloadHandler($(element).parents('form').attr('id'), function() 
	      	{
	      		e.editor.purgeTimeoutValue = 0;
		      	e.editor.persist();
		      	return true;
	      	});
	    			
			if ( $(element).hasClass('wysiwyg-field') )
			{
		      	registerFormValidator($(element).parents('form').attr('id'), function() 
      			{ 
		      		e.editor.custom.updateForm();
		      		e.editor.persist();
		      		return true; 
			    });
		      	
		      	registerFormDestructorHandler($(element).parents('form').attr('id'), function () {
		      		e.editor.destroy();
		      	});
			}

			$(element).attr('title', '');
			
			$(element).find('a[href]').click( function(e) 
			{
				e.stopImmediatePropagation();
				window.location = $(this).attr('href');
			});
			
			e.editor.dataProcessor.writer.setRules('p', {
                breakAfterClose: false
            });
			
			$(e.editor.editable().$).on( 'paste', pasteImage);
			makeupEditor($(e.editor.editable().$), 'body', project);
		});

		editor.on('destroy', function(e) 
		{
			e.editor.purgeTimeoutValue = 0;
			e.editor.persist();
		});
	}
	
	editor.custom = { 
		id: $(element).attr('id'),
		attachmentsHtml: html_entity_decode(attachmentsHtml),
		updateForm: function() 
		{
			if ( $('#'+this.id+'Files').length < 1 )
			{
				$('<div id="'+this.id+'Files" style="display:none"></div>')
					.appendTo($('#'+this.id).parent());
			}

			$('#'+this.id+'Files').html(this.attachmentsHtml);				
		}
	};
	
	setupEditor( editor );
}

function reportBrowserError(element)
{
	$(element).replaceWith('<div class="alert alert-danger" role="alert">'+cket('wrong-browser')+'<br/>'+$(element).html()+'</div>');
}

function pasteImage(e) {
	try {
	    var data = e.originalEvent.clipboardData.items[0].getAsFile();
	    var elem = this;
	    var fr = new FileReader;
	    
	    fr.onloadend = function() {
	        var img = new Image;
	        img.onload = function() {
	        	$(img)
	        		.attr("height",img.height)
	        		.attr("width",img.width);
	            $(elem).append(img);
	        };
	        img.src = fr.result;
	    };
	    fr.readAsDataURL(data);
	}
	catch(ex) {}
}

function pasteTemplate( field, content )
{
	var editor_id = $('textarea[id*='+field+']').attr('id');
	var instance = CKEDITOR.instances[editor_id];
	if ( !instance.checkDirty() ) {
		instance.setData(content, function() {
			instance.updateElement();
			instance.resetDirty();
		});
	} else {
		instance.insertHtml(content);
	}
}

function makeupEditor( e, container, project, offset )
{
	e.textcomplete([{ // html
		mentions: mentions,
		match: /\B@([^\s]*)\s?$/,
		search: function (term, callback) {
            if ( mentions.length < 1 ) {
                $.getJSON('/pm/'+project+'/mentions', function(data) {
                    mentions = data;
                    callback($.map(mentions, function (mention) {
                        return mention.Id.toLowerCase().indexOf(term.toLowerCase()) === 0 ? mention.Caption : null;
                    }));
                });
            }
            else {
                callback($.map(mentions, function (mention) {
                    return mention.Id.toLowerCase().indexOf(term.toLowerCase()) === 0 ? mention.Caption : null;
                }));
            }
		},
		index: 1,
		replace: function (selectedMention) {
            var selected = $.map(mentions, function (mention) {
                return mention.Caption == selectedMention ? mention.Id : null;
            });
			return '@' + selected.shift() + '  ';
		},
        template: function (value) {
            var selected = $.map(mentions, function (mention) {
                return mention.Caption == value ? mention : null;
            });
            if ( selected[0].PhotoColumn < 0 && selected[0].PhotoRow < 0 ) {
                return '<i class="icon-briefcase"></i>' + value;
            }
            else {
                return '<div class="user-mini-mention" style="background: url(\'/images/userpics-mini.png\') no-repeat -'+(parseInt(selected[0].PhotoColumn) * 18)+'px -'+(parseInt(selected[0].PhotoRow) * 18)+'px;"></div>' + value;
            }
        },
	}], {
        appendTo: container,
        maxCount: 60,
        topOffset: offset ? offset.top : 0,
        leftOffset: offset ? offset.left : 0,
		onKeydown: function (e, commands) {
			if (e.ctrlKey && e.keyCode === 74) { // CTRL-J
				return commands.KEY_ENTER;
			}
		}
	});
}

$(document).ready( function()
{
	setupEditorGlobal( 'Files' );
});
