CKEDITOR.plugins.add( 'productivity',
{
	init: function( editor )
	{
		if ( editor.element.getAttribute('contenteditable') != 'true' ) return false;

		var baseUrl = '/pm/'+editor.element.getAttribute('project');

		editor.addCommand( 'productivityCreateIssue', {
			exec: function(editor) {
				var focusedEditor = editor;
				var focusedSelection = focusedEditor.getSelection();
				var text = focusedSelection.getSelectedText();
				var objectClass = editor.element.getAttribute('objectclass');

				var data = {
					Caption: text.split(/[\s,;:.]+/).slice(0,7).join(" "),
					Description: text,
				};
				data[objectClass] = editor.element.getAttribute('objectid');

				var focusManager = new CKEDITOR.focusManager( focusedEditor );
				workflowNewObject(baseUrl+'/issues/board?mode=request&class=metaobject&entity=pm_ChangeRequest','Request','pm_ChangeRequest','',cket('issue-title'), data, function(id) {
					if ( focusedEditor.element.hasClass('wysiwyg-input') ) return;
					if ( objectClass != "ProjectPage" ) return;
					if ( text != "" ) focusedEditor.insertText(text + ' ');
					focusedEditor.insertText(' [I-'+id+']');
					focusedEditor.persist(true);
					focusManager.blur();
				});
			}
		});
		editor.addCommand( 'productivityCreateTask', {
			exec: function(editor) {
				var focusedEditor = editor;
				var focusedSelection = focusedEditor.getSelection();
				var text = focusedSelection.getSelectedText();
				var objectClass = editor.element.getAttribute('objectclass');

				var data = {
					Caption: text.split(/[\s,;:.]+/).slice(0,7).join(" "),
					Description: text,
				};
				data[objectClass] = editor.element.getAttribute('objectid');

				var focusManager = new CKEDITOR.focusManager( focusedEditor );
				workflowNewObject(baseUrl+'/tasks/board?class=metaobject&entity=pm_Task','Task','pm_Task','',cket('task-title'), data, function(id) {
					if ( focusedEditor.element.hasClass('wysiwyg-input') ) return;
					if ( objectClass != "ProjectPage" ) return;
					if ( text != "" ) focusedEditor.insertText(text + ' ');
					focusedEditor.insertText(' [T-'+id+']');
					focusedEditor.persist(true);
					focusManager.blur();
				});
			}
		});
		editor.addCommand( 'productivityComment', {
			exec: function(editor) {
				var focusedEditor = editor;
				var focusedSelection = focusedEditor.getSelection();
				var text = focusedSelection.getSelectedText();

				var data = {
					Caption: text != "" ? '<blockquote>'+text+'</blockquote> <p></p>' : ""
				};
				var className = editor.element.getAttribute('objectclass').toLowerCase();
				var objectId = editor.element.getAttribute('objectid');

				var focusManager = new CKEDITOR.focusManager( focusedEditor );
				focusedEditor.persist(true);
				focusManager.blur();

				workflowNewObject(baseUrl+'/comments/'+className+'/'+objectId+'','Comment','Comment','',cket('comment-title'), data, function(id) {
					var text = focusedSelection.getSelectedText();
					if ( text != "" && !focusedEditor.element.hasClass('wysiwyg-input') ) {
						var element = CKEDITOR.dom.element.createFromHtml(
							'<span comment-id="'+id+'">'+text+'</span>'
						);
						focusedEditor.insertElement(element);
						focusedEditor.persist(true);
						focusManager.blur();
					}
					toggleDocumentPageComments($(focusedEditor.element.$).closest('tr').find('.comments-section:hidden'), false);
				});
			}
		});

		if ( editor.element.hasClass('wysiwyg-input') ) return false;

		if ( editor.contextMenu ) {
			editor.addMenuGroup( 'productivityGroup' );
			editor.addMenuItem( 'productivityCreateIssueItem', {
				label : cket('new-issue'),
				icon : '',
				command : 'productivityCreateIssue',
				group : 'productivityGroup'
			});
			editor.contextMenu.addListener( function( element ) {
				if ( element && $.inArray(element.getName(), ['img','a']) >= 0 ) {
					return false;
				}
				return { productivityCreateIssueItem : CKEDITOR.TRISTATE_OFF };
			});
			editor.addMenuItem( 'productivityCreateTaskItem', {
				label : cket('new-task'),
				icon : '',
				command : 'productivityCreateTask',
				group : 'productivityGroup'
			});
			editor.contextMenu.addListener( function( element ) {
				if ( element && $.inArray(element.getName(), ['img','a']) >= 0 ) {
					return false;
				}
				return { productivityCreateTaskItem : CKEDITOR.TRISTATE_OFF };
			});
			editor.addMenuItem( 'productivityCommentItem', {
				label : cket('new-comment'),
				icon : '',
				command : 'productivityComment',
				group : 'productivityGroup'
			});
			editor.contextMenu.addListener( function( element ) {
				if ( !productivityCheckSelection(element) ) {
					return null;
				}
				return { productivityCommentItem : CKEDITOR.TRISTATE_OFF };
			});
		}
	}
});

function productivityCheckSelection(element) {
	if ( element && $.inArray(element.getName(), ['img','a']) >= 0 ) {
		return false;
	}
	var sel = window.getSelection();
	if (sel.toString() == '') {
		return false;
	}
	return true;
}