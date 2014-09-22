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
	
  	tarea.innerHTML = str; return tarea.value;
  	tarea.parentNode.removeChild(tarea);
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
	editor.on( 'dialogShow', function(e)
	{
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

	editor.on( 'dialogHide', function(e)
	{
		var dialog = CKEDITOR.dialog.getCurrent();

		if ( dialog.getName() == 'image' || dialog.getName() == 'image2' )
		{
			var element = dialog.getContentElement('uploadImage', 'myFile');

			if ( element != null ) 
			{
				e.editor.custom.attachmentsHtml = element.getElement().getHtml();
			}
		}
	});

	editor.on( 'focus', function( e ) 
	{
		$('.wysiwyg-welcome[for-id='+e.editor.custom.id+']').hide();
		
		$('.wysiwyg-hover').removeClass('wysiwyg-hover');
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

function setupWysiwygEditor( editor_id, toolbar, rows, modify_url, attachmentsHtml, appVersion ) 
{
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
			contentsCss: ['/pm/'+devpromOpts.project+'/scripts/css?v='+appVersion],
			startupFocus: $(element).is(':focus')
		});

		editor.on('instanceReady', function(e) 
		{
			e.editor.config.autoGrow_minHeight = e.editor.config.height;

			e.editor.updateElement();
			
	      	registerBeforeUnloadHandler(function() 
	      	{
		      	e.editor.updateElement(); 
		      	return true;
	      	});

	      	registerFormValidator(function() 
	      	{ 
	      		e.editor.custom.updateForm();
		      	e.editor.updateElement();
		      	return true; 
	      	});
	      	
	      	registerFormDestructorHandler(function () {
	      		e.editor.destroy();
	      	});
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
				runMethod(modify_url, {
					'class': $(element).attr('objectClass'),
					'object': $(element).attr('objectId'),
					'attribute': $(element).attr('attributeName'),
					'value': this.getData(),
					'parms': { 
						ContentEditor: 'WikiRtfCKEditor'
					}
				}, 
				function(result) 
				{
					editorInstance.resetDirty();
				
					var resultJson = jQuery.parseJSON(result);
					
					if ( typeof resultJson.modified != 'undefined' ) {
						$(element).parents('[modified]').attr('modified', resultJson.modified);
					}
				}, 
				'');
			}
		};
		
		editor.on('blur', function(e) 
		{
			e.editor.persist();
		});

		editor.on('instanceReady', function(e) 
		{
	      	registerBeforeUnloadHandler(function() 
	      	{
		      	e.editor.persist();
		      	return true;
	      	});
	    			
			if ( $(element).hasClass('wysiwyg-field') )
			{
		      	registerFormValidator(function() 
      			{ 
		      		e.editor.custom.updateForm();
		      		e.editor.persist();
		      		return true; 
			    });
		      	
		      	registerFormDestructorHandler(function () {
		      		e.editor.destroy();
		      	});
			}

			$(element).attr('title', '');
			
			$(element).find('a[href]').click( function(e) 
			{
				e.stopImmediatePropagation();
				window.location = $(this).attr('href');
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

$(document).ready( function()
{
	setupEditorGlobal( 'Files' );
});
