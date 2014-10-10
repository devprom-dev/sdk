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

				if( $('.list_cell[more="'+new_more+'"][group="'+new_group+'"]').length < 1 )
				{
					window.location.reload();
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

			if( jQuery.trim(item.attr("more")) != jQuery.trim(cell.attr("more")) )
			{
				return { url: controllerUrl+'methods.php?method=modifystatewebmethod',
					 data: { 'source': jQuery.trim(item.attr("more")), 
					 		 'target': jQuery.trim(cell.attr("more")), 
					 		 'object': item.attr("object"),
					 		 'class': this.className } };
			}

			if( jQuery.trim(item.attr("group")) != jQuery.trim(cell.attr("group")) )
			{
				return { url: controllerUrl+'methods.php?method=modifyattributewebmethod',
					 data: { 'attribute': this.groupAttribute, 
					 		 'value': jQuery.trim(cell.attr("group")), 
					 		 'object': item.attr("object"),
					 		 'class': this.className } };
			}

			if( parseInt(cell.attr("order")) >= 0 )
			{
				var tobe_seq = parseInt(cell.attr("order")) == parseInt(item.attr("order"))
					? Math.max(parseInt(cell.attr("order")) - 1, 0) : parseInt(cell.attr("order"));

				if ( parseInt(item.attr("order")) != tobe_seq )
				{
					return { url: controllerUrl+'methods.php?method=modifyattributewebmethod',
						 data: { 'attribute': 'OrderNum', 
						 		 'value': tobe_seq, 
						 		 'object': item.attr("object"),
						 		 'class': this.className } };
				}
			}
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
		start: function ( event, ui ) 
		{
			$(this).fadeTo('fast', 0.2);
		},
		stop: function ( event, ui ) 
		{
			if ( ui.helper.is(':visible') ) {
				$(this).fadeTo('fast', 1);
			}
		},
		className: '',
		groupAttribute: '',
		boardCreated: '',
		itemFormUrl: '',
		resetParms: '&view=board&state=all&rows=all&offset2=0&infosections=none',
		waitRequest: null
	};

function board( options ) 
{
	boardMake( options );
	
	//redrawBoardChanges( options );
	
	setTimeout( function () { redrawBoardChanges(options); }, 500 );
}

function boardMake( options )
{
	$(options.itemCSSPath)
		.width(options.getItemWidth()).draggable(options).css('position', '');

	$(options.cellCSSPath).droppable({
		hoverClass: options.hoverClass,
		accept: options.droppableAcceptFunction,
		drop: function( event, ui ) 
		{
			var item = ui.draggable;
			ui.helper.hide();
			
			var cell = $(this).is('.board-column') ? $(this).children('.list_cell') : $(this);
			var method = options.getMethodAttributes( item, cell );
	
			if ( item.attr("object") == "" )
			{
				createBoardItem( item.attr("createItemURIParms"), options, method.data, function( objectid, options ) {
					item.attr('object', objectid);
					item.attr('lifecycle', 'created');
	
					redrawBoardItem( objectid, options );
					item.attr('object', "");
				});
			}
			else if ( typeof method != 'undefined' )
			{
				runMethod( method.url, method.data,
					function ( result ) 
					{
						filterLocation.showActivity();
						resultObject = jQuery.parseJSON(result);
	
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
								$('#modal-form').remove();
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
											$('#modal-form').remove();
											$('body').append( '<div id="modal-form" style="display:none;">'+
												result+'</div>' );
											
											completeUIExt($('#modal-form'));
											
											$('#modal-form').attr('title', options.transitionTitle);
											window.onbeforeunload = null;
											
											$('#modal-form').dialog({
												width: resultObject.url.match(/issues\/board\?mode\=group/) ? $(window).width() - 300 : 750,
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
													formDestroy();
												},
												buttons: [
													{
														text: options.saveButtonName,
													 	click: function() {
															var dialogVar = $(this);
															
															if ( !validateForm($('#modal-form #object_form')) ) return false;
															
															// submit the form
															$('#'+options.className+'action').val('modify');
															$('#'+options.className+'redirect').val(resultObject.url+'&Transition=');

															$('#modal-form').parent()
																.find('.ui-button')
																.attr('disabled', true)
																.addClass("ui-state-disabled");
															
															$('#modal-form #object_form').ajaxSubmit({
																dataType: 'html',
																success: function( data ) 
																{
																	var warning = $(data).find('.form_warning');
																	
																	if ( warning.length > 0 )
																	{
																		$('#modal-form').parent()
																			.find('.ui-button')
																			.attr('disabled', false)
																			.removeClass("ui-state-disabled");
																		
																		$('.form_warning').remove();
																		$('<div class="alert alert-error form_warning">'+warning.html()+'</div>').insertBefore($('#modal-form #object_form'));
																	}
																	else 
																	{
																		dialogVar.dialog('close');
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
														text: options.closeButtonName,
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
					},
				'' );
			}
		}
	});
	
	$(".board_item_separator").droppable( "option", "tolerance", "touch" );
	
	$(options.itemCSSPath).not('.board-item-actions-armed').each( function() {
			$(this).dblclick( function() {
					modifyBoardItem( $(this), options, options.afterItemModified );
			});
			
			$(this).click( function(e) {
					if(e.ctrlKey) {
							$(this).find('input[type=checkbox]').each(function() {
									$(this).is(':checked') ? $(this).removeAttr('checked') : $(this).attr('checked', 'checked');
							});
					}
			});
			
			$(this).addClass("board-item-actions-armed");
	});
}

function initializeBoardItem( items, options )
{
	boardMake( options );

	completeUIExt(items);
	
	options.initializeBoardItemCustom( items, options );
}

function modifyBoardItem( item, options, callback ) 
{
	var objectid = item.attr('object');
	
	if ( typeof objectid == 'undefined' || objectid == '' ) return;
	
	workflowModify({
		form_url: '/pm/'+item.attr("project")+options.itemFormUrl,
		class_name: options.className,
		entity_ref: options.className,
		object_id: objectid,
		form_title: item.attr("uid")
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
				$('#modal-form').remove();
				$('body').append( '<div id="modal-form" style="display:none;" title="'+options.classUserName+'">'+
					result+'</div>' );

				$('#object_form').attr('action', url);

				$('#'+options.className+'action').val('add');
				$('#'+options.className+'redirect').val(url);
				
				$.each(data, function( key, value ) {
					$('#object_form').append(
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
						completeUIExt($('#modal-form').parent());
						
						$('#modal-form #object_form input:visible:first').blur();
						
						focusField('object_form');
					},
					create: function() 
					{
				        $(this).css("maxHeight", $(window).height() - 200);      
				    },
					beforeClose: function(event, ui) 
					{
						formDestroy();
						filterLocation.hideActivity();
					},
					buttons: [
						{
							text: options.saveButtonName,
						 	click: function() {
								var dialogVar = $(this);
								
								if ( !validateForm($('#modal-form #object_form')) ) return false;
								
								$('#modal-form').parent()
									.find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
								
								$('#object_form').ajaxSubmit({
									dataType: 'html',
									success: function( data ) 
									{
										var warning = $(data).find('.form_warning');
										
										if ( warning.length > 0 ) 
										{
											$('#modal-form').parent()
												.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
											
											$('.form_warning').remove();
											$('<div class="alert alert-error form_warning">'+warning.html()+'</div>').insertBefore($('#object_form'));
										}
										else 
										{
											var objectid = $(data).find('#'+options.className+'Id').val();
											
											dialogVar.dialog('close');
											
											if ( typeof callback == 'function' ) callback( objectid, options ); 
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
							text: options.closeButtonName,
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
	
	var itemSelectors = new Array();
	
	if ( $.isArray(item) )
	{
		var objectIds = new Array();
		
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
				$(result).find('.board-table th').each( function(index, value) {
					$('.board-table th:eq('+index+')').html($(this).html());
				});

				var items = new Array();
				
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
	
	if ( options.waitRequest )
	{
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
			$(result).find('.board-table th').each( function(index, value) 
			{
				$('.board-table th:eq('+index+')').html($(this).html());
			});
	
			var items = new Array();
			var itemSelectors = new Array();
			
			$(result).find(options.itemCSSPath).each( function(index, value) 
			{
				itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object')+'"]';
				
				if ( $(itemSelector).is("[modified]") && $(this).attr('modified') <= $(itemSelector).attr('modified') ) return true;
					
				itemSelectors.push(itemSelector);
				
				items.push($(this));
			});

			$(result).find(".object-changed[object-id]").each( function(index, value) 
			{
				itemSelector = options.itemCSSPath+'[object="'+$(this).attr('object-id')+'"]';
				
				if ( $(result).find(itemSelector).length < 1 ) $(itemSelector).remove();
			});
			
			if ( typeof options.redrawBoardItemCustom != 'undefined' ) 
			{
				options.redrawBoardItemCustom( $(items), result );
			}
	
			options.initializeBoardItem( $(itemSelectors.join(',')), options );
			
			filterLocation.hideActivity();
		},
	    complete: function(xhr, textStatus)
	    {
	    	if ( textStatus == "abort" ) return;
	    	
    		setTimeout( function() {
    			redrawBoardChanges(options);
    		}, $.inArray(textStatus, ["error","timeout","parsererror"]) < 0 ? 1 : 180000);
	    }
	});
}
