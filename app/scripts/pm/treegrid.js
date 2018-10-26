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

function mergeTreeGridRows( container, data )
{
    var sourceTable = data.find('.table-inner');
    var targetTable = container.find('.table-inner');

	$.each(targetTable.find("tr[object-id]"), function(index, value) {
		var targetRow = $(value);
        sourceTable.find('tr[object-id="'+targetRow.attr('object-id')+'"] td').each(function() {
        	if ( $(this).attr('uid') ) {
                targetRow.find('td[uid='+$(this).attr('uid')+']').html($(this).html());
			}
            if ( $(this).attr('id') ) {
        		if ( $(this).attr('id') == 'caption' ) {
                    targetRow.find('td[uid='+$(this).attr('id')+'] .fancytree-title').html($(this).html());
				}
				else {
                    targetRow.find('td[uid='+$(this).attr('id')+']').html($(this).html());
				}
            }
		})
	});
}

function refreshTreeGridItems()
{
	if ( !devpromOpts.windowActive ) return;
	if ( localTreeGridOptions.waitRequest ) {
        localTreeGridOptions.waitRequest.abort();
        localTreeGridOptions.waitRequest = null;
	}

	var items = $('#tablePlaceholder .table-inner:first').find('tr[object-id]');

    localTreeGridOptions.waitRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&view=trace&rows=all&offset1=0&wait=true',
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
	    {
			var skip = [];
			var html = $('<div>'+data+'</div>');

            updateUI(html);

			html.find(".object-changed[object-id]").each( function(index, value)
			{
				itemSelector = 'tr[object-id="'+$(this).attr('object-id')+'"]';
				if ( html.find(itemSelector).length < 1 ) {
                    var item = $('#tablePlaceholder .table-inner:first').find(itemSelector);
                    if ( item.length > 0 ) {
                        deleteRow(item);
                    }
                }
			});

			html.find('tr[object-id]').each( function() {
				var object_id = $(this).attr('object-id');
				
    	        var targetRow = $('#tablePlaceholder .table-inner:first').find('tr[object-id="'+object_id+'"]');
                if ( targetRow.length > 0 ) {
                    if ( $(this).attr('modified') <= targetRow.attr('modified') ) {
                        skip.push(object_id);
                        return true;
                    }
					targetRow.attr('modified', $(this).attr('modified'));
                }
                else {
                    var tree = $("#tablePlaceholder table.fancytree-ext-table").fancytree("getTree");
                    tree.reload();
                }
   	    	});

			$.each( skip, function(index, object_id) {
				html.find('tr[object-id="'+object_id+'"]').remove();
			});

            mergeTreeGridRows($('#tablePlaceholder'), html);
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
