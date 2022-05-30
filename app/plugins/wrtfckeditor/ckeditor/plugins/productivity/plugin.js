CKEDITOR.plugins.add( 'productivity',
{
	init: function( editor )
	{
		if ( editor.element.getAttribute('contenteditable') != 'true' ) return false;

		var baseUrl = '/pm/'+editor.element.getAttribute('project');

		editor.addCommand( 'productivityCreateIssue', {
			exec: function(editor) {
                var focusedName = editor.name;
				var objectClass = editor.element.getAttribute('objectclass');

                var text = editor.getSelection().getSelectedText();
				var data = {
					Caption: text.split(/[\s,;:.]+/).slice(0,7).join(" "),
					Description: text,
				};
				data[objectClass] = editor.element.getAttribute('objectid');
                if ( objectClass == "ProjectPage" ) {
                    editor.insertText('[I-]');
				}

				workflowNewObject(baseUrl+'/issues/board?mode=request&class=metaobject&entity=pm_ChangeRequest','Request','pm_ChangeRequest','', data, function(id)
				{
                    if ( objectClass != "ProjectPage" ) return;
                    var focusedEditor = CKEDITOR.instances[focusedName];
					if ( focusedEditor.element.hasClass('wysiwyg-input') ) return;
                    focusedEditor.setData(focusedEditor.getData().replace(/\[I\-\]/g, 'I-'+id));
				}, "false");
			}
		});
		editor.addCommand( 'productivityCreateTask', {
			exec: function(editor) {
				var focusedName = editor.name;
				var objectClass = editor.element.getAttribute('objectclass');

                var text = editor.getSelection().getSelectedText();
				var data = {
					Caption: text.split(/[\s,;:.]+/).slice(0,7).join(" "),
					Description: text,
				};
				data[objectClass] = editor.element.getAttribute('objectid');
                if ( objectClass == "ProjectPage" ) {
                    editor.insertText('[T-]');
                }

				workflowNewObject(baseUrl+'/tasks/board?class=metaobject&entity=pm_Task','Task','pm_Task','', data, function(id)
				{
                    if ( objectClass != "ProjectPage" ) return;
					var focusedEditor = CKEDITOR.instances[focusedName];
					if ( focusedEditor.element.hasClass('wysiwyg-input') ) return;
                    focusedEditor.setData(focusedEditor.getData().replace(/\[T\-\]/g, 'T-'+id));
				}, "false");
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