var draggableOptions = {
		itemCSSPath: ".board_item",
		cellCSSPath: ".list_cell,.board_item_separator",
		hoverClass: "list_cell_hover",
		revert: "invalid",
		revertDuration: 100,
		helper: "clone",
		cursor: "move",
		redrawBoardItemCustom: function ( item, result )
		{
			item.each(function() {
				var objectid = $(this).attr('object');

				var newCard = $(result).find('.board_item[object="'+objectid+'"]');
				var oldCard = $('.board_item[object="'+objectid+'"]');

				new_more = newCard.attr('more');
				new_group = newCard.parent().attr('group');
				old_more = oldCard.attr('more');
				old_group = oldCard.parent('.list_cell').attr('group');

				if( $('.list_cell[more="'+new_more+'"][group="'+new_group+'"]').length < 1 ) {
					setTimeout(function() { window.location.reload(); }, 200);
					return;
				}

				var itemFound = false;
				$('.list_cell[more="'+new_more+'"][group="'+new_group+'"][sort="OrderNum"] > .board_item')
					.each(function() {
						if ( parseInt($(this).attr('order')) >= parseInt(newCard.attr('order')) )
						{
							newCard.insertBefore($(this));
							oldCard.remove();
							
							itemFound = true; 
							
							return false;
						}
					});
				
				if ( !itemFound )
				{
					var cell = $('.list_cell[more="'+new_more+'"][group="'+new_group+'"]');
					
					if ( cell.length < 1 ) return;
					
					if ( cell.attr('sort') == 'OrderNum' )
					{
						oldCard.remove();
						
						newCard.appendTo(cell);
					} 
					else
					{
						if ( oldCard.length > 0 && new_more == old_more && new_group == old_group )
						{
							oldCard.replaceWith(newCard);
						}
						else
						{
							oldCard.remove();
							
							newCard.prependTo(cell);
						}
					}
				}

				newCard.show().fadeTo('fast', 1);
			});
		},
		initializeBoardItemCustom: function ( item, options ) {},
		droppableAcceptFunction: function ( draggable ) {},
		getMethodAttributes: function ( item, cell ) 
		{
			var controllerUrl = item.attr("project") != '' ? '/pm/'+item.attr("project")+'/' : '';
			var methods = [];
			var dataObject = {
				'object': item.attr("object"),
				'class': this.className
			};
			var url = '';

			if( parseInt(cell.attr("order")) >= 0 )
			{
				var tobe_seq = parseInt(cell.attr("order")) == parseInt(item.attr("order"))
					? Math.max(parseInt(cell.attr("order")) - 1, 0) : parseInt(cell.attr("order"));

				if ( parseInt(item.attr("order")) != tobe_seq ) {
					dataObject.attribute = 'OrderNum';
					dataObject.value = tobe_seq;
					url = controllerUrl+'methods.php?method=modifyattributewebmethod';
				}
			}

			if( jQuery.trim(item.attr("group")) != jQuery.trim(cell.attr("group")) )
			{
				dataObject.attribute = this.groupAttribute;
				dataObject.value = jQuery.trim(cell.attr("group"));
				url = controllerUrl+'methods.php?method=modifyattributewebmethod';
			}

			if( jQuery.trim(item.attr("more")) != jQuery.trim(cell.attr("more")) )
			{
				controllerUrl = cell.is('[project]') ? '/pm/'+cell.attr('project')+'/' : controllerUrl;
				if ( this.boardAttribute == "State" ) {
					url = controllerUrl+'methods.php?method=modifystatewebmethod';
					dataObject.source = jQuery.trim(item.attr("more"));
					dataObject.target = jQuery.trim(cell.attr("more"));
				}
				else {
					url = controllerUrl+'methods.php?method=modifyattributewebmethod';
					dataObject.attribute = this.boardAttribute;
					dataObject.value = jQuery.trim(cell.attr("more"));
					if ( this.groupAttribute != '' ) {
						dataObject.parms = {};
						dataObject.parms[this.groupAttribute] = jQuery.trim(cell.attr("group"));
					}
				}
			}
			if ( url != '' ) {
				methods.push({url: url, data: dataObject});
			}
			return methods;
		},
		afterItemModified: function( item, options )
		{
			//redrawBoardChanges( options );
		},
		initializeBoardItem: function( items, options )
		{
			initializeBoardItem( items, options );
		},
		getItemWidth: function (columns, offset)
		{
			columnWidth = Math.round(($('.board-table').width() - offset) / columns);
			itemsInColumn = (Math.floor(columnWidth / (130)));
			
			return itemsInColumn <= 1 
				? Math.min(Math.max(columnWidth + 15, 120), 175) 
				: Math.max((columnWidth / itemsInColumn - itemsInColumn * 10), 130);
		},
		redrawItemUrl: '',
		stack: ".board_item",
		appendTo: "parent",
		itemWidth: 999,
		recentlyChangedTime: '',
		drag: function( event, ui ) {
			if ( event.ctrlKey || event.metaKey ) return false;
			if ( $('div[id*="context-menu"]>ul:visible').length > 0 ) return false;
		},
		start: function ( event, ui ) {
			if ( event.ctrlKey || event.metaKey ) return;
			if ( $('div[id*="context-menu"]>ul:visible').length > 0 ) return;
			$('.popover.in-focus').toggleClass('in').remove();
			$('.board_item_body input[type=checkbox]').removeAttr('checked');
			toggleBulkActions();
			$(this).fadeTo('fast', 0.2);
		},
		stop: function ( event, ui ) {
			if ( ui.helper.is(':visible') ) {
				$(this).fadeTo('fast', 1);
			}
		},
		className: '',
		classUserName: '',
		groupAttribute: '',
		boardAttribute: 'State',
		boardCreated: '',
		itemFormUrl: '',
		resetParms: '&view=board&rows=all&offset2=0',
		waitRequest: null,
		resizeCards: function () 
		{
			var width = this.getItemWidth();
			$('.list_cell:not(.board-size-mic) '+this.itemCSSPath + ' div.bi-cap').width(width - 10);
			$('.list_cell:not(.board-size-mic) '+this.itemCSSPath).width(width);
			$('.list_cell.board-size-mic '+this.itemCSSPath).css('width', 'auto');
		},
		sliderTitle: ''
	};

function board( options ) 
{
	var defaultBoardSize = (cookies.get('board-slider-pos') != null 
			? cookies.get('board-slider-pos')
			: ($(window).width() <= 1024 ? 1 : 2));
	setBoardSize(options,defaultBoardSize);
	
	boardMake( options );

	$('<div id="board-slider"></div>').insertAfter('.bulk-filter-actions');
	$('#board-slider').slider({
	      value: defaultBoardSize,
	      min: 0,
	      max: 4,
	      step: 1,
	      slide: function( event, ui ) {
	    	  setBoardSize(options,ui.value);
	      }
	}).attr('title', options.sliderTitle);

	$('a.collapse-cards').click(function() {
		var cell = $('.list_cell[more="'+$(this).attr('more')+'"][group="'+$(this).attr('group')+'"]');
		if ( cell.length > 0 ) {
			var cookieId = $(this).parents('.board-table').attr('id') + "[size/"
				+ $(this).attr('more').trim() + "/" + $(this).attr('group').trim() + "]";
			if ( cell.hasClass('board-size-mic') ) {
				cell.removeClass('board-size-mic');
				cookies.set(cookieId, null);
			}
			else {
				cell.addClass('board-size-mic');
				cookies.set(cookieId, "board-size-mic");
			}
			options.resizeCards();
		}
	});
	$('a[id=collapse-cards]').click(function() {
		var state = $(this).attr('alt');
		var reset = $(this).parents('th').hasClass('board-size-mic');
		$('.list_cell[more=" '+state+'"]').each(function() {
			if ( reset ) {
				$(this).removeClass('board-size-mic');
			}
			else {
				$(this).addClass('board-size-mic');
			}
		});
		var cookieId = $(this).parents('.board-table').attr('id') + "[column/" + state.trim() + "]";
		if ( reset ) {
			$(this).parents('th').removeClass('board-size-mic');
			cookies.set(cookieId, null);
		}
		else {
			$(this).parents('th').addClass('board-size-mic');
			cookies.set(cookieId, "board-size-mic");
		}
		var title = $(this).text();
		$(this).text($(this).attr('class'));
		$(this).attr('class',title);
		options.resizeCards();
		var regCells = $('.list_header:not(.board-size-mic)');
		$('.list_header:not(.board-size-mic)').attr('width',(100 / regCells.length) + '%');
		$('.list_header.board-size-mic').attr('width','auto');
		$('.dropdown .dropdown-toggle').dropdown('toggle');
	});

	setTimeout( function () { redrawBoardChanges(options); }, 500 );
}

function boardMake( options )
{
	options.resizeCards();
	$(options.itemCSSPath).draggable(options).css('position', '');

	$(options.cellCSSPath).droppable({
		hoverClass: options.hoverClass,
		accept: options.droppableAcceptFunction,
		drop: function( event, ui ) 
		{
			var item = ui.draggable;
			ui.helper.hide();
			
			var cell = $(this).is('.board-column') ? $(this).children('.list_cell') : $(this);
			var methods = options.getMethodAttributes( item, cell );
			
			if ( item.attr("object") == "" )
			{
				$.each(methods, function(index,method) {
					createBoardItem( item.attr("createItemURIParms"), options, method.data, function( objectid, options ) {
						item.attr('object', objectid);
						item.attr('lifecycle', 'created');
		
						redrawBoardItem( objectid, options );
						item.attr('object', "");
					});
				});
			}
			else
			{
				processBoardActions(methods,item,options);
			}
		}
	});
	
	$(".board_item_separator").droppable( "option", "tolerance", "touch" );

	$(options.itemCSSPath).not('.board-item-actions-armed').each( function() {
			$(this).dblclick( function(event) {
				$('.popover.in-focus').toggleClass('in').remove();
				if ( event.ctrlKey || event.metaKey ) return;
				modifyBoardItem( $(this), options, options.afterItemModified );
			});
			$(this).click( function(event) {
				if(event.ctrlKey || event.metaKey) {
					$(this).find('input[type=checkbox]').each(function() {
						$(this).is(':checked') ? $(this).removeAttr('checked') : $(this).attr('checked', 'checked');
					});
				}
				else {
					$('.board_item_body input[type=checkbox]').removeAttr('checked');
					$(this).find('input[type=checkbox]').each(function() {
						$(this).attr('checked', 'checked');
					});
				}
				toggleBulkActions();
			});
			$(this).addClass("board-item-actions-armed");
	});
	$(options.cellCSSPath).click(function(e) {
		if (!$(e.target).is(options.cellCSSPath)) return;
		$('.board_item_body input[type=checkbox]').removeAttr('checked');
		toggleBulkActions();
	})
}

function processBoardActions( methods, item, options )
{
	if ( methods.length < 1 ) return;
	var method = methods.shift();
	runMethod( 
			method.url,
			method.data,
			function ( result ) {
				processActionResult(result, item, options);
				processBoardActions(methods, item, options);
			},
			''
	);
}

function processActionResult( result, item, options ) 
{
	filterLocation.showActivity();
	resultObject = jQuery.parseJSON(result);
	if ( !resultObject ) {
		resultObject = {message:''};
	}

	switch ( resultObject.message )
	{
		case '':
			redrawBoardItem( item, options );
			break;

		case 'ok':
			if ( typeof resultObject.object != 'undefined' )
			{
				item.attr('object', resultObject.object);
				item.attr('lifecycle', 'created');
			}

			if ( typeof resultObject.object != 'undefined' )
			{
				item.attr('object', "");
			}
			
			break;
			
		case 'denied':
			$('#modal-form').parent().detach();
			$('body').append( '<div id="modal-form" title="'+options.classUserName+'">'+
					resultObject.description+'</div>' );

			$('#modal-form').dialog({
				width: 450,
				modal: true,
				buttons: { "Ok": function() { $(this).dialog("close"); } }
			});
			
			redrawBoardItem( item, options );
			break;

		case 'redirect':
			$.ajax({
				type: "GET",
				url: resultObject.url,
				dataType: "html",
				async: true,
				cache: false,
				success: 
					function(result) {
						$('#modal-form').parent().detach();
						$('body').append( '<div id="modal-form" style="display:none;">'+
							result+'</div>' );
						
						completeUIExt($('#modal-form'));
						
						$('#modal-form').attr('title', options.transitionTitle);
						window.onbeforeunload = null;
						
						$('#modal-form').dialog({
							width: (typeof resultObject.url == 'undefined' || resultObject.url.match(/issues\/board\?mode\=group/)
								? $(window).width() - 300
								: $(window).width() * 3/5),
							modal: true,
							open: function()
							{
								workflowMakeupDialog();
							},
							create: function() 
							{
						        $(this).css("maxHeight", $(window).height() - 200);        
						    },
							beforeClose: function(event, ui) 
							{
								redrawBoardItem( item, options );
								return workflowHandleBeforeClose(event, ui);
							},
							buttons: [
								{
									text: text('form-submit'),
									id: options.className+'SubmitBtn',
								 	click: function() {
										var dialogVar = $(this);
										
										if ( !validateForm($('#modal-form form[id]')) ) return false;
										
										// submit the form
										$('#'+options.className+'action').val('modify');
										$('#'+options.className+'redirect').val(resultObject.url+'&Transition=');

										$('#modal-form').parent()
											.find('.ui-button')
											.attr('disabled', true)
											.addClass("ui-state-disabled");
										
										$('#modal-form form[id]').ajaxSubmit({
											dataType: 'html',
											success: function( data ) 
											{
												try {
													var object = jQuery.parseJSON(data);
													workflowCloseDialog(dialogVar);
												}
												catch(e) {
													var warning = $(data).find('.form_warning');
													if ( warning.length > 0 )
													{
														$('#modal-form').parent()
															.find('.ui-button')
															.attr('disabled', false)
															.removeClass("ui-state-disabled");

														$('.form_warning').remove();
														$('<div class="alert alert-error form_warning">'+warning.html()+'</div>').insertBefore($('#modal-form form[id]'));
													}
												}
											},
											error: function( xhr )
											{
												$('#modal-form').parent()
													.find('.ui-button')
													.attr('disabled', false)
													.removeClass("ui-state-disabled");
											}
										});
									}
								},
								{
									text: text('form-close'),
									id: options.className+'CancelBtn',
									click: function() 
									{
										$(this).dialog('close');
									}
								}
							]
						});
					}
			});
	}
}

function initializeBoardItem( items, options )
{
	boardMake( options );

	completeUIExt(items);
	
	toggleBulkActions();
	
	options.initializeBoardItemCustom( items, options );
}

function modifyBoardItem( item, options, callback ) 
{
	var objectid = item.attr('object');
	
	if ( typeof objectid == 'undefined' || objectid == '' ) return;
	
	workflowModify({
		form_url: item.attr("project") ? '/pm/'+item.attr("project") + options.itemFormUrl : options.itemFormUrl,
		class_name: options.className,
		entity_ref: options.className,
		object_id: objectid,
		form_title: item.attr("uid") ? item.attr("uid") : options.classUserName
	}, "donothing");
}

function createBoardItem( query_string, options, data, callback ) 
{
	var base_url = options.itemFormUrl == '' ? filterLocation.location.toString() : options.itemFormUrl;
	
	var url = base_url;
	var url_items = url.split('#');
	
	url = url_items[0]; 
	
	if ( url.indexOf('?') < 0 ) {
		url += '?formonly=true';
	}
	else {
		url += '&formonly=true';
	}

	url += '&'+options.className+'action=show&'+query_string+
		'&form-mode=quick&entity='+options.className+'&'+options.className+'Id=';
	 
	filterLocation.showActivity();
	
	$.ajax({
		type: "GET",
		url: url,
		dataType: "html",
		async: true,
		cache: false,
		success: 
			function(result) {
				$('#modal-form').parent().detach();
				$('body').append( '<div id="modal-form" style="display:none;" title="'+options.classUserName+'">'+
					result+'</div>' );

				$('form[id]').attr('action', url);

				$('#'+options.className+'action').val('add');
				$('#'+options.className+'redirect').val(url);
				
				$.each(data, function( key, value ) {
					$('form[id]').append(
						'<input type="hidden" name="'+key+'" value="'+value+'">');
				});
				
				window.onbeforeunload = null;
				
				$('#modal-form').dialog({
					width: Math.min(950, $(window).width()*2/3),
					modal: true,
					height: 'auto',
					resizable: false,
					open: function()
					{
						workflowMakeupDialog();
					},
					create: function() 
					{
				        $(this).css("maxHeight", $(window).height() - 200);      
				    },
					beforeClose: function(event, ui) 
					{
						return workflowHandleBeforeClose(event, ui);
					},
					buttons: [
						{
							text: text('form-submit'),
							id: options.className+'SubmitBtn',
						 	click: function() {
								var dialogVar = $(this);
								
								if ( !validateForm($('#modal-form form[id]')) ) return false;
								
								$('#modal-form').parent()
									.find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
								
								$('form[id]').ajaxSubmit({
									dataType: 'html',
									success: function( data ) 
									{
										try {
											var object = jQuery.parseJSON(data);
											workflowCloseDialog(dialogVar);
											if ( typeof callback == 'function' ) {
												callback( object.Id, options );
											}
										}
										catch(e) {
											var warning = $(data).find('.form_warning');
											if (warning.length > 0) {
												$('#modal-form').parent()
													.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");

												$('.form_warning').remove();
												$('<div class="alert alert-error form_warning">' + warning.html() + '</div>').insertBefore($('form[id]'));
											}
										}
									},
									error: function( xhr )
									{
										$('#modal-form').parent()
											.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
									}
								});
							}
						},
						{
							text: text('form-close'),
							click: function() {
								$(this).dialog('close');
							}
						}
					]
				});
			}
	});
}

function redrawBoardItem( item, options )
{
	var url = filterLocation.locationTableOnly();
	
	var itemSelectors = [];
	
	if ( $.isArray(item) )
	{
		var objectIds = [];
		
		$.each(item, function(index, value) {
			itemSelectors.push(options.itemCSSPath+'[object='+value+']');
			objectIds.push(value);
		});

		url += '&'+options.redrawItemUrl+'='+objectIds.join(',');
	}
	else
	{
		var objectid = (typeof item != 'object' ? item : item.attr("object") );

		itemSelectors.push(options.itemCSSPath+'[object='+objectid+']');
		url += '&'+options.redrawItemUrl+'='+objectid;
	}
	
	url += options.resetParms;
	
	$.ajax({
		type: "GET",
		url: url,
		dataType: "html",
		async: true,
		cache: false,
		success: 
			function(result) 
			{
				updateBoardHeaders($(result));

				var items = [];
				
				$.each(itemSelectors, function(key, value) 
				{
					foundItem = $(result).find(value);
					foundItem.length > 0 ? items.push(foundItem) : $(value).remove();
				});

				if ( typeof options.redrawBoardItemCustom != 'undefined' ) 
				{
					options.redrawBoardItemCustom( $(items), result );
				}

				options.initializeBoardItem( $(itemSelectors.join(',')), options );
				
				filterLocation.hideActivity();
			}
	});
}

function redrawBoardChanges( options )
{
	if ( options.className == '' ) return;

	var url = filterLocation.locationTableOnly();
	
	url += options.resetParms + '&class='+options.className+'&wait=true';
	
	try {
		if ( options.waitRequest ) {
			options.waitRequest.abort();
			options.waitRequest = null;
		}
		options.waitRequest = $.ajax({
			type: "GET",
			url: url,
			async: true,
			cache: false,
			dataType: "html",
			success: function(result) 
			{
				updateBoardHeaders($(result));

				var items = [];
				var itemSelectors = [];
				
				$(result).find(options.itemCSSPath).each( function(index, value) 
				{
					itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object')+'"]';
					var item = $(itemSelector);
					if ( item.length < 1 ) return true;
					if ( item.is("[modified]") ) {
						if ( $(this).attr('modified') <= $(itemSelector).attr('modified') ) return true;
						itemSelectors.push(itemSelector);
						items.push($(this));
					}
				});

				$(result).find(".object-changed[object-id]").each( function(index, value) {
					itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object-id')+'"]';
					if ( $(result).find(itemSelector).length < 1 ) {
						$(itemSelector).remove();
					}
					else {
						if ( $(itemSelector).length < 1 ) {
							items.push($(result).find(itemSelector));
							itemSelectors.push(itemSelector);
						}
					}
				});

				if ( typeof options.redrawBoardItemCustom != 'undefined' ) {
					options.redrawBoardItemCustom( $(items), result );
				}

				options.initializeBoardItem( $(itemSelectors.join(',')), options );
				
				filterLocation.hideActivity();
			},
		    complete: function(xhr, textStatus)
		    {
		    	if ( textStatus == "abort" ) return;
		    	if ( xhr.responseText == "" ) return;
		    	if ( $.inArray(xhr.status, [302,301,500,404]) != -1 ) return;
		    	
	    		setTimeout( function() {
	    			redrawBoardChanges(options);
	    		}, $.inArray(textStatus, ["error","timeout","parsererror"]) < 0 ? 500 : 180000);
		    },
		    error: function (xhr, ajaxOptions, thrownError) {
				setTimeout( function() {
					redrawBoardChanges(options);
				}, 180000);
		    }
		});		
	}
	catch(e) {
		setTimeout( function() {
			redrawBoardChanges(options);
		}, 180000);
	}
}

function updateBoardHeaders( result )
{
	result.find('.board-table th').each( function(index, value) {
		$('.board-table th:eq('+index+')').html($(this).html());
	});
	result.find('.board-table tr.info').each( function(index, value) {
		$('.board-table tr.info[group-id="'+$(this).attr('group-id')+'"]').html($(this).html());
	});
	result.find('.board-table tr.row-basement').each( function(index, value) {
		$('.board-table tr.row-basement[group-id="'+$(this).attr('group-id')+'"]').html($(this).html());
	});
}

function selectCards( column )
{
	$('.list_cell .checkbox').attr('checked',false);
	$('.list_cell[more=" '+column+'"] .checkbox').attr('checked',true);
	toggleBulkActions();
}

function setBoardSize( options, value )
{
	var sizes = ['xs','s','m','l','xl'];
	$.each(sizes, function(i,v) {
	  $('.board-table').removeClass('board-size-'+v);
	});
	$('.board-table').addClass('board-size-'+sizes[value]);
	options.resizeCards();
	cookies.set('board-slider-pos', value);	
}