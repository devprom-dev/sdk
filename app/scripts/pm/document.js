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
		visiblePages: 20,
		scrollable: true,
		reorder: true
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
					if ( $('.table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(6) ) {
							buildTopWaypoint(options);
						}
					}
				}
				if ( e.originalEvent.detail > 0 ) {
					if ( $('.table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(options);
					}
				}
			})
			.bind('mousewheel', function(e) {
				var offset = typeof e.originalEvent.deltaY != 'undefined' 
						? e.originalEvent.deltaY : -e.originalEvent.wheelDelta; 
				if ( offset < 0 ) {
					if ( $('.table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(6) ) {
							buildTopWaypoint(options);
						}
					}
				}
				if ( offset > 0 ) {
					if ( $('.table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(options);
					}
				}
			})
			.bind('touchmove', function(e) {
				if ( localOptions.scrollLastPos < 0 ) {
					localOptions.scrollLastPos = e.originalEvent.touches[0].pageY;
					return;
				}
				if ( e.originalEvent.touches[0].pageY > localOptions.scrollLastPos ) {
					if ( $('.table-inner tr[object-id]:first').hasClass('row-empty') ) {
						if ( localOptions.vscrollAllowed(3) ) {
							buildTopWaypoint(options);
						}
					}
					localOptions.scrollLastPos = -1;
				}
				if ( e.originalEvent.touches[0].pageY < localOptions.scrollLastPos ) {
					if ( $('.table-inner tr[object-id]:last').hasClass('row-empty') ) {
						buildBottomWaypoint(options);
					}
					localOptions.scrollLastPos = -1;
				}
			})
			.keydown(function(e)
			{
				if ( $(e.target).is('.cke_editable') ) return;
			    switch(e.which) {
			        case 38: // up
			        	buildTopWaypoint(options);
			        	break;
		
			        case 40: // down
			        	buildBottomWaypoint(options);
			        	break;
		
			        default: 
			        	return;
			    }
			});
	}
	
	$(document).scroll( function(event)
	{
		if ( !$('#wikitree').is(':visible') ) return;
		
		scrollPos = $(this).scrollTop();
		
		treePos = $('.wikitreesection').position().top;
		
		if ( scrollPos >= 51 && scrollPos > treePos + 15 )
		{
			if ( $('#wikitree').css('position') != 'fixed' )
			{
				$('#treeview-hints').hide();
				
				//if ( $('.content-internal').height() > $(window).height() * 1.5 )
				{
				}
				
				$('.content-internal').css('min-height', $(window).height() * 1.1);

				$('footer').hide();
				$('.content-internal').addClass('content-internal-fullpage');
				$('body').addClass('fullpage');

				$('#wikitree').css({
					position: 'fixed',
					top: 0,
					width: $('.wikitreesection').width(),
					'overflow-y': 'auto',
					'overflow-x': 'hidden',
					height: $(window).height()
				});
			}
		}
		else if ( $('#wikitree').css('position') != 'relative' )
		{
			$('#wikitree').css({
				position: 'relative',
				height: 'auto',
				top: 'auto',
				'overflow-y': 'hidden',
				'overflow-x': 'hidden'
			});

			$('footer').show();
			$('.content-internal').removeClass('content-internal-fullpage');
			$('body').removeClass('fullpage');
			
			
			$('#treeview-hints').show();
		}
	});
	
	makeupUI($('.table-inner:first'));
}

function getDocumentContentHeight()
{
	return $('section.content-internal').height() - $(document).scrollTop() + $('section.content-internal').position().top + 10;
}

function buildBottomWaypoint(options)
{
	if ( $(document).scrollTop() + $(window).height() < $('.table-inner').height() - 100 ) return;
	
	var progressBar = $('.table-inner tr.row-empty:last');
	 
	if ( progressBar.find('td#content').html() != '' ) return;
	
	$('.table-inner>tbody>tr[object-id]').not('.row-empty').each( function() 
    {
		if ( !$(this).next().is('.row-empty') ) return;
		
		var ids = [$(this).next('.row-empty').attr('object-id')];

		openPage( ids, progressBar, function() 
    	{
			setTimeout( function() 
			{
				restoreCache(function() {
					progressBar.find('td#content').html('');
        	    });
				
	    		$('.table-inner:first').find('tr[object-id="'+ids[0]+'"]')
	    			.prevAll('tr[object-id]').slice(localOptions.visiblePages).each(function() { clearRow($(this)); });
    		}, 
    		400 );
    	});
	});
}

function buildTopWaypoint(options)
{
	if ( $(document).scrollTop() > 49 ) return;
	
	var progressBar = $('.table-inner tr.row-empty:first');
	 
	if ( progressBar.find('td#content').html() != '' ) return;
	
	$('.table-inner>tbody>tr[object-id]').not('.row-empty').each( function() 
	{
		if ( !$(this).prev().is('.row-empty') ) return;
		
		var ids = [$(this).prev('.row-empty').attr('object-id')];

		var pageId = ids[0];
		
		openPage( ids, progressBar, function()
		{
			setTimeout( function() 
    		{
	    		restoreCache(function() {
					progressBar.find('td#content').html('');
	    		});
	    		
	    		$('.table-inner:first').find('tr[object-id="'+pageId+'"]')
	    			.nextAll('tr[object-id]').slice(localOptions.visiblePages).each(function() { clearRow($(this)); });
			}, 
			400 );
		});

		return false;
	});
}

function restoreCache( callback )
{
	ids = [];
	
	$('#documentCache tr[object-id]').each(function(i, cachedItem)
	{
		cachedItem = $(cachedItem);
		
		ids.push(cachedItem.attr('object-id'));

		itemSelector = 'tr[object-id="'+cachedItem.attr('object-id')+'"]';
	
		// trying to pick up given item
		var holder = $('.table-inner:first').find(itemSelector);
	
		var group_id = cachedItem.attr('group-id');
	
		if ( typeof group_id != 'undefined' )
		{
			// if group is defined and it is different than current then drop the item
			if ( holder.length > 0 && holder.attr("group-id") != group_id )
			{
				holder.remove();
			}
			
			// trying to pick up item with the given group
			itemSelector += '[group-id="'+group_id+'"]'; 
		}
		
		holder = $('.table-inner:first').find(itemSelector);
		
		if ( holder.length < 1 || holder.attr('sort-value') != cachedItem.attr('sort-value') )
		{
			// if there is no item then create new one
			$('.table-inner tr#no-elements-row').remove();
			
			var group_selector = '.table-inner tbody tr[group-id="'+group_id+'"]';
			
			if ( typeof group_id != 'undefined' && $(group_selector).length > 0 )
			{
				if ( cachedItem.attr("sort-value") == "" )
				{
					// put it at the end of the group
					$(group_selector+":last").after(cachedItem);
				}
				else if( cachedItem.attr("sort-type") == "desc" )
				{
					var list = $(group_selector).filter( function() {
						return parseInt($(this).attr("sort-value")) <= parseInt(cachedItem.attr("sort-value"));
					});
					
					list.length < 1 ? $(group_selector+":first").before(cachedItem) : list.first().before(cachedItem);
				}
				else
				{
					var list = $(group_selector).filter( function() {
						return parseInt($(this).attr("sort-value")) >= parseInt(cachedItem.attr("sort-value"));
					});
					
					list.length < 1 ? $(group_selector+":last").after(cachedItem) : list.first().before(cachedItem);  
				}
			}
			else
			{
				// put it in the end of the table
				$('.table-inner tbody').append(cachedItem);
			}
			holder.remove();
		}
		else
		{
			// just refresh item
			holder.height(cachedItem.height());
			holder.replaceWith(cachedItem.hide());
		}

	    cachedItem.find('div[attributename="Caption"]').each( function() {
	    	$('.treeview-label[object-id="'+cachedItem.attr('object-id')+'"] span.title').text($(this).text());
	    });
	});
    
    drawNumbers();
    
	if ( typeof callback != 'undefined' ) callback();

	$('.table-inner:first tr[object-id]').filter(function (i,e) {
		return $.inArray($(e).attr('object-id'), ids) >= 0;
	})
	.fadeTo(300, 1, function() {
	});

	refreshListItems();
    
	return true;
}

function drawNumbers()
{
	var firstNumber = $('.table-inner:first td[name="row-num"]').first().text();
	
	if ( firstNumber == '' ) return;
	
	$('.table-inner:first td[name="row-num"]').each( function(index, e) {
		$(e).html(parseInt(firstNumber,10) + index);
	});
}

function openPage( ids, progress_element, callback )
{
	if ( ids.length < 1 ) return;
	
	$(progress_element).find('td#content').html('<div class="document-loader"></div>');

	if ( $('#documentCache').find('tr[object-id="'+ids[0]+'"]').length > 0 )
	{
        if ( typeof callback != 'undefined' ) callback();
	}
	
	load_ids = [];
	
	$.each( ids, function(index, value) 
	{
		if ( $('#documentCache').find('tr[object-id="'+value+'"]').length > 0 ) return true;
		
		if ( $('.table-inner').find('tr.loading[object-id="'+value+'"]').length > 0 ) return true;

		if ( $('.table-inner').find('tr[object-id="'+value+'"]').not('.row-empty').length > 0 ) return true;
		
		$('.table-inner').find('tr[object-id="'+value+'"]').addClass('loading');
		
		load_ids.push(value);
	});

	if ( load_ids.length < 1 ) return;
	
	//if ( $.active > 1 ) return;
	
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
        	
	        imagesLoaded(container, function(instance) 
			{
				$(data).filter('script').each( function() 
	        	{
		        	eval($(this).html());
		        });
				
				if ( typeof callback != 'undefined' ) callback();
	        });

	        container.append($(data).find('.table-inner').attr("class",""));

	        makeupUI(container);
	        
	        completeUIExt(container);
		}
	});
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
	console.log('reordering sections');
	
    sortNodes($('.table-inner:first').find('tr[object-id]'),
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
	var items = $('.table-inner:first').find('tr[object-id]:not(.row-empty)');
	
	var ids = [items.first().attr('object-id'), items.last().attr('object-id')];
	
	if ( localOptions.waitRequest )
	{
		localOptions.waitRequest.abort();
		localOptions.waitRequest = null;
	}
	
	localOptions.waitRequest = $.ajax({
		type: "GET",
		url: filterLocation.locationTableOnly()+'&wait=true&doc-visible-ids='+ids.join(','),
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) 
	    {
			var changed = new Array();
			
			var skip = new Array();
			
			var comments_open = new Array();
			
			var html = $(data);
			
			html.find(".object-changed[object-id]").each( function(index, value) 
			{
				itemSelector = 'tr[object-id="'+$(this).attr('object-id')+'"]';
					
				if ( html.find(itemSelector).length < 1 && $(itemSelector).length > 0 )
				{
					deleteRow($(itemSelector));
				}
			});

			html.find('tr[object-id]').each( function()
    	    {
				var object_id = $(this).attr('object-id');
				
			    $(this).find('div[attributename="Caption"]').each( function() {
			    	$('.treeview-label[object-id="'+object_id+'"] span.title').text($(this).text());
			    });
				
    	        var targetRow = $('.table-inner:first').find('tr[object-id="'+object_id+'"]');

    	        if ( $(this).attr('modified') <= targetRow.attr('modified') )
	        	{
    	        	skip.push(object_id);
    	        	return true;
	        	}
    	        
    	        if ( targetRow.hasClass('row-empty') )
    	        {
    	        	$.each( $(this).prop("attributes"), function() {
    	        		if ( this.name == 'class' ) return true;
    	        		targetRow.attr(this.name, this.value);
    	        	});
    	        }
    	        
    	        if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null )
    	        {
    	        	if ( CKEDITOR.currentInstance.name == targetRow.find('td#content div.cke_editable[attributename="Content"]').attr('id') )
    	        	{
    	        		skip.push(object_id);
        	        	return true;
    	        	}
    	        }

    	        if ( targetRow.find('.document-item-bottom').length > 0 ) comments_open.push(object_id);
    	        
   	    	    clearRow(targetRow);
   	    	    
   	    	    changed.push(object_id);
   	    	    
   	    	    // append group if missed
   	    	    var group_id = $(this).attr('group-id');
   	    	    
   	    	    if ( $('.table-inner:first').find('tr.info[group-id="'+group_id+'"]').length < 1 )
   	    	    {
   	    	    	$('.table-inner:first').append(html.find('tr.info[group-id="'+group_id+'"]'));
   	    	    }
   	    	});
	        
	        var container = $('#documentCache');

	        container.append(html.find('.table-inner').attr("class",""));

	        makeupUI(container);
	        
	        completeUIExt(container);

	        html.filter('script').each( function() 
        	{
	        	eval($(this).html());
	        });

			$.each( skip, function(index, object_id) 
    	    {
				var row = container.find('tr[object-id="'+object_id+'"]');
				
				clearRow(row);
				row.remove();
   	    	});
	        
			$.each( comments_open, function(index, object_id) 
		    {
				var row = container.find('tr[object-id="'+object_id+'"]');
				
				row.find('.document-item-bottom-hidden').each(function(item) {
					$(item).removeClass('document-item-bottom-hidden');
				});
		    });
	    },
	    complete: function(xhr, textStatus)
	    {
	    	if ( textStatus == "abort" ) return;
	    	if ( xhr.responseText == "" ) return;
	    	
    		setTimeout( function() {
    			restoreCache(function() {
    				if ( localOptions.reorder ) reorderSections();
    			});
    		}, $.inArray(textStatus, ["error","timeout","parsererror"]) < 0 ? 1 : 180000);
	    }
	});		
}	

function deleteRow( element )
{
	var group_id = element.attr("group-id");

	clearRow(element);
	
	if ($('tr[group-id="'+group_id+'"]').length < 3 ) $('tr.info[group-id="'+group_id+'"]').remove(); 
	
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

function gotoRandomPage( page_id, load_pages, use_cache )
{
	if ( use_cache && $('tr[object-id='+page_id+']').length > 0 && !$('tr[object-id='+page_id+']').hasClass('row-empty') )
	{
		scrollToPage( page_id );
		selectPageInTree( page_id );
		CKEDITOR.instances[$('[id*=WikiPageContent][objectid='+page_id+']').attr("id")].focus();

		return;
	}
	
	if ( $.active > 1 ) return;

	beforeUnload();
	
	$('tr[object-id]').each( function() { clearRow($(this)); });

	scrollToPage(page_id);

	selectPageInTree( page_id );
	
	var ids = [page_id.toString()];
	
	$('tr[object-id='+page_id+']').nextAll().slice(0,localOptions.scrollable ? (typeof load_pages != 'undefined' ? (load_pages - 1) : 2) : 0).each( function() {
		ids.push($(this).attr('object-id'));
	});

	openPage( ids, $('tr[object-id='+page_id+']'), function()
	{
	    restoreCache(function() {
	    });
	});
}

function selectPageInTree( page_id )
{
	if ( typeof page_id == 'undefined' ) return;

    if ( $('tr[object-id="'+page_id+'"]').length < 1 ) return;

	$('li.treeview > div > a > span.label').removeClass('label');
	
	$('li.treeview[id="'+page_id+'"]').parents('li').each(function() {
		$(this).children('div.expandable-hitarea').first().click();
	});
	
	$('li.treeview[id="'+page_id+'"] > div > a > span').addClass('label');
}

function scrollToPage( page_id, timeout )
{
	if ( typeof page_id == 'undefined' ) return;

    if ( $('tr[object-id="'+page_id+'"]').length < 1 ) return;

	pos = $('tr[object-id="'+page_id+'"]').offset().top;

	if ( pos > $(document).scrollTop() && pos < ($(window).height() - 80) ) return;
	
	$('body, html').animate({ scrollTop: pos }, 50);
}

function makeupUI( container )
{
	hasCheckboxes = $('.table-inner th.visible[uid=checkbox]').length > 0;

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
		$(e).dblclick( function() {
			$(this).find('td[id=operations] ul li:first a').click();
		});
	});
	
	container.find("tr:not(.info) td").dblclick( function(e) {
		if (window.getSelection)
	        window.getSelection().removeAllRanges();
	    else if (document.selection)
	        document.selection.empty();
	});
	
	container.find("td#content").hover(
			function() {
				$(this).find('.document-item-bottom-hidden').show();
				
				if ( $(this).parent().is(':not(.row-empty)')) {
					selectPageInTree($(this).parent().attr('object-id'));
				}
			},
			function() {
				$(this).find('.document-item-bottom-hidden').hide();
			}
		);
	
	container.find('.dropdown-comments').not('.properties-armed').click( function(event) 
	{ 
		event.preventDefault();
		event.stopImmediatePropagation();

		$('.actions-button').not(this).parent().removeClass('open');
		
		$(this).addClass('properties-armed'); 

		$(this).next('.comments-section').toggle();
		$(this).toggle();
	});

	var locstr = new String(window.location);
	if ( locstr.indexOf('#comment') > 0 )
	{
		var commentString = locstr.substring(locstr.indexOf('#comment'));
		var parts = commentString.split('#');
		
		if ( parts.length > 0 ) {
			container.find('#'+parts[1]).parents('.comments-section').prev('.dropdown-comments').click();
		}
	}
}