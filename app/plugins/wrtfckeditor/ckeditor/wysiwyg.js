var cket = underi18n.MessageFactory(ckeditor_resources);
hljs.configure({
	tabReplace: '    '
});
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
 }
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
		originalEvent = event.data.domEvent.$;
		if ( this.editable().$.ownerDocument != window.document ) Mousetrap.handleKeyEvent(originalEvent);
    });
    editor.on( 'change', function( evt  ) {
        var edt = $(this.editable().$);
        if ( edt.attr('objectclass') == 'TestScenario' || edt.attr('objectclass') == 'TestCaseExecution' ) {
        	var skip = false;
            edt.find('table tr > td:nth-child(1)').each(function(index) {
            	if ( index == 0 ) {
                    skip = $(this).text() != "1";
				}
				if ( !skip ) {
                    $(this).html(index + 1);
				}
            });
		}
    });
}

function setupEditorGlobal( filesTitle )
{
	var width = $('.documentToolbar').width();
	$('.documentToolbar').css('min-height', width > 1410 ? '40px' : '76px');

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
			resize_enabled : false,
			language: devpromOpts.language == '' ? 'en' : devpromOpts.language,
			contentsCss: ['/pm/'+project+'/scripts/css/?v='+appVersion],
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
                e.editor.resetDirty();
		      	return true;
	      	});

	      	registerFormValidator($(element).parents('form').attr('id'), function() 
	      	{ 
	      		e.editor.custom.updateForm();
		      	e.editor.updateElement();
                e.editor.resetDirty();
		      	return true; 
	      	});
	      	
	      	registerFormDestructorHandler($(element).parents('form').attr('id'), function () {
                e.editor.resetDirty();
	      		e.editor.destroy();
	      	});
	      	
			e.editor.dataProcessor.writer.setRules('p', {
                breakAfterClose: false
            });

            var editableElement = $(e.editor.editable().$);
            editableElement.on( 'paste', pasteImage);
			makeupEditor(e.editor, editableElement, 'body', project, $('#cke_'+editor_id+' .cke_wysiwyg_frame').offset());

			if ( !$.browser.msie ) {
				e.editor.updateElement();
				originalFormState = $('#modal-form form[id]').formSerialize();
			}
		});
	}
	else
	{
		var editor = CKEDITOR.inline( element, {
			removePlugins: toolbar == '' ? 'codesnippet,image2,mathjax,autoembed,widget,embed,embedbase,pastefromword,pastetext,autolink,clipboard,notificationaggregator,notification,toolbar' : '',
			toolbar: toolbar,
			enterMode: toolbar == '' ? CKEDITOR.ENTER_BR : CKEDITOR.ENTER_P,
			allowedContent: toolbar != '',
			language: devpromOpts.language == '' ? 'en' : devpromOpts.language,
			sharedSpaces: {top: 'documentToolbar'}
		});
		if ( editor == null ) {
			reportBrowserError(element);
			return;
		}

		editor.purgeTimeoutValue = 6000;
		editor.persist = function( async )
		{
			var element = $('#' + this.name ); 
			var editorInstance = this;
				
			if ( typeof $(element).attr('objectClass') == 'undefined' ) {
				$('#'+$(element).attr('id')+'Value').val( editorInstance.getData() );
			}
			else if ( editorInstance.checkDirty() )
			{
				runMethod(modify_url, {
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
				}, '', async);
			}
		};
		
		editor.on('blur', function(e) {
			if ( typeof e.editor.purgeTimeout == 'number' ) {
				clearTimeout(e.editor.purgeTimeout);
			}
			e.editor.purgeTimeout = setTimeout(function() { e.editor.persist(true); }, e.editor.purgeTimeoutValue);
		});
		editor.on('focus', function(e) {
			if ( typeof e.editor.purgeTimeout == 'number' ) {
				clearTimeout(e.editor.purgeTimeout);
			}
		});

		editor.on('instanceReady', function(e) 
		{
	      	registerBeforeUnloadHandler($(element).parents('form').attr('id'), function()
	      	{
		      	e.editor.persist(false);
		      	return true;
	      	});
	    			
			if ( $(element).hasClass('wysiwyg-field') || $(element).parents('.embedded_form').length > 0 )
			{
		      	registerFormValidator($(element).parents('form').attr('id'), function() 
      			{ 
		      		e.editor.custom.updateForm();
					e.editor.persist(true);
		      		return true;
			    });
		      	
		      	registerFormDestructorHandler($(element).parents('form').attr('id'), function () {
		      		e.editor.destroy();
		      	});
			}

			$(element).attr('title', '');
            $(element).removeAttr('style');
			
			$(element).find('a[href]').click( function(e) 
			{
				e.stopImmediatePropagation();
				window.location = $(this).attr('href');
			});
			
			e.editor.dataProcessor.writer.setRules('p', {
                breakAfterClose: false
            });
			
			$(e.editor.editable().$).on( 'paste', pasteImage);
			makeupEditor(e.editor, $(e.editor.editable().$), 'body', project);
		});

		editor.on('destroy', function(e) 
		{
			e.editor.purgeTimeoutValue = 0;
			e.editor.persist(false);
		});

		editor.on('panelShow', function(e)
		{
			var panelElement = $(e.data.element.$);
			var stickedParent = panelElement.parents('.sticked');
			if (stickedParent.length < 1) return;
			panelElement.css({
				'left': panelElement.position().left + stickedParent.position().left
			});
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
	    var elem = $(e.target);
	    var fr = new FileReader;
	    
	    fr.onloadend = function() {
            var originalImage = new Image;
            var uriData = '';
            if ( fr.result.length > 1048576 ) {
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
                        elem.canContainText()
                            ? elem.append(scaledImage)
                            : $(scaledImage).insertAfter(elem);
                    };
                    scaledImage.src = canvas.toDataURL();
                };
			}
			else {
                originalImage.onload = function() {
                    $(originalImage)
                        .attr("height",originalImage.height)
                        .attr("width",originalImage.width);
                    elem.canContainText()
						? elem.append(originalImage)
						: $(originalImage).insertAfter(elem);
                };
			}
            originalImage.src = fr.result;
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

function makeupEditor( editor, e, container, project, offset )
{
	var templates = [];
	var templatesUrl = '/pm/'+editor.element.getAttribute('project')+'/module/wrtfckeditor/searchtexttemplate?export=list&objectclass='
		+ editor.element.getAttribute('objectclass');

    editor.dataProcessor.htmlFilter.addRules( {
        elements: {
            a: function( element ) {
                element.attributes.target = '_blank';
            }
        }
    });

    if ( e.attr('objectclass') == 'TestCaseExecution' ) {
        e.find('table tr > th').each(function(index) {
            if ( $(this).hasClass('readonly-on-run') ) {
                e.find('table tr > td:nth-child('+(index+1)+')').attr('contenteditable', 'false');
			}
        });
    }

    e.textcomplete([
		{ // mentions
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
				return '@' + selected.shift();
			},
			template: function (value) {
				var selected = $.map(mentions, function (mention) {
					return mention.Caption == value ? mention : null;
				});
				if ( selected[0].PhotoColumn < 0 && selected[0].PhotoRow < 0 ) {
					return '<i class="icon-briefcase"></i>' + value;
				}
				else {
					return '<div class="user-mini-mention" style="background: url(/images/userpics-mini.png) no-repeat -'+(parseInt(selected[0].PhotoColumn) * 18)+'px -'+(parseInt(selected[0].PhotoRow) * 18)+'px;"></div>' + value;
				}
			}
		},
		{ // templates
			mentions: templates,
			match: /\B#([^\s]*)\s?$/,
			search: function (term, callback) {
				$.getJSON(templatesUrl, function(data) {
					templates = data;
					callback($.map(data, function (template) {
						return template.Id.toLowerCase().indexOf(term.toLowerCase()) === 0 ? template.Id : null;
					}));
				});
			},
			index: 1,
			replace: function (templateId) {
				var selected = $.map(templates, function (template) {
					return template.Id == templateId ? template.Caption : null;
				});
				return selected.shift();
			}
		}],
		{
			zIndex: 9000,
			appendTo: container,
			maxCount: 60,
			topOffset: offset ? offset.top : 0,
			leftOffset: offset ? offset.left : 0,
			onKeydown: function (e, commands) {
				if (e.ctrlKey && e.keyCode === 74) { // CTRL-J
					return commands.KEY_ENTER;
				}
			}
		}
	);
}
(function ($) {
    var cannotContainText = ['AREA', 'BASE', 'BR', 'COL', 'EMBED', 'HR', 'IMG', 'INPUT', 'KEYGEN', 'LINK', 'MENUITEM', 'META', 'PARAM', 'SOURCE', 'TRACK', 'WBR', 'BASEFONT', 'BGSOUND', 'FRAME', 'ISINDEX'];
    $.fn.canContainText = function() {
        var tagName = $(this).prop('tagName').toUpperCase();
        return ($.inArray(tagName, cannotContainText) == -1);
    };
}(jQuery));

$(document).ready( function()
{
	setupEditorGlobal( 'Files' );
});
