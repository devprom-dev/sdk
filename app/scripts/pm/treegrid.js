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
			var html = $('<div>'+data+'</div>');

            updateUI(html);

			html.find(".object-changed[object-id]").each( function(index, value) {
                var object_id = $(this).attr('object-id');

				itemSelector = 'tr[object-id="'+object_id+'"]';
				if ( html.find(itemSelector).length < 1 ) {
                    var item = $('#tablePlaceholder .table-inner:first').find(itemSelector);
                    if ( item.length > 0 ) {
                        var tree = $("#tablePlaceholder table.fancytree-ext-table").fancytree("getTree");
						var node = tree.getNodeByKey(object_id);
						if ( node ) node.remove();
                        return true;
                    }
                }
                update.push(object_id);
			});

			if ( update.length < 1 ) return;

            var tree = $("#tablePlaceholder table.fancytree-ext-table").fancytree("getTree");
            try {
                $.ajax({
                    type: "POST",
                    url: tree.options.source.url + '&parent=all&roots='+update.join('-'),
                    dataType: "html",
                    proccessData: false,
                    success: function (result, status, xhr) {
                        try {
                            data = jQuery.parseJSON(result);
                            $.each(data, function(index, item) {
                            	var node = tree.getNodeByKey(item.id);
                            	if ( node ) {
                            	    if ( node.parent && node.parent.key != item.parentid ) {
                                        node.remove();
                                        node = tree.getNodeByKey(item.id);
                                        if ( !node ) {
                                            var parent = item.parentid > 0
                                                ? tree.getNodeByKey(item.parentid)
                                                : tree.getRootNode();
                                            parent.addChildren(item);
                                        }
                                        tree.activateKey(item.id);
                                    }
                                    else {
                                        treeGridRemoveChildren(tree, item);
                                        node.fromDict(item);
                                    }
								}
								else {
									var parent = item.parentid > 0
                                        ? tree.getNodeByKey(item.parentid)
                                        : tree.getRootNode();

                                    parent.addChildren(item);
									tree.activateKey(item.id);
								}
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

function treeGridRemoveChildren( tree, item )
{
    if ( !item.children ) return;
    $.each(item.children, function(index, child) {
        var node = tree.getNodeByKey(child.id);
        if ( node ) node.remove();
        treeGridRemoveChildren(tree, child);
    })
}