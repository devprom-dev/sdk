var draggableOptions = {
		itemCSSPath: ".board_item",
		cellCSSPath: ".list_cell,.board_item",
		sliderName: "",
		hoverClass: "list_cell_hover",
		revert: "invalid",
		revertDuration: 100,
		helper: "clone",
		cursor: "move",
		redrawBoardItemCustom: function ( item, jResult, putOnTop )
		{
            item.each(function() {
            	var self = $(this);
				var objectid = self.attr('object');
				var newCard = jResult.find('.board_item[object="'+objectid+'"]');
				var oldCard = $('.board_item[object="'+objectid+'"]');

				new_more = newCard.attr('more');
				new_group = newCard.parent().attr('group');
				old_more = oldCard.attr('more');
				old_group = oldCard.parent('.list_cell').attr('group');
				var jNewColumnGroup = $('.list_cell[more="'+new_more+'"][group="'+new_group+'"]');

				if( jNewColumnGroup.length < 1 ) {
					if ( $('.list_cell[group="-3"]').length > 0 ) {
						jNewColumnGroup = $('.list_cell[more="'+new_more+'"][group="-3"]');
 					} else {
						setTimeout(function() { window.location.reload(); }, 200);
						return;
					}
				}

				var itemFound = false;
				jNewColumnGroup.find('[sort="OrderNum"] > .board_item').each(function() {
						if ( parseInt(self.attr('order')) >= parseInt(newCard.attr('order')) ) {
							newCard.insertBefore(self);
							oldCard.remove();
							itemFound = true;
							return false;
						}
					});

				if ( !itemFound ) {
					if ( jNewColumnGroup.length < 1 ) return;

					if ( jNewColumnGroup.attr('sort') == 'OrderNum' ) {
						oldCard.remove();
						newCard.appendTo(jNewColumnGroup);
					}
					else {
						if ( oldCard.length > 0 && new_more == old_more && new_group == old_group ) {
							oldCard.replaceWith(newCard);
						}
						else {
							oldCard.remove();
							if ( putOnTop ) {
								newCard.prependTo(jNewColumnGroup);
							}
							else {
								newCard.appendTo(jNewColumnGroup);
							}
						}
					}
				}
			});
		},
		initializeBoardItemCustom: function ( item, options ) {},
		droppableAcceptFunction: function ( draggable ) {
			if ( !draggable.is(draggableOptions.itemCSSPath) ) return false;
			return true;
		},
		getMethodAttributes: function ( item, cell ) 
		{
			var controllerUrl = cell.is('[project]')
				? '/pm/'+cell.attr("project")+'/' : '/pm/'+item.attr("project")+'/';

			var url = '';
			var dataObject = {
				'object': item.attr("object"),
				'class': this.className
			};

			if( parseInt(cell.attr("order")) >= 0 )
			{
				var tobe_seq = parseInt(cell.attr("order")) == parseInt(item.attr("order"))
					? Math.max(parseInt(cell.attr("order")) - 1, 0) : parseInt(cell.attr("order"));

				if ( parseInt(item.attr("order")) != tobe_seq ) {
					dataObject.attribute = 'OrderNum';
					dataObject.value = Math.max(0, parseInt(tobe_seq) - 1);
				}
			}


			if( cell.is('.list_cell') && jQuery.trim(item.attr("group")) != jQuery.trim(cell.attr("group")) )
			{
                if ( this.groupAttribute == "State" ) {
                    dataObject.attribute = this.boardAttribute;
                    dataObject.value = jQuery.trim(cell.attr("more"));
                    dataObject.source = jQuery.trim(item.attr("group"));
                    dataObject.target = jQuery.trim(cell.attr("group"));
                }
                else {
                    dataObject.attribute = this.groupAttribute;
                    dataObject.value = jQuery.trim(cell.attr("group"));
                    url = controllerUrl + 'methods.php?method=modifyattributewebmethod';
                    if ( this.boardAttribute != '' ) {
                        dataObject.parms = {};
                        dataObject.parms[this.boardAttribute] = jQuery.trim(cell.attr("more"));
                    }
                }
			}

			if( cell.is('.list_cell') && jQuery.trim(item.attr("more")) != jQuery.trim(cell.attr("more")) )
			{
				if ( this.boardAttribute == "State" ) {
					dataObject.source = jQuery.trim(item.attr("more"));
					dataObject.target = jQuery.trim(cell.attr("more"));
					if ( this.groupAttribute != '' ) {
						dataObject.attribute = this.groupAttribute;
						dataObject.value = jQuery.trim(cell.attr("group"));
					}
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

			if ( dataObject.source ) {
                url = controllerUrl+'methods.php?method=modifystatewebmethod';
			}
			else if ( dataObject.attribute ) {
                url = controllerUrl+'methods.php?method=modifyattributewebmethod';
			}

			return {url: url, data: dataObject};
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
    	cancel: '.label,a,li',
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
			toggleBulkActions(event);
			$(this).fadeTo('fast', 0.2);
		},
		stop: function ( event, ui ) {
			if ( ui.helper.is(':visible') ) {
				$(this).fadeTo('fast', 1);
			}
		},
		className: '',
		entityRefName: '',
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
			$('.board-table:not(.board-size-xl) .list_cell '+this.itemCSSPath).each(function() {
				if ( width != $(this).width() ) {
					$(this).width(width);
					$(this).find('div.bi-cap, div.ca-field').width(width);
				}
			});
			$('.board-table .list_cell '+this.itemCSSPath).each(function() {
				$(this).find('.label-tag .label').width(width - 36);
			});
			var width = $('.board-column').width() - 8;
			$('.board-size-xl .list_cell '+this.itemCSSPath).each(function() {
				if ( width != $(this).width() ) {
					$(this).width(width);
					$(this).find('div.bi-cap, div.ca-field').width(width);
				}
			});
			$('.list_cell.board-size-mic '+this.itemCSSPath).css('width', '20px');
			$('.board-size-mic .list_cell '+this.itemCSSPath).css('width', '20px');
		},
		sliderTitle: '',
		methods: []
	};

function board( options ) 
{
	options.sliderName = 'board-slider-pos';
	var defaultBoardSize = (cookies.get(options.sliderName) != null
			? cookies.get(options.sliderName)
			: ($(window).width() <= 1024 ? 2 : 3));
	setBoardSize(options,defaultBoardSize);
	
	boardMake( options );

	$('#board-slider').slider({
	      value: defaultBoardSize,
	      min: -1,
	      max: 4,
	      step: 1,
	      slide: function( event, ui ) {
	    	  setBoardSize(options,ui.value);
	      }
	}).attr('title', options.sliderTitle);

	$('body')
		.on('click.dropdown.data-api', function(e) {
			var items = $(e.target).closest('a.collapse-cards');
			if ( items.length ) {
				var item = items.first();
				var cell = $('.list_cell[more="'+item.attr('more')+'"][group="'+item.attr('group')+'"]');
				if ( cell.length > 0 ) {
					var cookieId = item.parents('.board-table').attr('id') + "[size/"
						+ item.attr('more').trim() + "/" + item.attr('group').trim() + "]";
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
			}
			var items = $(e.target).closest('a[id=collapse-cards]');
			if ( items.length ) {
				var item = items.first();
				var state = item.attr('alt');
				var reset = item.parents('th').hasClass('board-size-mic');
				$('.list_cell[more=" '+state+'"]').each(function() {
					reset ? $(this).removeClass('board-size-mic') : $(this).addClass('board-size-mic');
				});
				var cookieId = item.parents('.board-table').attr('id') + "[column/" + state.trim() + "]";
				var index = item.parents('th').attr('value');
                var thel = item.parents('table').find('th[value="'+index+'"]');
				if ( reset ) {
                    thel.removeClass('board-size-mic');
					cookies.set(cookieId, null);
				}
				else {
                    thel.addClass('board-size-mic');
                    thel.attr('width', '1%');
					cookies.set(cookieId, "board-size-mic");
				}
				var title = item.text();
				item.text(item.attr('class'));
				item.attr('class',title);
				options.resizeCards();
				var regCells = $('.list_header:not(.board-size-mic)');
				$('.list_header:not(.board-size-mic)').attr('width',(100 / regCells.length) + '%');
				$('.list_header.board-size-mic').attr('width','');
			}
			if ( $(e.target).is(options.cellCSSPath) ) {
				$('.board_item_body input[type=checkbox]').removeAttr('checked');
				toggleBulkActions(e);
			}
		});

	setTimeout( function () { redrawBoardChanges(options); }, 500 );
    setInterval( function () { redrawBoardChanges(options); }, 60000 );
    $(document).on('windowActivated', function() {
        redrawBoardChanges(options);
	});
    $('.table-master').attachDragger();

    $('.cell-hidden-ids').on('click', function() {
    	var item = $(this);
    	$('<div class="document-loader"></div>').prependTo(item.parent());
		redrawBoardItem(item.attr('ids').split(','), options, function() {
			$('.document-loader').detach();
			item.detach();
		});
	});
}

function boardMake( options )
{
	options.resizeCards();
	$(options.itemCSSPath).not('.board-item-actions-armed').draggable(options);

	$(options.cellCSSPath).not('.board-item-actions-armed').droppable({
		hoverClass: options.hoverClass,
		accept: options.droppableAcceptFunction,
		methods: [],
		drop: function( event, ui ) {
			var item = ui.draggable;
            ui.helper.hide();

			var cell = $(event.target).is('.board-column')
				? $(event.target).children('.list_cell') : $(event.target);

			var method = options.getMethodAttributes( item, cell );
			if ( method.url == '' ) return;
			
			if ( $(event.target).is('.board_item') && !$(event.target).is($(item)) ) {
				$(item).insertBefore($(event.target)).fadeTo('fast', 0.9);
			}
			else {
				$(item).prependTo($(event.target)).fadeTo('fast', 0.9);
			}

			if ( options.methods.length < 1 ) {
				setTimeout(function() {
					processBoardActions(options.methods,item,options);
					options.methods = [];
				}, 300);
			}
			options.methods.push(method);
		}
	});
	$(".board_item").not('.board-item-actions-armed').droppable( "option", "tolerance", "touch" );

	$(options.itemCSSPath).not('.board-item-actions-armed').each( function() {
			$(this).dblclick( function(event) {
				$('.popover.in-focus').toggleClass('in').remove();
				if ( event.ctrlKey || event.metaKey ) return;
				modifyBoardItem( $(this), options, options.afterItemModified );
			});
			$(this).click( function(event) {
				if ( $(event.target).closest('.dropdown-toggle').length > 0 ) return;
				if(event.ctrlKey || event.metaKey) {
					$(this).find('input[type=checkbox]').each(function() {
						$(this).is(':checked')
							? $(this).removeAttr('checked').prop('checked', false)
							: $(this).prop('checked', true);
					});
				}
				else {
					$('.board_item_body input[type=checkbox]').removeAttr('checked').prop('checked', false);
					$(this).find('input[type=checkbox]').prop('checked', true);
				}
				toggleBulkActions(event);
                var id = $(this).attr('object');
                if ( id != '' ) {
                    $(document).trigger("trackerItemSelected", [id, event.ctrlKey || event.metaKey]);
                }
			});
			$(this).addClass("board-item-actions-armed");
	});
	$('.board-column').hover(
		function() {
			var index = $(this).index();
			$(this).parents('tr').next().find('td:eq('+index+').cell-add-btn a').show();
		},
		function() {
			var index = $(this).index();
			$(this).parents('tr').next().find('td:eq('+index+').cell-add-btn a').attr('style','');
		}
	);
}

function processBoardActions( methods, item, options )
{
	if ( methods.length < 1 ) return;
	var method = methods.shift();
	runMethod( 
			method.url,
			method.data,
			function ( result ) {
				if ( processActionResult(method, result, item, options) ) {
					processBoardActions(methods, item, options);
				}
			},
			'',
			true
	);
}

function processActionResult( method, result, item, options )
{
	var resultObject = {};
	try {
        resultObject = jQuery.parseJSON(result);
        if ( !resultObject ) {
            resultObject = {message:''};
        }
	}
	catch(e) {
        resultObject = {message:''};
	}

	switch ( resultObject.message )
	{
		case '':
			redrawBoardItem( item, options );
			break;

		case 'ok':
			if ( typeof resultObject.object != 'undefined' ) {
				item.attr('object', resultObject.object);
				item.attr('lifecycle', 'created');
			}
			if ( typeof resultObject.object != 'undefined' ) {
				item.attr('object', "");
			}
			break;
			
		case 'denied':
			redrawBoardItem( item, options );
			return false;

		case 'alert':
			workflowCloseDialog();
			$('body').append( '<div id="modal-form" style="display:none;">'+resultObject.description+'</div>' );
			$('#modal-form').dialog({
				width: Math.max($(window).width() * 0.28, 300),
				modal: true,
				resizable: false,
				closeText: "",
				open: function() {
					workflowMakeupDialog();
				},
				create: function() {
					workflowBuildDialog($(this),{form_title: options.transitionTitle});
				},
				buttons: [{
					tabindex: 1,
					text: text('form-continue'),
					id: options.entityRefName+'SubmitBtn',
					click: function() {
						method.data = $.extend({
							'suppress-alert': 'true'
						}, method.data);
						workflowCloseDialog();
						runMethod(
							method.url,
							method.data,
							function ( result ) {
								processActionResult(method, result, item, options);
							},
							'',
							false
						);
					}
				},{
					tabindex: 2,
					text: text('form-close'),
					id: options.entityRefName+'CancelBtn',
					click: function() {
						workflowCloseDialog();
						redrawBoardItem( item, options );
						return false;
					}
				}]
			});
			break;

		case 'redirect':
			$.ajax({
				type: "GET",
				url: resultObject.url,
				dataType: "html",
				async: true,
				cache: false,
				success: 
					function(result)
					{
                        workflowCloseDialog($('#modal-form'));
						$('body').append( '<div id="modal-form" style="display:none;">'+
							result+'</div>' );
						
						$('#modal-form').attr('title', options.transitionTitle);
						window.onbeforeunload = null;
						
						$('#modal-form').dialog({
							width: (typeof resultObject.url == 'undefined' || resultObject.url.match(/issues\/board\?mode\=group/)
								? $(window).width() * 0.9
								: getDialogWidth()),
							modal: true,
                            closeText: "",
							open: function()
							{
								workflowMakeupDialog();
							},
							create: function() 
							{
								workflowBuildDialog($(this),options);
						    },
							beforeClose: function(event, ui) 
							{
								redrawBoardItem( item, options );
								return workflowHandleBeforeClose(event, ui);
							},
                            dragStart: function(event, ui) {
                                workflowDragDialog(event, ui);
                            },
							buttons: [
								{
									text: text('form-submit'),
									id: options.entityRefName+'SubmitBtn',
								 	click: function() {
										var dialogVar = $(this);
										
										if ( !validateForm($('#modal-form form[id]')) ) return false;
										
										// submit the form
										$('#'+options.entityRefName+'action').val('modify');
										$('#'+options.entityRefName+'redirect').val(resultObject.url+'&Transition=');

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
														$('<div class="alert alert-error form_warning">'+warning.html()+'</div>').insertBefore($('#modal-form form[id][class_name]'));
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
									id: options.entityRefName+'CancelBtn',
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

	return true;
}

function initializeBoardItem( items, options )
{
	completeUIControls(items);
	options.initializeBoardItemCustom( items, options );
	boardMake( options );
}

function modifyBoardItem( item, options, callback ) 
{
	var objectid = item.attr('object');
	
	if ( typeof objectid == 'undefined' || objectid == '' ) return;
	
	workflowModify({
		form_url: item.attr("project") ? '/pm/'+item.attr("project") + options.itemFormUrl : options.itemFormUrl,
		class_name: options.className,
		entity_ref: options.entityRefName,
		object_id: objectid,
		form_title: options.classUserName
	}, "donothing");
}

function redrawBoardItem( item, options, callback )
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
			function(result) {
				var jqe = $(result);
				updateBoardHeaders(jqe, options);

				var items = [];
				$.each(itemSelectors, function (key, value) {
					foundItem = jqe.find(value);
					foundItem.length > 0 ? items.push(foundItem) : $(value).remove();
				});

				if (typeof options.redrawBoardItemCustom != 'undefined') {
					options.redrawBoardItemCustom($(items), jqe, items.length < 2);
				}

				options.initializeBoardItem($(itemSelectors.join(',')), options);

				if (callback) callback();
			}
	});
}

function redrawBoardChanges( options )
{
	if ( options.className == '' ) return;

	try {
		if ( !devpromOpts.windowActive ) return;

		if ( options.waitRequest ) {
			options.waitRequest.abort();
			options.waitRequest = null;
		}

		var url = filterLocation.locationTableOnly();
		url += options.resetParms + '&class='+options.className+'&wait=true';

		options.waitRequest = $.ajax({
			type: "GET",
			url: url,
			async: true,
			cache: false,
			dataType: "html",
			success: function(result) 
			{
				var jResult = $('<div>'+result+'</div>');
				updateBoardHeaders(jResult,options);
				updateUI(jResult);

				var items = [];
				var itemSelectors = [];

                jResult.find(options.itemCSSPath+'[modified]').each( function(index, value) {
					itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object')+'"]';
					var item = $(itemSelector);
					if ( item.length < 1 ) return true;
					if ( $(this).attr('modified') <= item.attr('modified') ) return true;
					itemSelectors.push(itemSelector);
					items.push($(this));
				});

                jResult.find(".object-changed[object-id]").each( function(index, value) {
					itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object-id')+'"]';
					var item = jResult.find(itemSelector);
					if ( item.length < 1 ) {
						$(itemSelector).remove();
					}
					else {
						if ( $(itemSelector).length < 1 ) {
							items.push(item);
							itemSelectors.push(itemSelector);
						}
					}
				});

				if ( typeof options.redrawBoardItemCustom != 'undefined' ) {
					options.redrawBoardItemCustom( $(items), jResult, items.length < 2 );
				}

				options.initializeBoardItem( $(itemSelectors.join(',')), options );
			},
		    complete: function(xhr, textStatus)
		    {
                var nativeResponse = xhr.getResponseHeader('X-Devprom-UI') == 'tableonly';
                if ( nativeResponse && xhr.responseText.indexOf('div') > -1 ) {
                    setTimeout( function() {
                        redrawBoardChanges(options);
                        toggleBulkActions(null,1);
                    }, 300);
				}
		    }
		});
	}
	catch(e) {
		setTimeout( function() {
			redrawBoardChanges(options);
		}, 180000);
	}
}

function updateBoardHeaders( result, options )
{
    if ( $('.dropdown-fixed.open').length > 0 ) return;
	result.find('.board-table th .brd-head-menu').each( function(index, value) {
		var header = $('.board-table th .brd-head-menu:eq('+index+')');
		if ( header.find('.more-actions.open').length > 0 ) return true;
		header.html($(this).html());
	});
    result.find('.board-table th .brd-head-details').each( function(index, value) {
        $('.board-table th .brd-head-details:eq('+index+')').html($(this).html());
    });
	result.find('.board-table tr.info[group-id]').each( function(index, value) {
		var items = $('.board-table tr.info[group-id="'+$(this).attr('group-id')+'"]');
		items.html($(this).html());
		completeUIControls(items);
	});
	result.find('.board-table tr.row-basement[group-id]').each( function(index, value) {
		$('.board-table tr.row-basement[group-id="'+$(this).attr('group-id')+'"]').html($(this).html());
	});
}

function selectCards( column )
{
	$('.list_cell .checkbox').attr('checked',false);
	$('.list_cell[more=" '+column+'"] .checkbox').attr('checked',true);
	toggleBulkActions();
}

function resizeCardsInGroup( group )
{
	var cell = $('.list_cell[group="'+group+'"]');
	if ( cell.length < 1 ) return;

	var cookieId = cell.parents('.board-table').attr('id') + "[size/row/" + group + "]";
	if ( cell.hasClass('board-size-mic') ) {
		cell.removeClass('board-size-mic');
		cookies.set(cookieId, null);
		$('[group-id="'+group+'"] .plus-minus-toggle').removeClass('collapsed');
	}
	else {
		cell.addClass('board-size-mic');
		cookies.set(cookieId, "board-size-mic");
		setTimeout(function() {
			$('[group-id="'+group+'"] .plus-minus-toggle').addClass('collapsed');
		});
	}
	draggableOptions.resizeCards();
}

function setBoardSize( options, value )
{
	var sizes = ['mic','xs','s','m','l','xl'];
	$.each(sizes, function(i,v) {
	  $('.board-table').removeClass('board-size-'+v);
	});
	$('.board-table').addClass('board-size-'+sizes[value+1]);
	options.resizeCards();
	cookies.set(options.sliderName, value);
}

//
// Allows user to click and drag to scroll horizontally
// Common usage:
// --------------------------------------------------------------------------
$.fn.attachDragger = function(){
    var attachment = false, lastPosition, position, difference;
    $( $(this).selector )
		.on("mousedown mouseup mousemove mouseleave",function(e){
			if ( ! $(e.target).is('td') ) return;
			if( e.type == "mousedown" ) {
				attachment = true, lastPosition = [e.clientX, e.clientY];
			}
			if( e.type == "mouseup" || e.type == "mouseleave" ) {
				attachment = false;
			}
			if( e.type == "mousemove" && attachment == true ){
				position = [e.clientX, e.clientY];
				difference = [ (position[0]-lastPosition[0]), (position[1]-lastPosition[1]) ];
				$(this).scrollLeft( $(this).scrollLeft() - difference[0] );
				$(this).scrollTop( $(this).scrollTop() - difference[1] );
				lastPosition = [e.clientX, e.clientY];
			}
		})
		.scroll(function(e) {
            $(this).find('.sticks-top-body').css('left',$(this).position().left - $(this).scrollLeft());
		});
    $(window).on("mouseup", function(){
        attachment = false;
    });
}