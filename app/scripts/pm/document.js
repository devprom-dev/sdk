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
	visiblePages: 30,
	cachedPages: 5,
	scrollable: true,
	reorder: true,
    groupAttribute: '',
	draggable: true,
	pageOpen: 0,
	totalPages: 1,
	is_numeric: function (input) {
		var RE = /^-{0,1}\d*\.{0,1}\d+$/; return (RE.test(input));
	},
	is_integer: function (input) {
		var RE = /^-{0,1}\d*$/; return (RE.test(input));
	},
	addedQueue: [],
	sortedQueue: [],
	uiExtensionsEnabled: true,
	scrollbar: null
};
var timeout = 500;
var localOptions = {};

function initializeDocument( page_id, options )
{
	localOptions = $.extend(documentOptions, options);

	setTimeout( function() {
        refreshListItems();
	}, 500 );
	setInterval( function() {
        refreshListItems();
	}, 180000);
    $(document).on('windowActivated', function() {
        refreshListItems();
    });

  	makeupUI($('#tablePlaceholder'));

	if ( $('.table-inner-div').length > 0 ) {
		documentOptions.scrollbar = new PerfectScrollbar('.table-inner-div', {
			suppressScrollX: true,
			wheelPropagation: false
		});
		if ( localOptions.scrollable ) {
			var wasOverElement = 0;
			$('.table-inner-div')
				.bind('ps-scroll-y', function(e) {
					var el = $(document.elementFromPoint($(this).offset().left + 60, $(this).offset().top + 60)).parents('tr[object-id]');
					if ( el.length > 0 && el.attr('object-id') != wasOverElement ) {
						selectPageInTree(wasOverElement = el.attr('object-id'));
					}
				})
				.bind('wheel', function(e) {
					var offset = typeof e.originalEvent.deltaY != 'undefined'
						? e.originalEvent.deltaY : -e.originalEvent.wheelDelta;
					if ( offset < 0 ) {
						buildTopWaypoint(localOptions);
					}
					if ( offset > 0 && $('#doc-load-more a:visible').length > 0 ) {
						buildBottomWaypoint(localOptions);
					}
				});

			$(document)
				.keydown(function(e)
				{
					if ( $('body>div.ui-dialog').length > 0 ) return;
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
	}

    $('.table-master:visible').attachDragger();

	$('.plus-minus-toggle[data-toggle="collapse"]')
		.on('click', function (e) {
			localStorage.setItem($(this).attr('href'), !$(this).is('.collapsed'));
		})
		.each(function() {
			var state = localStorage.getItem($(this).attr('href'));
			if ( state == 'true' ) {
				$(this).addClass('collapsed');
				$('tbody[id='+$(this).attr('href').replace('#','')+']').collapse('toggle');
			}
		});

    scrollToPage(page_id);
}

function collapseGroups() {
	$('.plus-minus-toggle:not(.collapsed)').each(function() {
		$(this).addClass('collapsed');
		$('tbody[id='+$(this).attr('href').replace('#','')+']').collapse('toggle');
		localStorage.setItem($(this).attr('href'), true);
	});
}
function restoreGroups() {
	$('.plus-minus-toggle.collapsed').each(function() {
		$(this).removeClass('collapsed');
		$('tbody[id='+$(this).attr('href').replace('#','')+']').collapse('toggle');
		localStorage.setItem($(this).attr('href'), false);
	});
}

function getPageSelected()
{
	var selectedId = 0;
	var itemSelected = $(":focus");
	if ( itemSelected.length > 0 ) {
		selectedId = itemSelected.parents("[object-id]").attr('object-id');
	}
	return selectedId;
}
function clickDocumentRowElement( path )
{
	var selectedId = getPageSelected();
	if ( selectedId < 1 ) return;
	var documentRow = $('tr[object-id='+selectedId+']');
	if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) {
		(new CKEDITOR.focusManager(CKEDITOR.currentInstance)).blur();
	}
	var el = documentRow.find(path);
	if ( el.length < 1 ) return;
	if ( el.is('[href]') && el.attr('href') != '#' ) {
		window.location = el.attr('href');
	}
	else {
		el.click();
	}
}

function buildBottomWaypoint(options)
{
	var progressBar = $('#tablePlaceholder .table-inner tr.row-empty:last');
	$('#tablePlaceholder .table-inner>tbody>tr[object-id]').not('.row-empty').each( function()
    {
		if ( !$(this).next().is('.row-empty') ) return;

        var ids = $(this).nextAll('.row-empty').map(function() {
            return $(this).attr('object-id');
        }).get().slice(0, options.scrollable ? options.cachedPages : 1);

		openPage( ids, false, progressBar, function(pageId) {
			setTimeout( function() {
				hideProgressBar(progressBar);
				restoreCache(pageId, function() {
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
	$('#tablePlaceholder .table-inner>tbody>tr[object-id]').not('.row-empty').each( function() {
		if ( !$(this).prev().is('.row-empty') ) return;
		if ( $(this).position().top < 0 ) return;

		var ids = $(this).prevAll('.row-empty').map(function() {
            return $(this).attr('object-id');
        }).get().slice(0, options.scrollable ? options.cachedPages : 1);

		openPage( ids, false, progressBar, function(pageId) {
			setTimeout( function() {
				hideProgressBar(progressBar);
	    		restoreCache(pageId, function() {
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
	var ids = [];
    var selector = pageId == ''
        ? '#documentCache tr[object-id]'
        : '#documentCache tr[object-id="'+pageId+'"]';
	var scrollPos = $('#tablePlaceholder .table-inner:first').scrollTop();

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
		cachedItem.find('[contenteditable]:not(.cke_editable)').each(function(i) {
			try {
				var funcName = 'setup' + $(this).attr('id');
				if ( typeof window[funcName] != 'undefined' ) window[funcName]();
			}
			catch(e) {
			}
		});

		holder = $('#tablePlaceholder .table-inner:first').find(itemSelector);

		var resort = holder.length < 1
			|| holder.attr('sort-value') != cachedItem.attr('sort-value')
            || holder.attr('order') != cachedItem.attr('order');

		if ( resort && localOptions.pageOpen < 1 )
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
							return parseInt($(this).attr("sort-value")) == parseInt(cachedItem.attr("sort-value"))
								? parseInt($(this).attr("order")) <= parseInt(cachedItem.attr("order"))
								: parseInt($(this).attr("sort-value")) <= parseInt(cachedItem.attr("sort-value"));
						});
					}
					else {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return false;
							return $(this).attr("sort-value").toString() == cachedItem.attr("sort-value").toString()
								? parseInt($(this).attr("order")) <= parseInt(cachedItem.attr("order"))
								: $(this).attr("sort-value").toString() <= cachedItem.attr("sort-value").toString();
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
							return parseInt($(this).attr("sort-value")) == parseInt(cachedItem.attr("sort-value"))
								? parseInt($(this).attr("order")) >= parseInt(cachedItem.attr("order"))
								: parseInt($(this).attr("sort-value")) >= parseInt(cachedItem.attr("sort-value"));
						});
					}
					else {
						list = $(group_selector+":not(.info)").filter( function() {
                            if ( !$(this).is("[sort-value]") ) return true;
							return $(this).attr("sort-value").toString() == cachedItem.attr("sort-value").toString()
								? parseInt($(this).attr("order")) >= parseInt(cachedItem.attr("order"))
								: $(this).attr("sort-value").toString() >= cachedItem.attr("sort-value").toString();
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
                    $('#tablePlaceholder .table-inner:first tbody:first').append(cachedItem);
                }
                else {
					var rowNumSelector = 'td[name="row-num"] .lst-num';
					cachedItem.find(rowNumSelector).replaceWith(holder.find(rowNumSelector).clone());
                    holder.replaceWith(cachedItem);
                }
			}
		}
		else
		{
			// just refresh item
			var rowNumSelector = 'td[name="row-num"] .lst-num';
			cachedItem.find(rowNumSelector).replaceWith(holder.find(rowNumSelector).clone());
			holder.replaceWith(cachedItem);
		}
    });
    
    drawNumbers();

	if ( typeof callback != 'undefined' ) callback();

	$('#tablePlaceholder .table-inner:first tr[object-id]').filter(function (i,e) {
		return $.inArray($(e).attr('object-id'), ids) >= 0;
	})
	.each(function() {
        completeUIExt($(this));
	});

	$('#tablePlaceholder .table-inner:first').scrollTop(scrollPos);

	return true;
}

function drawNumbers()
{
	var firstNumber = $('#tablePlaceholder .table-inner:first td[name="row-num"] .lst-num').first().text();
	if ( firstNumber == '' ) return;
	
	$('#tablePlaceholder .table-inner:first td[name="row-num"] .lst-num').each( function(index, e) {
		$(e).html(parseInt(firstNumber,10) + index);
	});
}

function showProgressBar(el) {
	el.find('td#content').html('<div class="document-loader"></div>');
	$('#new-doc-section, #doc-load-more').hide();
}

function hideProgressBar(el) {
	el.find('td#content').html('');
}

function openPage( ids, force, progress_element, callback ) {
	if (ids.length < 1) return;

	var load_ids = [];
	$.each(ids, function (index, value) {
		if ($('#documentCache').find('tr[object-id="' + value + '"]').not('.row-empty').length > 0) return true;
		if ($('#tablePlaceholder .table-inner:first').find('tr[object-id="' + value + '"]').not('.row-empty').length > 0) return true;
		load_ids.push(value);
	});

	if (load_ids.length > 0 && load_ids[0] != ids[0] || load_ids.length < 1) {
		if (typeof callback != 'undefined') callback(ids[0]);
		callback = function (d) {};
	}
	if (load_ids.length < 1) return;

	if (localOptions.openPageRequest) {
		if ( !force ) return;
		localOptions.openPageRequest.abort();
		localOptions.openPageRequest = null;
	}

	localOptions.openPageRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&class='+localOptions.className
				+'&'+localOptions.className+'='+load_ids.join(',')+'&tableonly=true',
		async: true,
		cache: false,
		dataType: "html",
		beforeSend: function() {
			if ( load_ids.length > 0 && load_ids[0] == ids[0] ) {
				showProgressBar($(progress_element));
			}
		},
		success: function(data) {
	        var container = $('#documentCache');
            mergeRows(container, $(data));
			makeupUI(container);
			if ( typeof callback != 'undefined' ) callback(ids[0]);
		},
		complete: function(data) {
			localOptions.openPageRequest = null;
		}
	});
}

function mergeTitles( data )
{
	if ( typeof renameTreeNode == 'undefined' ) return;
	var sourceTable = data.find('.table-inner');
	$.each(sourceTable.find("tr[object-id]"), function(index, value) {
		renameTreeNode($(value).attr('object-id'), $(value).find('input[name=treeTitle]').val());
	});
}

function mergeRows( container, data )
{
    var sourceTable = data.find('.table-inner');
    var targetTable = container.find('.table-inner');

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
		var targetRow = $(value);
		var rowModified = sourceTable.find('tr[object-id="'+targetRow.attr('object-id')+'"]');
		if ( rowModified.length > 0 ) {
			targetRow.replaceWith(rowModified);
		}
	});
	$.each(sourceTable.find("tr[object-id]"), function(index, value) {
		var wasRow = targetTable.find('tr[object-id="'+$(value).attr('object-id')+'"]');
		if ( wasRow.length < 1 ) targetTable.append($(value));
	});
}

var sortNodes = function(nodes, mapper, compare) {

    var map = [],
        parentNode = $(nodes[0]).parent().get(0),
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
        parentNode.appendChild(nodes[map[i][0]]);
    }
};

function reorderSections()
{
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
	if ( !devpromOpts.windowActive ) return;

	if ( localOptions.waitRequest ) {
		localOptions.waitRequest.abort();
		localOptions.waitRequest = null;
	}

	var items = $('#tablePlaceholder .table-inner:first').find('tr[object-id]:not(.row-empty)');
	var newItems = [];

	localOptions.waitRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&wait=true',
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
	    {
			newItems = [];
			var skip = [];
			var comments_open = [];
			var html = $('<div>'+data+'</div>');
			var modifier = localOptions.modifier;

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
					if ( typeof CKEDITOR != 'undefined' ) {
						var toskip = false;
						targetRow.find('td#content div.cke_editable').each( function() {
							var editor = CKEDITOR.instances[$(this).attr('id')];
							if ( editor && editor.checkDirty() ) toskip = true;
						});
						if ( toskip ) {
							skip.push(object_id);
							return true;
						}
					}
					if ( localOptions.reorder && targetRow.attr('sort-value') != $(this).attr('sort-value') ) {
						targetRow.attr('sort-value', $(this).attr('sort-value'));
					}
					targetRow.attr('modified', $(this).attr('modified'));
                }
                else {
					if ( $(this).attr('modifier') == modifier || $(this).attr('modifier') == "" ) {
						newItems.push(object_id);
					}
					else {
						skip.push(object_id);
					}
                }

   	    	    // append group if missed
   	    	    var group_id = $(this).attr('group-id');
   	    	    if ( $('#tablePlaceholder .table-inner:first').find('tr.info[group-id="'+group_id+'"]').length < 1 ) {
   	    	    	$('#tablePlaceholder .table-inner:first').append(html.find('tr.info[group-id="'+group_id+'"]'));
   	    	    }
   	    	});

            mergeTitles(html);

			$.each( skip, function(index, object_id) {
				var row = html.find('tr[object-id="'+object_id+'"]');
				clearRow(row);
				row.remove();
			});

			var container = $('#documentCache');
            mergeRows(container, html);
	        makeupUI(container);
	    },
	    complete: function(xhr, textStatus)
	    {
			if ( newItems.length > 0 ) {
				if ( $.inArray(newItems[0], localOptions.addedQueue) >= 0 ) {
					localOptions.addedQueue = [];
					if ( localOptions.visiblePages == 1 ) {
						showSinglePage(newItems[0], true);
					} else {
						showNewRows(newItems, true);
					}
				}
				else {
					showNewRows(newItems, !localOptions.scrollable);
				}
			}

            var nativeResponse = xhr.getResponseHeader('X-Devprom-UI') == 'tableonly';
            if ( nativeResponse && xhr.responseText.indexOf('div') > -1 ) {
                setTimeout( function() {
                    restoreCache('', function() {
                        $('#tablePlaceholder .table-inner:first tr.row-empty:not([sort-value])').remove();
                        toggleBulkActions(null,1);
                    });
                    refreshListItems();
                }, 300);
			}
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
    if ( typeof deleteTreeNode != 'undefined' ) {
		deleteTreeNode(element.attr('object-id'));
	}
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
			editor.persist(false);
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
	if ( documentOptions.scrollbar ) {
		documentOptions.scrollbar.update();
	}
	reorderSections();
}

function gotoRandomPage( page_id, load_pages, use_cache )
{
	if ( editorFocused() ) return;

	if ( use_cache && $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]').not('.row-empty').length > 0 ) {
		scrollToPage(page_id);
		return;
	}

	clearDocument();

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
	openPage( ids, true, $('#tablePlaceholder .table-inner:first tr[object-id="'+ids[0]+'"]'), function(pageId) {
		restoreCache(pageId, function() {
			setRowFocus(pageId);
			$.each(nextIds.slice(0,load_pages), function(index,value) {
                restoreCache(value, function() {});
            });
			scrollToPage(pageId);
        });
	});
}

function selectPageInTree( page_id )
{
	if ( typeof page_id == 'undefined' ) return;
    if ( $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]').length < 1 ) return;
	if ( typeof activateTreeNode != 'undefined' ) {
		activateTreeNode(page_id);
	}
    var captionElement = $("[id*=WikiPageCaption][objectid='"+page_id+"']");
    if ( captionElement.text() != '' ) {
        updateHistory(captionElement.text(), page_id);
    }
}

function scrollToPage( page_id )
{
	if ( typeof page_id == 'undefined' ) return;

	var item = $('#tablePlaceholder .table-inner:first tr[object-id="'+page_id+'"]');
    if ( item.length < 1 ) return;
    if ( item.height() > $(window).height() && item.isInViewport() ) return;

    var pos = item.offset().top - 95 - $('#tablePlaceholder .table-inner:first th[uid=content]').height();
	if ( $('.documentToolbar').length > 0 ) {
		pos -= $('.documentToolbar').height();
	}
	$('.table-inner-div').scrollTop($('.table-inner-div').scrollTop() + pos);
}

function showSinglePage( pageId, selectRow )
{
	if ( typeof loadContentTree != 'undefined' ) {
		loadContentTree( function() {
			if ( selectRow ) {
				gotoRandomPage(pageId, 4, true);
			}
		});
	}
}

function showCreatedPage( pageId )
{
	if ( typeof loadContentTree != 'undefined' ) {
		loadContentTree( function() {
			gotoRandomPage(pageId, 4, true);
		});
	}
}

function showNewRows( rows, selectRow ) {
	if ( selectRow ) {
		restoreCache(rows[0], function() {
			setRowFocus(rows[0]);
		});
	}
}

function makeupUI( container )
{
	hasCheckboxes = $('#tablePlaceholder .table-inner:first th.visible[uid=checkbox]').length > 0;
	if ( hasCheckboxes ) {
		container.find("tr:not(.info)").each( function(i, e) {
			$(e).find('td[uid=checkbox]').removeClass('hidden');
		});
	}
	else {
		container.find("tr:not(.info)").each( function(i, e) {
			$(e).find('td[uid=checkbox]').addClass('hidden');
		});
	}

	container.find('input[type="checkbox"][name*="to_delete"]:not(.makeup-armed)').change(function(e){
        toggleBulkActions(e, 1);
    }).addClass('makeup-armed');

	container.find("tr:not(.info):not(.header-row):not(.makeup-armed)").each( function(i, e) {
		$(e).dblclick( function(evt) {
			if ( $(evt.target).closest('td[uid="checkbox"],td#operations,div.wysiwyg').length > 0 ) return;
			var ref = $(this).find('td[id=operations] a#modify');
			if (ref.is('[onclick]')) {
				ref.click();
			} else if (ref.is('[href]')) {
				window.location = ref.attr('href');
			}
		}).click( function(e) {
			var id = $(this).attr('object-id');
			if ( id != '' ) {
				$(document).trigger("trackerItemSelected", [id, e.ctrlKey || e.metaKey, $(this).attr('object-class')]);
			}
		}).addClass('makeup-armed');
	});
	
	container.find("tr:not(.info) td:not(#content):not(.makeup-armed)").dblclick( function(e) {
		if ( $(this).find('.wysiwyg').length > 0 ) return;
		if (window.getSelection)
	        window.getSelection().removeAllRanges();
	    else if (document.selection)
	        document.selection.empty();
	}).addClass('makeup-armed');

	container.find("table.table-document td#content:not(.hover-armed)").hover(
			function() {
				if ( $(this).find('.document-page-bottom .editor-area').length < 1 ) {
					var el = $(this).find('.document-item-bottom-hidden');
                    el.addClass('hover').show();
				}
				if ( $(this).parents('tr').first().is(':not(.row-empty)')) {
					selectPageInTree($(this).parents('tr').first().attr('object-id'));
				}
			},
			function() {
				if ( $(this).find('[contenteditable]:focus').length > 0 ) return;
				var el = $(this).find('.document-item-bottom-hidden');
                el.removeClass('hover').hide();
			}
		).addClass('hover-armed');

	container.find("td#content div[contenteditable]:not(.focus-armed)")
		.focus( function() {
			$(this).parents('td#content').find('.document-item-bottom-hidden').show();
			if ( $(this).parents('tr').first().is(':not(.row-empty)')) {
				selectPageInTree($(this).parents('tr').first().attr('object-id'));
			}
		})
		.focusout( function() {
			var parentNode = $(this).parents('td#content');
            if ( parentNode.find('.document-item-bottom-hidden.hover').length > 0 ) return;
            parentNode.find('.document-item-bottom-hidden').hide();
		})
		.addClass('focus-armed');

	if ( documentOptions.draggable ) {
        makeUpDraggable(documentOptions);
    }
}

function openCreatedPage( id ) {
	updateHistory("", id);
	window.location.reload();
}

function setRowFocus( page_id )
{
	var captionElement = $("[id*=WikiPageCaption][objectid='"+page_id+"']");
	if ( captionElement.text() != '' ) {
		updateHistory(captionElement.text(), page_id);
	}

	if ( typeof CKEDITOR == 'undefined' ) return;
	if ( captionElement.text().indexOf('<') >= 0 && captionElement.text().indexOf('>') >= 0 ) {
		var editor = CKEDITOR.instances[captionElement.attr("id")];
        if ( editor ) {
            (new CKEDITOR.focusManager(editor)).focus();
        }
        captionElement.focus();
		return;
	}
	var contentElement = $("[id*=WikiPageContent][contenteditable][objectid='"+page_id+"']");
	if ( contentElement.length > 0 ) {
		var editor = CKEDITOR.instances[contentElement.attr("id")];
		if ( editor ) {
			(new CKEDITOR.focusManager(editor)).focus();
        }
        contentElement.focus();
		return;
	}
	var captionElement = $("[id*=WikiPageCaption][objectid='"+page_id+"']");
	var editor = CKEDITOR.instances[captionElement.attr("id")];
    if ( editor ) {
        (new CKEDITOR.focusManager(editor)).focus();
    }
    captionElement.focus();
}

function updateHistory( pageTitle, pageId )
{
	if ( typeof window.history == 'undefined' ) return;
	var location = window.location.href;
	location = location.replace(new RegExp('page=[0-9]+', 'i'), 'page=' + pageId);
	if ( location.indexOf('page=') < 0 ) location += '&page=' + pageId;
	window.history.replaceState({}, pageTitle, location);
	filterLocation.location = location;
}

function toggleDocumentStructure()
{
    if ( $('div.wiki-page-tree').is(':visible') ) {
		$('div.wiki-page-tree').hide();
	}
    else {
		$('div.wiki-page-tree').show();
	}
	$('.treeview-push').toggleClass('invisible');

	cookies.set('toggle-docstruct', $('.treeview-push').is('.invisible'));
}

function editorFocused() {
	return CKEDITOR.currentInstance != null
		&& document.activeElement != null
		&& $(CKEDITOR.currentInstance.editable()).attr('id')
		&& $(CKEDITOR.currentInstance.editable()).attr('id') == document.activeElement.getAttribute('id');
}

var documentDraggingOptions = {
    itemCSSPath: "tr[object-id]",
    cellCSSPath: "tr[object-id]",
    hoverClass: "doc-row-hover",
    revert: "invalid",
    revertDuration: 100,
    cursorAt: {
    	top: 15,
		left: 15
	},
    helper: function( e ) {
    	if ( e ) {
    		var uid = $(e.currentTarget).find('td[id="uid"]').html();
    		if ( !uid ) uid = "";
            var caption = $(e.currentTarget).find('td[id="caption"]').html();
            if ( !caption ) caption = "";
            if ( uid + caption == "" ) return "clone";

            return $( "<div class='ui-dragging-body'>" + uid + caption + "</div>" );
		}
		else {
            return "clone";
		}
	},
    cursor: "move",
    getMethodAttributes: function ( item, cell )
    {
        var methods = [];
        var dataObject = {
            'object': item.attr("object-id"),
            'class': item.attr("object-class")
        };

        var sourceIndex = parseInt(item.attr("order"));
		if ( isNaN(sourceIndex) ) sourceIndex = 1;

        var targetIndex = parseInt(cell.attr("order"));
        if ( isNaN(targetIndex) ) targetIndex = 0;

		var tobe_seq = targetIndex == sourceIndex
			? Math.max(targetIndex - 1, 1) : targetIndex + 1;

		dataObject.attribute = 'OrderNum';
		dataObject.value = Math.max(0, parseInt(tobe_seq));

        if( jQuery.trim(item.attr("group-id")) != jQuery.trim(cell.attr("group-id")) ) {
            dataObject.attribute = this.groupAttribute;
            dataObject.value = jQuery.trim(cell.attr("group-id"));
        }

        var controllerUrl = cell.is(':not([project=""])') ? '/pm/'+cell.attr('project')+'/' : '';
        var url = controllerUrl + 'methods.php?method=modifyattributewebmethod';
        methods.push({url: url, data: dataObject});

        return methods;
    },
    appendTo: "parent",
    cancel: 'td[id]',
    drag: function( event, ui ) {
        if ( event.ctrlKey || event.metaKey ) return false;
        if ( $('#modal-form').length > 0 ) return false;
    },
    start: function ( event, ui ) {
        if ( event.ctrlKey || event.metaKey ) return;
        $('.popover.in-focus').toggleClass('in').remove();
        toggleBulkActions(event);
        $(this).fadeTo('fast', 0.2);
    },
    stop: function ( event, ui ) {
        if ( ui.helper.is(':visible') ) {
            $(this).fadeTo('fast', 1);
        }
    },
    groupAttribute: ''
};

function makeUpDraggable(options)
{
    var draggingOptions = documentDraggingOptions;
    draggingOptions.groupAttribute = options.groupAttribute;

    $(draggingOptions.itemCSSPath).not('.ui-draggable').draggable(draggingOptions);
    $(draggingOptions.cellCSSPath).not('.ui-droppable').droppable({
        hoverClass: draggingOptions.hoverClass,
        drop: function( event, ui )
        {
            if ( $('#modal-form').length > 0 ) return false;

            var item = ui.draggable;
            var cell = $(this);
            ui.helper.hide();

            if ( item.attr("object-id") == cell.attr("object-id") ) return false;

            var methods = draggingOptions.getMethodAttributes( item, cell );
            if ( methods.length < 1 ) return false;

            processDocumentActions(methods, item, draggingOptions);
        }
    });
}

function processDocumentActions( methods, item, options )
{
    if ( methods.length < 1 ) return;
    var method = methods.shift();
    runMethod(
        method.url,
        method.data,
        function ( result ) {
            processDocumentActions(methods, item, options);
        },
        '',
        true
    );
}

function gotoPageJson( jsonText ) {
    try {
        resultObject = jQuery.parseJSON(jsonText);
        showSinglePage(resultObject.Id,true);
    }
    catch( e ) {
    }
}

function onPageAdded(jsonText) {
	try {
		if ( jQuery.isNumeric(jsonText) ) {
			showSinglePage(jsonText,true);
			return;
		}
		gotoPageJson( jsonText );
	}
	catch( e ) {
	}
}
