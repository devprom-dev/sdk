var cket = underi18n.MessageFactory(ckeditor_resources);
hljs.configure({
	tabReplace: '    '
});
hljs.initHighlightingOnLoad();
var mentions = [];

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
    var tableClass = advTab.get('advCSSClasses');
    tableClass['default'] = "docs-table";
}

function html_entity_decode(str)
{
	var tarea=document.createElement('textarea');
  	tarea.innerHTML = str;
	var value = tarea.value;
	$(tarea).remove();
	return value;
}
 
function setupEditor( editor )
{
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
    editor.on( 'contentDom', function() {
        var editable = editor.editable();
        editable.attachListener( editable, 'mousedown', function( evt ) {
            var target = evt.data.getTarget(),
                clickedAnchor = ( new CKEDITOR.dom.elementPath( target, editor.editable() ) ).contains( 'a' ),
                href = clickedAnchor && clickedAnchor.getAttribute( 'href' ),
                modifierPressed = evt.data.$.ctrlKey || evt.data.$.shiftKey;

            if ( href && modifierPressed ) {
                window.open( href, target );
                evt.data.preventDefault();
            }
        });
    } );
    $('.wysiwyg-welcome:not(.armed)').click(function() {
        $('#' + $(this).attr('for-id')).focus();
        $(this).addClass('armed');
    });
}

function setupDialogLink( def )
{
    var infoTab = def.getContents( 'info' );
    var linkTypeItems = infoTab.get( 'linkType' ).items;
    if ( linkTypeItems.length > 0 ) {
        var items_no_anchor = linkTypeItems.slice(0, 1).concat( linkTypeItems.slice(2, linkTypeItems.length) );
        infoTab.get( 'linkType' ).items = items_no_anchor;
    }
}

function setupEditorGlobal( filesTitle )
{
	CKEDITOR.on('dialogDefinition', function( ev )
	{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;

		if ( dialogName === 'table' ) {
		  setupDialogTable( dialogDefinition );
		}
        if ( dialogName === 'link' ) {
            setupDialogLink( dialogDefinition );
        }
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
            buildPastable(editableElement);
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
			removePlugins: toolbar == '' ? 'codesnippet,image,image2,mathjax,autoembed,widget,embed,embedbase,pastefromword,pastetext,autolink,clipboard,notificationaggregator,notification,toolbar' : '',
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

		editor.purgeTimeoutValue = 1000;
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

			var editor = $(e.editor.editable().$);
			buildPastable(editor);
            makeupEditor(e.editor, editor, 'body', project);
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

    if ( !Array.isArray(mentions[project]) ) {
        mentions[project] = [];
	}

    e.textcomplete([
		{ // mentions
			mentions: mentions[project],
			match: /\B@([^\s]*)\s?$/,
			search: function (term, callback) {
				if ( mentions[project].length < 1 ) {
					$.getJSON('/pm/'+project+'/mentions', function(data) {
                        mentions[project] = data;
						callback($.map(mentions[project], function (mention) {
							return mention.Id.toLowerCase().indexOf(term.toLowerCase()) === 0 ? mention.Caption : null;
						}));
					});
				}
				else {
					callback($.map(mentions[project], function (mention) {
						return mention.Id.toLowerCase().indexOf(term.toLowerCase()) === 0 ? mention.Caption : null;
					}));
				}
			},
			index: 1,
			replace: function (selectedMention) {
				var selected = $.map(mentions[project], function (mention) {
					return mention.Caption == selectedMention ? mention.Id : null;
				});
				return '@' + selected.shift();
			},
			template: function (value) {
				var selected = $.map(mentions[project], function (mention) {
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

function buildPastable(editableElement) {
    if ($.browser.mozilla && $.browser.version >= '57' || detectIE()) return;
    editableElement.pastableContenteditable();
    editableElement.on( 'pasteImage', pasteImage);
}

$(document).ready( function()
{
	setupEditorGlobal( 'Files' );
});
