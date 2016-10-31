var documentOptions = {
	waitRequest: null,
	scrollLastPos: -1,
	vscrollTries: 0,
	vscrollAllowed: function( sensitivity ) {
		if ( $(window).scrollTop() > 0 ) return false;
		var allowed = this.vscrollTries++ > sensitivity;
		if ( allowed ) this.vscrollTries = 0;
		return allowed;
	},
	className: '',
	visiblePages: 10,
	cachedPages: 30,
	scrollable: true,
	reorder: true,
	pageOpen: 0,
	totalPages: 1,
	is_numeric: function (input) {
		var RE = /^-{0,1}\d*\.{0,1}\d+$/; return (RE.test(input));
	},
	is_integer: function (input) {
		var RE = /^-{0,1}\d*$/; return (RE.test(input));
	},
	addedQueue: []
};
	
var localOptions = {};

function initializeDocument( page_id, options ) 
{
	localOptions = $.extend(documentOptions, options);
	
	selectPageInTree(page_id);

	setTimeout( 'refreshListItems()', 500 );

	if ( localOptions.scrollable )
	{
		$(document)
			.bind('DOMMouseScroll', function(e) {
				if ( e.originalEvent.detail < 0 ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(6) ) {
							buildTopWaypoint(localOptions);
						}
					}
				}
				if ( e.originalEvent.detail > 0 ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(localOptions);
					}
				}
			})
			.bind('mousewheel', function(e) {
				var offset = typeof e.originalEvent.deltaY != 'undefined' 
						? e.originalEvent.deltaY : -e.originalEvent.wheelDelta; 
				if ( offset < 0 ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(6) ) {
							buildTopWaypoint(localOptions);
						}
					}
				}
				if ( offset > 0 ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(localOptions);
					}
				}
			})
			.bind('touchmove', function(e) {
				if ( localOptions.scrollLastPos < 0 ) {
					localOptions.scrollLastPos = e.originalEvent.touches[0].pageY;
					return;
				}
				if ( e.originalEvent.touches[0].pageY > localOptions.scrollLastPos ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(3) ) {
							buildTopWaypoint(localOptions);
						}
					}
					localOptions.scrollLastPos = -1;
				}
				if ( e.originalEvent.touches[0].pageY < localOptions.scrollLastPos ) {
					if ( $('#tablePlaceholder .table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(localOptions);
					}
					localOptions.scrollLastPos = -1;
				}
			})
			.keydown(function(e)
			{
				if ( $(e.target).is('.cke_editable') ) return;
			    switch(e.which) {
			        case 38: // up
			        	buildTopWaypoint(localOptions);
			        	break;
		
			        case 40: // down
			        	buildBottomWaypoint(localOptions);
			        	break;
		
			        default: 
			        	return;
			    }
			});
	}
	
	makeupUI($('#tablePlaceholder .table-inner:first'));

	$('#toggle-structure-panel').button().off('click').on('click', function (e) {
		var button = $(this);
		e.stopImmediatePropagation();
		if (!button.hasClass('active')) {
			button.addClass('active');
		} else {
			button.removeClass('active');
		}
		toggleDocumentStructure(page_id);
	});

	$("body").on("contextmenu", "#tablePlaceholder .table-inner tr[object-id]", function(e) {
		$('.dropdown-fixed.open, .btn-group.open').removeClass('open');
		var item = $(this).find('td#operations .dropdown-fixed');
		if ( item.length > 0 ) {
			item.last().addClass('open').removeClass('last')
				.css({
					left: e.pageX,
					top: e.pageY
				});
			return false;
		}
	});
}

function buildBottomWaypoint(options)
{
	if ( $(document).scrollTop() + $(window).height() < $('.table-inner').height() - 100 ) return;
	
	var progressBar = $('#tablePlaceholder .table-inner tr.row-empty:last');
	if ( progressBar.find('td#content').html() != '' ) return;
	
	$('#tablePlaceholder .table-inner>tbody>tr[object-id]').not('.row-empty').each( function()
    {
		if ( !$(this).next().is('.row-empty') ) return;

        var ids = $(this).nextAll('.row-empty').map(function() {
            return $(this).attr('object-id');
        }).get().slice(0, options.scrollable ? options.cachedPages : 1);

		openPage( ids, false, progressBar, function(pageId) {
			setTimeout( function() {
				restoreCache(pageId, function() {
					progressBar.find('td#content').html('');
					setRowFocus(pageId);
        	    });
	    		$('#tablePlaceholder .table-inner:first').find('tr[object-id="'+pageId+'"]')
	    			.prevAll('tr[object-id]').slice(options.visiblePages).each(function() {
						clearRow($(this));
					});
    		}, 
    		400 );
    	});
	});
}

function buildTopWaypoint(options)
{
	if ( $(document).scrollTop() > 49 ) return;
	
	var progressBar = $('#tablePlaceholder .table-inner tr.row-empty:first');
	if ( progressBar.find('td#content').html() != '' ) return;
	
	$('#tablePlaceholder .table-inner>tbody>tr[object-id]').not('.row-empty').each( function()
	{
		if ( !$(this).prev().is('.row-empty') ) return;

		var ids = $(this).prevAll('.row-empty').map(function() {
            return $(this).attr('object-id');
        }).get().slice(0, options.scrollable ? options.cachedPages : 1);

		openPage( ids, false, progressBar, function(pageId) {
			setTimeout( function() {
	    		restoreCache(pageId, function() {
					progressBar.find('td#content').html('');
					setRowFocus(pageId);
	    		});
	    		$('#tablePlaceholder .table-inner:first').find('tr[object-id="'+pageId+'"]')
	    			.nextAll('tr[object-id]').slice(localOptions.visiblePages).each(function() {
                        clearRow($(this));
                    });
			}, 
			400 );
		});
		return false;
	});
}

function restoreCache( pageId, callback )
{
	ids = [];
    var selector = pageId == ''
        ? '#documentCache tr[object-id]'
        : '#documentCache tr[object-id="'+pageId+'"]';
	
	$(selector).each(function(i, cachedItem)
	{
		cachedItem = $(cachedItem);
		
		ids.push(cachedItem.attr('object-id'));
		itemSelector = 'tr[object-id="'+cachedItem.attr('object-id')+'"]';
	
		// trying to pick up given item
		var holder = $('#tablePlaceholder .table-inner:first').find(itemSelector);
        if ( pageId == '' && ($('.wiki-page-document').length > 0 && holder.length < 1 || holder.is('.row-empty')) ) return true; // skip to update non visible rows when is refreshed blindly

		var group_id = cachedItem.attr('group-id');
		if ( typeof group_id != 'undefined' ) {
			// if group is defined and it is different than current then drop the item
			if ( holder.length > 0 && holder.attr("group-id") != group_id ) {
				holder.remove();
			}
			// trying to pick up item with the given group
			itemSelector += '[group-id="'+group_id+'"]'; 
		}

		// initialize editor
		cachedItem.find('[contenteditable]').each(function(i) {
			var funcName = 'setup' + $(this).attr('id');
			if ( typeof window[funcName] != 'undefined' ) {
				window[funcName]();
			}
		});

		holder = $('#tablePlaceholder .table-inner:first').find(itemSelector);
		if ( (holder.length < 1 || holder.attr('sort-value') != cachedItem.attr('sort-value')) && localOptions.pageOpen < 1 )
		{
			// if there is no item then create new one
			$('#tablePlaceholder .table-inner:first tr#no-elements-row').remove();
			
			var group_selector = '#tablePlaceholder .table-inner:first tbody tr[group-id="'+group_id+'"]';
			if ( typeof group_id != 'undefined' && $(group_selector).length > 0 ) {
				if ( cachedItem.attr("sort-value") == "" )
                {
					// put it at the end of the group
					if ( $(group_selector+":last").is(".row-empty") ) {
						cachedItem = cachedItem.clone();
						clearRow(cachedItem);
					}
					$(group_selector+":last").after(cachedItem);
					if ( holder.length > 0 ) holder.remove();
				}
				else if( cachedItem.attr("sort-type") == "desc" )
				{
					var list = null;
					if ( localOptions.is_integer(cachedItem.attr("sort-value")) ) {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return false;
							return parseInt($(this).attr("sort-value")) <= parseInt(cachedItem.attr("sort-value"));
						});
					}
					else {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return false;
							return $(this).attr("sort-value").toString() <= cachedItem.attr("sort-value").toString();
						});
					}

					var anchor = list.length < 1 ? $(group_selector+":first") : list.first();
					if ( anchor.is(".row-empty") && $(group_selector+":not(.row-empty)").length > 0 ) {
						cachedItem = cachedItem.clone();
						clearRow(cachedItem);
					}
					list.length < 1 ? anchor.after(cachedItem) : anchor.before(cachedItem);
					if (holder.length > 0) holder.remove();
				}
				else
				{
					var list = null;
					if ( localOptions.is_integer(cachedItem.attr("sort-value")) ) {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return true;
							return parseInt($(this).attr("sort-value")) >= parseInt(cachedItem.attr("sort-value"));
						});
					}
					else {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return true;
							return $(this).attr("sort-value").toString() >= cachedItem.attr("sort-value").toString();
						});
					}

					var anchor = list.length < 1 ? $(group_selector+":last") : list.first();
					if ( anchor.is(".row-empty") && $(group_selector+":not(.row-empty)").length > 0 ) {
						cachedItem = cachedItem.clone();
						clearRow(cachedItem);
					}
					list.length < 1 ? anchor.after(cachedItem) : anchor.before(cachedItem);
					if (holder.length > 0) holder.remove();
				}
			}
			else
			{
				// put it in the end of the table
                if ( holder.length < 1 && localOptions.totalPages < 2 ) {
                    $('#tablePlaceholder .table-inner:first tbody').append(cachedItem);
                }
                else {
					var rowNumSelector = 'td[name="row-num"]';
					cachedItem.find(rowNumSelector).replaceWith(holder.find(rowNumSelector).clone());
                    holder.replaceWith(cachedItem);
                }
			}
		}
		else
		{
			// just refresh item
			holder.height(cachedItem.height());
			var rowNumSelector = 'td[name="row-num"]';
			cachedItem.find(rowNumSelector).replaceWith(holder.find(rowNumSelector).clone());
			holder.replaceWith(cachedItem);
		}
	});
    
    drawNumbers();
    
	if ( typeof callback != 'undefined' ) callback();

    refreshListItems();

	$('#tablePlaceholder .table-inner:first tr[object-id]').filter(function (i,e) {
		return $.inArray($(e).attr('object-id'), ids) >= 0;
	})
	.fadeTo(300, 1, function() {
	});

	return true;
}

function drawNumbers()
{
	var firstNumber = $('#tablePlaceholder .table-inner:first td[name="row-num"]').first().text();
	if ( firstNumber == '' ) return;
	
	$('#tablePlaceholder .table-inner:first td[name="row-num"]').each( function(index, e) {
		$(e).html(parseInt(firstNumber,10) + index);
	});
}

function openPage( ids, force, progress_element, callback )
{
	if ( ids.length < 1 ) return;
	
	$(progress_element).find('td#content').html('<div class="document-loader"></div>');
    var restoreLoaded = true;

	if ( $('#documentCache').find('tr[object-id="'+ids[0]+'"]').not('.row-empty').length > 0 ) {
        if ( typeof callback != 'undefined' ) {
            restoreLoaded = false;
            callback(ids[0]);
        }
	}
	
	load_ids = [];
	
	$.each( ids, function(index, value)	{
		if ( $('#documentCache').find('tr[object-id="'+value+'"]').not('.row-empty').length > 0 ) return true;
		if ( $('#tablePlaceholder .table-inner:first').find('tr[object-id="'+value+'"]').not('.row-empty').length > 0 ) return true;

		load_ids.push(value);
	});

    if ( load_ids.length < 1 ) return; // nothing to load into the cache
	if ( !force && ids.length - load_ids.length >= (localOptions.cachedPages - 3) ) return; // fill up the cache if less than 3 pages left to be displayed

	url = filterLocation.locationTableOnly()+'&class='+localOptions.className+
		'&'+localOptions.className+'='+load_ids.join(',')+'&tableonly=true&object='+load_ids.join(',');
	
	$.ajax({
		type: "GET",
		url: url,
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
		{
	        var container = $('#documentCache');

            mergeRows(container, $(data));
			makeupUI(container);
			completeUIExt(container);

			if ( restoreLoaded && typeof callback != 'undefined' ) callback(load_ids[0]);
		}
	});
}

function mergeRows( container, data )
{
    var sourceTable = data.find('.table-inner');
    var targetTable = container.find('.table-inner');

    $.each(sourceTable.find("tr[object-id]"), function(index, value) {
        var treeItem = $('.treeview-label[object-id="'+$(value).attr('object-id')+'"]');
		var row = $(value).find('div[attributename="Caption"]');
        if ( treeItem.length > 0 && row.length > 0 ) {
            treeItem.find('span.title').text(row.text());
        }
    });

    if ( targetTable.length < 1 ) {
		sourceTable.clone().appendTo(container);
		container.find('tr[group-id],tr[object-id]').remove();
		targetTable = container.find('.table-inner');
    }
	$.each(targetTable.find("tr.info[group-id]"), function(index, value) {
		if ( $(value).attr('group-id') == "" ) return true;
		var rowModified = sourceTable.find('tr.info[group-id="'+$(value).attr('group-id')+'"]');
		if ( rowModified.length > 0 ) $(value).replaceWith(rowModified);
	});
	$.each(sourceTable.find("tr.info[group-id]"), function(index, value) {
		if ( $(value).attr('group-id') == "" ) return true;
		var wasRow = targetTable.find('tr.info[group-id="'+$(value).attr('group-id')+'"]');
		if ( wasRow.length < 1 ) targetTable.append($(value));
	});
	$.each(targetTable.find("tr[object-id]"), function(index, value) {
		var rowModified = sourceTable.find('tr[object-id="'+$(value).attr('object-id')+'"]');
		if ( rowModified.length > 0 ) $(value).replaceWith(rowModified);
	});
	$.each(sourceTable.find("tr[object-id]"), function(index, value) {
		var wasRow = targetTable.find('tr[object-id="'+$(value).attr('object-id')+'"]');
		if ( wasRow.length < 1 ) targetTable.append($(value));
	});
}

function showNewRows( rows ) {
	if ( typeof loadContentTree != 'undefined' ) {
		loadContentTree(function() {
			selectPageInTree(rows[0]);
		});
	}
	restoreCache(rows[0], function() {});
}

var sortNodes = function(nodes, mapper, compare) {

    var map = [],
        parent = $(nodes[0]).parent().get(0),
        i, l;

    for (i=0, l=nodes.length; i < l ; i++) {
        map.push([i, mapper(nodes[i]) ]);
    }

    var formerMap = map.slice( 0);
    map.sort(compare);
    if (formerMap.join('') == map.join('')) {
        return;
    }

    for (i=0, l=map.length; i < l ; i++) {
        parent.appendChild(nodes[map[i][0]]);
    }
};

function reorderSections()
{
    return;
	console.log('reordering sections');
	
    sortNodes($('#tablePlaceholder .table-inner:first').find('tr[object-id]'),
        function (elem) {
            return $(elem).attr('sort-value');
        },
        function (a, b) {
            if (a[1] == null && b[1] != null) return 1;
            if (a[1] != null && b[1] == null) return -1;
            return a[1] < b[1] ? -1 : 1;
        }
    );
}

function refreshListItems()
{
	if ( !devpromOpts.windowActive ) {
		setTimeout( function() { refreshListItems(); }, 3000);
		return;
	}

	if ( localOptions.waitRequest ) {
		localOptions.waitRequest.abort();
		localOptions.waitRequest = null;
	}

	var items = $('#tablePlaceholder .table-inner:first').find('tr[object-id]:not(.row-empty)');

	localOptions.waitRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&rows=all&offset1=0&wait=true',
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
	    {
			var changed = [];
            var newItems = [];
			var skip = [];
			var comments_open = [];
			var html = $(data);
			
			beforeUnload($('#tablePlaceholder .table-inner:first form[id]').attr('id'));
			if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) return;

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
                    if ( targetRow.find('.document-item-bottom').length > 0 ) comments_open.push(object_id);
                    changed.push(object_id);
                }
                else {
                    newItems.push(object_id);
                }

   	    	    // append group if missed
   	    	    var group_id = $(this).attr('group-id');
   	    	    if ( $('#tablePlaceholder .table-inner:first').find('tr.info[group-id="'+group_id+'"]').length < 1 ) {
   	    	    	$('#tablePlaceholder .table-inner:first').append(html.find('tr.info[group-id="'+group_id+'"]'));
   	    	    }
   	    	});

			$.each( skip, function(index, object_id) {
				var row = html.find('tr[object-id="'+object_id+'"]');
				clearRow(row);
				row.remove();
			});

			var container = $('#documentCache');
            mergeRows(container, html);
	        makeupUI(container);
	        completeUIExt(container);

			$.each( comments_open, function(index, object_id) {
				var row = container.find('tr[object-id="'+object_id+'"]');
				row.find('.document-item-bottom-hidden').each(function(item) {
					$(item).removeClass('document-item-bottom-hidden');
				});
		    });

			if ( newItems.length > 0 ) {
				if ( localOptions.visiblePages == 1 || $.inArray(newItems[0], localOptions.addedQueue) >= 0 ) {
					localOptions.addedQueue = [];
					showCreatedPage(newItems[0]);
				}
				else {
					showNewRows(newItems);
				}
			}
	    },
	    complete: function(xhr, textStatus)
	    {
	    	if ( textStatus == "abort" ) return;
	    	if ( xhr.responseText == "" ) return;
	    	if ( $.inArray(xhr.status, [302,301,500,404]) != -1 ) return;
	    	
    		setTimeout( function() {
    			restoreCache('', function() {
					$('#tablePlaceholder .table-inner:first tr.row-empty:not([sort-value])').remove();
    				if ( localOptions.reorder ) reorderSections();
    			});
            }, $.inArray(textStatus, ["error","timeout","parsererror"]) < 0 ? 100 : 180000);
	    },
	    error: function (xhr, ajaxOptions, thrownError) {
    		setTimeout( function() {
    			restoreCache('', function() {
					$('#tablePlaceholder .table-inner:first tr.row-empty:not([sort-value])').remove();
    				if ( localOptions.reorder ) reorderSections();
    			});
            }, 180000);
	    }
	});		
}	

function deleteRow( element )
{
	var group_id = element.attr("group-id");

	clearRow(element);
	if ( typeof group_id != 'undefined' && group_id != '' && $('#tablePlaceholder .table-inner:first tr[group-id="'+group_id+'"]').length < 3 ) {
        $('#tablePlaceholder .table-inner:first tr.info[group-id="'+group_id+'"]').remove();
    }
    $('.treeview-label[object-id="'+element.attr('object-id')+'"]').parent().remove();
    element.remove();
	drawNumbers();
}

function clearRow( element )
{
	if ( typeof CKEDITOR != 'undefined' )
	{
		element.find('td#content div.cke_editable').each( function() 
		{
			var editor = CKEDITOR.instances[$(this).attr('id')];
			if ( !editor ) return true;

			editor.persist();
			editor.destroy();
		});
	}

	element.find('td').each( function() { $(this).html(''); }); 
	element.addClass('row-empty');
}

function clearDocument()
{
    beforeUnload($('#tablePlaceholder .table-inner:first form[id]').attr('id'));
    $('#tablePlaceholder .table-inner:first tr[object-id]').each( function() { clearRow($(this)); });
}

function gotoRandomPage( page_id, load_pages, use_cache )
{
	if ( use_cache && $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]').not('.row-empty').length > 0 ) {
		scrollToPage(page_id);
		setRowFocus(page_id);
		return;
	}

    clearDocument();
    scrollToPage(page_id);

    var item = $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]');
    var nextIds = item.nextAll('.row-empty').map(
                    function() {
                        return $(this).attr('object-id');
                    }).get().slice(0, localOptions.scrollable ? localOptions.cachedPages : 0);
    var ids = item.prevAll('.row-empty').map(
                function() {
                    return $(this).attr('object-id');
                }).get().slice(0, localOptions.scrollable ? localOptions.cachedPages : 0).concat(nextIds);

    ids.unshift(page_id);
	selectPageInTree(page_id);
	openPage( ids, true, $('#tablePlaceholder .table-inner:first tr[object-id="'+ids[0]+'"]'), function(pageId) {
        restoreCache(pageId, function() {
			setRowFocus(pageId);
			$.each(nextIds.slice(0,load_pages), function(index,value) {
                restoreCache(value, function() {});
            });
        });
	});
}

function selectPageInTree( page_id )
{
	if ( typeof page_id == 'undefined' ) return;
    if ( $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]').length < 1 ) return;

	$('li.treeview > div > a > span.label').removeClass('label');
	
	$('li.treeview[id="'+page_id+'"]').parents('li').each(function() {
		$(this).children('div.expandable-hitarea').first().click();
	});
	
	$('li.treeview[id="'+page_id+'"] > div > a > span').addClass('label');
}

function scrollToPage( page_id )
{
	if ( typeof page_id == 'undefined' ) return;
    var item = $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]');
    if ( item.length < 1 ) {
        if ( timeout > 0 ) {
            setTimeout(function() {
                scrollToPage(page_id, timeout - 1);
            }, timeout * 300);
        }
        return;
    }

    pos = item.offset().top;
	if ( $('.documentToolbar').length > 0 ) {
		pos -= $('.documentToolbar').height() + 9;
	}
	if ( pos > $(document).scrollTop() && pos < ($(window).height() - 80) ) return;
	$('body, html').animate({ scrollTop: pos }, 50);
    selectPageInTree( page_id );
}

function showCreatedPage( pageId )
{
	if ( typeof loadContentTree != 'undefined' ) {
		loadContentTree( function() {
			gotoRandomPage(pageId, 4, false);
			selectPageInTree(pageId);
		});
	}
}

function makeupUI( container )
{
	hasCheckboxes = $('#tablePlaceholder .table-inner:first th.visible[uid=checkbox]').length > 0;

	if ( hasCheckboxes )
	{
		container.find("tr:not(.info)").each( function(i, e) {
			$(e).find('td[uid=checkbox]').removeClass('hidden');
		});
	}
	else
	{
		container.find("tr:not(.info)").each( function(i, e) {
			$(e).find('td[uid=checkbox]').addClass('hidden');
		});
	}

	container.find("tr:not(.info)").each( function(i, e) {
		$(e).dblclick( function(evt) {
			if ( $(evt.target).closest('td[uid="checkbox"],td#operations,div.wysiwyg').length > 0 ) return;
			var ref = $(this).find('td[id=operations] a:not([data-toggle]):first');
			if (ref.is('[onclick]')) {
				ref.click();
			} else if (ref.is('[href]')) {
				window.location = ref.attr('href');
			}
		}).click( function(e) {
			var id = $(this).attr('object-id');
			if ( id != '' && $(e.target).is('td') ) {
				$(document).trigger("trackerItemSelected", id);
			}
		});
	});
	
	container.find("tr:not(.info) td:not(#content)").dblclick( function(e) {
		if ( $(this).find('.wysiwyg').length > 0 ) return;
		if (window.getSelection)
	        window.getSelection().removeAllRanges();
	    else if (document.selection)
	        document.selection.empty();
	});
	
	container.find("td#content").hover(
			function() {
				if ( $(this).find('.document-page-bottom .editor-area').length < 1 ) {
					$(this).find('.document-item-bottom-hidden').show();
				}
				if ( $(this).parent().is(':not(.row-empty)')) {
					selectPageInTree($(this).parent().attr('object-id'));
				}
			},
			function() {
				if ( $(this).find('[contenteditable]:focus').length > 0 ) return;
				$(this).find('.document-item-bottom-hidden').hide();
			}
		);
	container.find("td#content div[contenteditable]")
		.focus( function() {
			$(this).parents('td#content').find('.document-item-bottom-hidden').show();
			if ( $(this).parents('tr').first().is(':not(.row-empty)')) {
				selectPageInTree($(this).parents('tr').first().attr('object-id'));
			}
		})
		.focusout( function() {
			$(this).parents('td#content').find('.document-item-bottom-hidden').hide();
		});

	container.find('.document-page-comments-link').click( function(event) {
		toggleDocumentPageComments($(this));
	});
	container.find("tr[object-id]").each( function(i,e) {
		if ( cookies.get('comments-state-'+$(this).attr('object-id')) == 'open' && $(this).find('.comment-line').length > 0 ) {
			toggleDocumentPageComments($(this).find('.comments-section'));
		}
	});

	var locstr = String(window.location);
	if ( locstr.indexOf('#comment') > 0 )
	{
		var commentString = locstr.substring(locstr.indexOf('#comment'));
		var parts = commentString.split('#');
		var section = container.find('#'+parts[1]).parents('.comments-section');
		if ( parts.length > 0 && !section.is(':visible') ) {
			toggleDocumentPageComments(section);
		}
	}

	toggleBulkActions();
}

function openCreatedPage( id ) {
	localOptions.addedQueue.push(id);
}

function setRowFocus( page_id ) {
	if ( localOptions.visiblePages == 1 ) return;
	var contentElement = $('[id*=WikiPageContent][objectid='+page_id+']');
	contentElement.focus();
	var editor = CKEDITOR.instances[contentElement.attr("id")];
	if ( editor ) editor.focus();
}

function toggleDocumentStructure( documentId )
{
	cookies.set('toggle-structure-panel-' + documentId, !$('div.wiki-page-tree').is(':visible'));
	window.location.reload();
}

function toggleDocumentTreePlacement( placement ) {
	cookies.set('document-tree-placement', placement);
	window.location.reload();
}

function toggleDocumentPageComments(container) {
	var bottom = container.parents('.document-page-bottom');
	bottom.find('.comments-section').each(function(value) {
		$(this).toggle();
		cookies.set(
			'comments-state-'+$(this).parents('tr[object-id]').attr('object-id'),
			$(this).is(':visible') ? 'open' : 'closed'
		);
		if ( $(this).is(':visible') && $(this).find('.comment-line').length < 1 ) {
			$(this).find('.btn-success').click();
		}
	});
	bottom.find('.document-item-bottom-hidden').hide();
	bottom.find('.comments-cell').toggle();
}
