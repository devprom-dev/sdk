var treeGridOptions = {
	waitRequest: null,
	className: ''
};
var timeout = 500;
var localTreeGridOptions = {};

function initializeTreeGrid( page_id, options )
{
    localTreeGridOptions = $.extend(treeGridOptions, options);

	setTimeout( function() {
        refreshTreeGridItems();
	}, 500 );
	setInterval( function() {
        refreshTreeGridItems();
	}, 180000);
    $(document).on('windowActivated', function() {
        refreshTreeGridItems();
    })
}

function refreshTreeGridItems()
{
	if ( !devpromOpts.windowActive ) return;
	if ( localTreeGridOptions.waitRequest ) {
        localTreeGridOptions.waitRequest.abort();
        localTreeGridOptions.waitRequest = null;
	}

    localTreeGridOptions.waitRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&view=trace&rows=all&offset1=0&wait=true',
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
	    {
            var skip = [];
			var update = [];
            var updateKeys = [];
			var html = $('<div>'+data+'</div>');

            updateUI(html);

            var className = '';
			html.find(".object-changed[object-id]").each( function(index, value) {
                className = $(this).attr('object-class');
                var object_id = className + $(this).attr('object-id');
                update.push($(this).attr('object-id'));
                updateKeys.push(object_id);
			});

			if ( update.length < 1 ) return;

            var tree = $.ui.fancytree.getTree("#tablePlaceholder table.fancytree-ext-table");
            try {
                $.ajax({
                    type: "POST",
                    url: tree.options.dataUrl + '&parent=all&' + className.toLowerCase() + '=' + update.join(','),
                    dataType: "html",
                    proccessData: false,
                    success: function (result, status, xhr) {
                        try {
                            data = jQuery.parseJSON(result);
                            $.each(data, function(index, item) {
                            	var node = tree.getNodeByKey(item.key);
                            	if ( node ) {
                            	    if ( node.data.modified != item.modified ) {
                                        var expanded = node.expanded;
                                        node.fromDict(item);
                                        if ( expanded ) {
                                            node.setExpanded();
                                        }
                                    }
								}
								else {
									var parent = item.parentkey != ''
                                        ? tree.getNodeByKey(item.parentkey)
                                        : tree.getRootNode();
									if ( parent ) {
                                        parent.addChildren(item);
                                        parent.setExpanded();
                                    }
								}
                                var updateIndex = $.inArray(item.key, updateKeys);
                                if (updateIndex != -1) {
                                    updateKeys.splice(updateIndex, 1);
                                }
                                var updateIndex = $.inArray(item.parentkey, updateKeys);
                                if (updateIndex != -1) {
                                    updateKeys.splice(updateIndex, 1);
                                }
                            });
                            $.each(updateKeys, function(index, key) {
                                var node = tree.getNodeByKey(key);
                                if (node) node.remove();
                            });
                        }
                        catch( e )
                        {}
                    },
                    error: function (xhr, status, error) {
                        reportError(ajaxErrorExplain(xhr, error));
                    }
                });
            }
            catch(e) {
                reportError(e.toString());
            }
	    },
	    complete: function(xhr, textStatus)
	    {
            var nativeResponse = xhr.getResponseHeader('X-Devprom-UI') == 'tableonly';
            if ( nativeResponse && xhr.responseText.indexOf('div') > -1 ) {
                setTimeout( function() {
                    refreshTreeGridItems();
                }, 300);
			}
	    }
	});
}	

function treeGridRemoveChildren( tree, item, updateKeys )
{
    if ( !item.children ) return;
    $.each(item.children, function(index, child) {
        var node = tree.getNodeByKey(child.key);
        if ( node ) {
            node.remove();
        }
        var updateIndex = $.inArray(child.key, updateKeys);
        if (updateIndex != -1) {
            updateKeys.splice(updateIndex, 1);
        }
        treeGridRemoveChildren(tree, child, updateKeys);
    })
}