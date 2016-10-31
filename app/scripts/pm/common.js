var text = underi18n.MessageFactory(messages);
var devpromOpts = {
 	language: '',
 	datepickerLanguage: '',
	datepickerOptions: {},
 	dateformat: '',
 	project: '',
 	template: '',
 	methodsUrl: 'methods.php',
 	saveButtonName: text('form-submit'),
 	url: '',
 	iid: '',
 	version: '',
	windowActive: false
};

var formHandlers = [];
var originalState = '';
var previousPoint = null;
var originalFormState = '';
var lastActionBar = null;

 var filterLocation = 
 {
 	setup: function( value, timeout ) 
 	{
 		var values = value.split('=');
 		
 		this.parms[values[0]] = values[1];
 		
 		// do nothing if there were no changes in a filter
 		var urlIsChanged = this.location.replace(new RegExp("#", "g"), "") 
 			== window.location.toString().replace(new RegExp("#", "g"), "");
 		
 		if ( value == '' && urlIsChanged ) return;

		// cancel of data refresh
 		this.cancel();

		// build updated location
		this.location = updateLocation( value, this.location );

 		// do nothing if application is waiting for user activity
 		if ( timeout == 0 ) return;

		this.showActivity();

		if ( typeof timeout == 'undefined' ) timeout = 1500;
		
		var location = this.location.indexOf('&filterlocation') < 0 ? this.location+"&filterlocation" : this.location; 
		
 		this.timeout = window.setTimeout(
			" window.location = '"+location+"'; ", timeout );
 	},

	 resetFilter: function()
	 {
		 for ( var key in this.parms )
		 {
			 if ($.inArray(key,['','show','hide','group','sort','sort2','sort3','sort4','infosections','color']) >= 0) continue;
			 if ($.inArray(this.parms[key],['','all','hide']) >= 0) continue;
			 this.location = updateLocation( key+'=all', this.location );
		 }
		 window.location = this.location;
	 },

	 restoreFilter: function()
	 {
		 for ( var key in this.parms )
		 {
			 var re = new RegExp('[\\?\\&]'+key+'=[^\\&]*', 'gi');
			 if ( re.exec( this.location ) ) {
				 this.location = updateLocation( key+'=', this.location );
			 }
		 }
		 window.location = this.location;
	 },

	restoreColumns: function()
	{
 		if ( this.visibleColumns.length < 1 )
 		{
 			var re = new RegExp('show=([^\\&]+)');
 			var match = re.exec( this.location );
 			
 			if ( match != null )
 			{
 				this.visibleColumns = match[1].split('-');
 			}
 		}

 		if ( this.hiddenColumns.length < 1 )
 		{
 			var re = new RegExp('hide=([^\\&]+)');
 			var match = re.exec( this.location );
 			
 			if ( match != null )
 			{
 				this.hiddenColumns = match[1].split('-');
 			}
 		}
	},
	 	
 	showColumn: function ( name, timeout )
 	{
 		this.restoreColumns();
 		
		var columns = [];
		for ( var i = 0; i < this.hiddenColumns.length; i++ )
		{
 			if ( this.hiddenColumns[i] == name ) continue;
			columns.push( this.hiddenColumns[i] );
		}

		this.hiddenColumns = columns;
		found = false;
		
		for ( var i = 0; i < this.visibleColumns.length; i++ )
		{
 			if ( this.visibleColumns[i] == name ) {
 				found = true;
 				break;
 			}
		}

 		if ( !found ) 
 		{
	 		this.visibleColumns.push( name );
 		}
 		
 		this.setup( 'show=' + this.visibleColumns.join('-'), timeout );
 		this.setup( 'hide=' + this.hiddenColumns.join('-'), timeout );
 	},
 	
 	hideColumn: function ( name, timeout )
 	{
 		this.restoreColumns();

		var columns = [];
		for ( var i = 0; i < this.visibleColumns.length; i++ )
		{
 			if ( this.visibleColumns[i] == name ) continue;
			columns.push( this.visibleColumns[i] );
		}

		this.visibleColumns = columns;
		found = false;
		
		for ( var i = 0; i < this.hiddenColumns.length; i++ )
		{
 			if ( this.hiddenColumns[i] == name ) {
 				found = true;
 				break;
 			}
		}

 		if ( !found ) 
 		{
	 		this.hiddenColumns.push( name );
 		}
 		
 		this.setup( 'show=' + this.visibleColumns.join('-'), timeout );
 		this.setup( 'hide=' + this.hiddenColumns.join('-'), timeout );
 	},

 	turnOn: function ( parm, name, timeout )
 	{
		var names = name.split(',');

		if ( name == '' || name == 'all' || names.length > 1 ) {
			this.setup( parm+'='+name, timeout );
			return;
		}
		if ( typeof this.parms[parm] == 'undefined' ) {
			this.parms[parm] = 'all';
		}

		var values = this.parms[parm].split(',');
		var newvalues = [];

		found = false;
		
		for ( var i = 0; i < values.length; i++ )
		{
			if ( values[i] == 'all' ) continue;
			if ( values[i] == '' ) continue;
			
 			if ( values[i] == name ) found = true;

 			newvalues.push( values[i] );
		}
		if ( !found ) newvalues.push( name );

 		this.setup( parm+'='+newvalues.join(','), timeout );
 	},
 	
 	turnOff: function ( parm, name, timeout )
 	{
 		if ( name == 'all' ) {
 			this.setup( parm+'=', timeout );
 			return;
 	    }
		if ( typeof this.parms[parm] == 'undefined' ) {
			this.parms[parm] = 'all';
		}

		var values = this.parms[parm].split(',');
		var names = name.split(',');
		
		var newvalues = [];
		
		for ( var i = 0; i < values.length; i++ )
		{
 			if ( $.inArray(values[i], names) < 0 ) newvalues.push( values[i] );
		}

		this.setup( parm+'='+(newvalues.length < 1 ? 'none' : newvalues.join(',')), timeout );
 	},

 	setSort: function( sort_parm, field )
 	{
 		if ( $('li[uid='+sort_parm+'-a]>a').hasClass('checked') )
 		{
 			value = field+".A";
 		}

 		if ( $('li[uid='+sort_parm+'-d]>a').hasClass('checked') )
 		{
 			value = field+".D";
 		}
 		
 		this.setup( sort_parm+'='+value, 0 );
 	},
 	
 	setSortType: function( sort_parm, sort_type )
 	{
		var parts = this.parms[sort_parm].split('.');
		
		if ( sort_type == 'asc' )
		{
			this.setup( sort_parm+'='+parts[0]+".A", 0 );
		}
		
		if ( sort_type == 'desc' )
		{
			this.setup( sort_parm+'='+parts[0]+".D", 0 );
		}
 	},

 	cancel: function() 
 	{
 		if ( typeof this.timeout == "number" ) {
 			window.clearTimeout( this.timeout );
 			delete this.timeout;
 		}
 	},
 	
 	showActivity: function() 
 	{
 		$('i.icon-cog').addClass('filter-activity');
 	},
 	
 	hideActivity: function()
 	{
 		$('i.icon-cog').removeClass('filter-activity');
 	},
 	
 	hasActivity: function()
 	{
		return $('i.icon-cog').hasClass('filter-activity');
	},
 	
 	locationTableOnly: function()
 	{
 		var url = this.location;
 		
 		var items = url.split('#');
 		
 		url = items[0]; 
 		
 		if ( url.indexOf('?') < 0 ) 
 		{
 			url += '?tableonly=true';
 		}
 		else 
 		{
 			url += '&tableonly=true';
 		}
 		
 		return url;
 	},
 	
 	getParametersString: function ()
 	{
 		var keys = [];
 		
 		for ( var key in this.parms )
 		{
 			if ( key == '' ) continue;
 			
 			keys.push(key);
 		}
 		
 		return keys.join(',');
 	},
 	
 	getValuesString: function()
 	{
 		var values = [];
 		
 		for ( var key in this.parms )
 		{
 			if ( key == '' ) continue;
 			
 			values.push(this.parms[key]);
 		}
 		
 		return values.join(';');
 	},
 	
 	getEmptyValuesString: function()
 	{
 		var values = [];
 		
 		for ( var key in this.parms )
 		{
 			if ( key == '' ) continue;

 			values.push('');
 		}
 		
 		return values.join(';');
 	},

 	location: window.location.toString(),
 	visibleColumns: [],
 	hiddenColumns: [],
 	parms: []
 };

 if(!(window.console && console.log)) {
	console = {
		log: function(){},
		debug: function(){},
		info: function(){},
		warn: function(){},
		error: function(){}
	};
 }

 function is_firefox() 
 {
	return navigator.userAgent.indexOf("Firefox")!=-1;
 }

 // returns document parent window
 function getParentWindow()
 {
 	if(typeof document.parentWindow == 'undefined') {
 		return document.defaultView;
 	} else {
 		return document.parentWindow;
 	}
 }
 
function checkRows( group )
{
	$('#'+group+' tbody tr th input[type=checkbox]').is(':checked')
		? $('#'+group+' tbody tr td input[type=checkbox]').attr('checked', 'checked')
	    : $('#'+group+' tbody tr td input[type=checkbox]').removeAttr('checked');
	toggleBulkActions();
}

function checkRowsTrue( group )
{
	$('#'+group+' tr td .checkbox').attr('checked',true);
	toggleBulkActions();
}

function bulkDelete( class_name, method, url )
{
	translate( 636,
		function( text ) 
		{
			var ids = '';
			
			$('.checkbox').each(function() {
				if ( this.checked )
				{
					ids += this.name.toString().replace('to_delete_','')+'-';
				}
			});
			
			if ( ids != '' )
			{
				if ( !confirm(text) ) return;

				filterLocation.showActivity();				

				runMethod( devpromOpts.methodsUrl+'?method='+method, 
					{'class':class_name, 'objects':ids}, url, '' );
			}
			else
			{
				translate( 912, function( text ) { alert(text); } );
			}
		}
	);
}

function processBulk(title, url, id, callback)
{
	var ids = [];
	if ( typeof id != 'undefined' && id > 0 ) ids.push(id);
	$('.checkbox').each(function() {
		if ( this.checked ) {
			ids.push(parseInt(this.name.toString().replace('to_delete_',''), 10));
		}
	});
	if ( ids.length < 1 ) return;

	openAjaxForm(title, url.replace('%ids%',ids.join('-'))+'&bulkmode=complete&ids='+ids.join('-'), callback);
}

function toggleBulkActions()
{
	showSelectedCards();
	var ids = [];
	var states = [];
	$('.checkbox').each(function() {
		if ( this.checked ) {
			ids.push(parseInt(this.name.toString().replace('to_delete_',''), 10));
			var card = $(this).parents('[state]');
			var state = card.attr('state') + '-' + card.attr('project');
			if ( typeof state != 'undefined' && state != '' ) {
				if ( $.inArray(state.trim(), states) == -1 ) {
					states.push(state.trim());
				}
			}
		}
	});
	$('.bulk-filter-actions div[object-state]').hide();
	if ( states.length == 1 ) $('.bulk-filter-actions div[object-state="'+states.pop()+'"]').show();
	if ( ids.length > 0 ) {
		$(document).trigger("trackerItemSelected", ids[0]);
		$('.bulk-filter-actions').show();
	}
	else {
		$('.bulk-filter-actions').hide();
	}
}

function runMethod( method, data, url, warning, async )
{
	if ( warning != '' && !confirm(warning) ) return;
	if ( method.substr(0, 4) != '/pm/' && devpromOpts.project != '' ) method = '/pm/'+devpromOpts.project+'/'+method;
	if ( typeof async == 'undefined' ) async = true;

	filterLocation.showActivity();

	var ids = '';
	
	$('.checkbox').each(function() {
		if ( this.checked )
		{
			ids += this.name.toString().replace('to_delete_','')+'-';
		}
	});
	
	if ( ids != '' )
	{
		data.objects = ids;
	}
	
	$.ajax({
		type: "POST",
		url: method,
		dataType: "html",
		data: data,
		async: async,	
		success: 
			function(result, status, xhr) 
			{
				if ( xhr.getResponseHeader('status') == '500' )
				{
					window.location = '/500';
				}
			
				filterLocation.hideActivity();

				if ( xhr.getResponseHeader('status') == '404' )
				{
					result = '{"message":""}';
				}
				
				if ( typeof url == 'function' )
				{
					url( result );
				}
				else if ( typeof url == 'string' && url == 'donothing' )
				{
					donothing( result );
				}
				else
				{
					try {
						var resultObject = jQuery.parseJSON(result);
						if ( typeof resultObject.url != 'undefined' )
						{
							result = resultObject.url;
						}
						else
						{
							result = "";
						}
						
						if ( typeof resultObject.message != 'undefined' && resultObject.message != "ok" )
						{
							alert(resultObject.message);
						}
					}
					catch( e ) {
						if ( (new RegExp('Internal Server Error')).exec( result ) != null ) {
							window.location = '/500';
							return;
						}
					}

					if ( typeof url != 'undefined' )
					{
						if ( url+result == '' )
						{
							window.location.reload(true);
						}
						else
						{
							window.location = url+result;
						}
					}
				}
			},
		error: 
			function(result)
			{
				filterLocation.hideActivity();
			},
		statusCode:
			{
		      500: function(xhr) {
		    	  window.location = '/500';
		       }
			}
	});
}

function selectRefreshMethod( method_url, id, parm_name )
{
	$.ajax({
		url: method_url,
		dataType: 'html',
		data: { 
			'value': $('#select_'+id).val(),
			'valueparm': parm_name
		},
		error: function( xhr ) 
		{
		},
		success: function( data ) 
		{
			if ( data != '' )
			{
				filterLocation.setup( data );
			}
			else
			{
				window.location.reload();
			}
		}
	});				


}

function updateLocation( component, original )
{
	if ( component == '' ) return original;

	var parms = component.split('=');
	var location = original;

	var re = new RegExp('\\?'+parms[0]+'=[^\\&]*', 'gi');
	var match = re.exec( location );
	
	if ( parms[1] == '' && match != null )
	{
		location = location.replace(new RegExp(parms[0]+'=[^\\&]*\\&?', 'i'), ''); 
	}
	else
	{
		if ( match != null )
		{
			location = location.replace(re, '?'+component); 
			return location;
		}
		
		var re = new RegExp('\\&'+parms[0]+'=[^\\&]*', 'gi');
		var match = re.exec( location );
		
		if ( match != null )
		{
			location = location.replace(re, '&'+component); 
			return location;
		}
		
		location = location.replace(/[\?]/, '?'+component+'&'); 
		if ( location == original )
		{
			location += '?'+component;
		}
	}

	return location;
}

var timeout = 500;
var closetimer = 0;
var ddmenuitem = 0;
var ddrootitem = 0;

function dropdown_open()
{  
	dropdown_canceltimer();
	dropdown_close();
	
	if ( $(this).find('a:eq(0)').attr('class') != 'active' )
	{
		ddrootitem = $(this).find('a:eq(0)').attr('class', 'open');
	}

	ddmenuitem = $(this).find('ul:eq(0)');
	ddmenuitem.width(Math.max($(this).width(), ddmenuitem.width()));
	ddmenuitem.css('visibility', 'visible');
}

function dropdown_close()
{
	if(ddrootitem) ddrootitem.attr('class', '');
	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');
}

function dropdown_timer()
{
	closetimer = window.setTimeout(dropdown_close, timeout);
}

function dropdown_canceltimer()
{  
	if(closetimer)
   	{  
   		window.clearTimeout(closetimer);
   		closetimer = null;
   	}
}

function toggle_addcomment( method_id )
{
	var frm = document.getElementById('frame_'+method_id);
	var txt = document.getElementById('select_'+method_id);
 		
	frm.style.display = 
		frm.style.display == 'none' ?  'block' : 'none';
 				
	txt.focus();
}

function appendEmbeddedItem( form_id )
{
	if ( $('#embeddedFormBody'+form_id).html() == null )
	{
		body = $('#embeddedForm'+form_id).html();
		if ( $.browser.msie && $.browser.version < 9 )
		{
			$('#embeddedForm'+form_id).html('<form id="embeddedFormBody'+form_id+'" method="post" enctype="multipart/form-data">' +
				body + '</form>');
		}
		else
		{
			$('#embeddedForm'+form_id).html('');
			$('#embeddedForm'+form_id).append('<form id="embeddedFormBody'+form_id+'" method="post" enctype="multipart/form-data">');

			$('#embeddedFormBody'+form_id).append(body);
		}

		$( "#embeddedFormBody"+form_id+" .datepickerform" ).each(function() {
			$(this).datepicker(devpromOpts.datepickerOptions);
			$(this).datepicker("option", {
				onSelect: function(dateText, inst) {
					var tabindex = parseInt($(this).attr('tabindex')) + 1;
					$('input[tabindex='+tabindex+']').focus();
				}
			});
		})
	}

	$('#embeddedList'+form_id).hide();
	$('#embeddedList'+form_id).parent().find('a.embedded-add-button').hide();
	$('#embeddedForm'+form_id).show();

	// initialize editors
	$('#embeddedForm'+form_id).find('[contenteditable]').each(function(i) {
		var funcName = 'setup' + $(this).attr('id');
		if ( typeof window[funcName] != 'undefined' ) {
			window[funcName]();
		}
	});

	$('#embeddedForm'+form_id+' input:visible, #embeddedForm'+form_id+' textarea, #embeddedForm'+form_id+' select')
		.each( function() { 
			if ( $(this).attr('type') == 'button') return;
			if ( $(this).attr('type') == 'hidden') return;
			if ( $(this).attr('default') !== undefined ) {
				$(this).val($(this).attr('default'));
			}
			else {
				$(this).val('');
			}
			if ( $(this).attr("default") == "today" ) {
				$(this).attr("value", (new Date()).toString(devpromOpts.datejsformat));
			}

			$(this).keypress(function(e) {
				if ( e.which == 13 && !$(e.target).is('textarea') )
				{
					$('#embeddedForm'+form_id+' input[type="button"]:eq(0)').click();
					e.preventDefault();
				}
			});
		});

	jQuery.each($('#embeddedFormBody'+form_id+' input'), function() {
		if ( $(this).attr('type') == 'submit' || $(this).attr('type') == 'button' ) {
			if ( $(this).attr('class') == 'actionbutton' ) {
				$(this).attr('disabled', true);
			}
		}

		completeUIExt($('#embeddedFormBody'+form_id));
	});

	if ( !($.browser.msie && document.documentMode <= 9) )
	{
		$("#embeddedForm"+form_id).find("input:file")
			.unbind('change')
			.change( function() 
			{
				$("#embeddedForm"+form_id).find("input[action='save']").click();
			});
		
		$("#embeddedForm"+form_id).find("input:file")
			.filestyle({
		    	classText: 'span9 custom-file',
		    	classButton: 'custom-file',
		    	buttonText: '',
		    	icon: true,
		    	classIcon: 'icon-folder-open'
		    })
			.click();
	}

	focusField('embeddedForm'+form_id);
}
 			
function closeEmbeddedForm( form_id )
{
	$('#embeddedForm'+form_id).hide();
	$('#embeddedList'+form_id).show();
    $('#embeddedList'+form_id).parent().find('a.embedded-add-button').show();

	jQuery.each($('input'), function() {
		if ( $(this).attr('type') == 'submit' || $(this).attr('type') == 'button' ) {
			if ( $(this).attr('id') != 'saveEmbedded'+form_id && $(this).attr('id') != 'closeEmbedded'+form_id ) {
				$(this).removeAttr('disabled');
			}
		}
	});
}

function validateEmbedded( form_id, required )
{
	var valid = true;

	if ( !$('#embeddedForm'+form_id).is(':visible') ) return valid;

	valid = validateForm($('#embeddedForm'+form_id+' form'));
	if ( !valid ) return valid;

	if ( required == '' ) return valid;

	jQuery.each(required, function()
	{
		if ( $('#'+this).is(':not(div):visible') && $('#'+this).val() == '' || $('#'+this).is('div') && $('#'+this+'Value').val() == '' )
		{
			valid = false; 
			
			$('#'+this).fadeOut(0, function(){ $(this).css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $(this).css('background', 'white'); } );
		}

		if ( $('#'+this).val() == '' && $('#'+this).is('[searchattrs]') && $('#'+this).attr("searchattrs").indexOf('itself') > 0 && $('#'+this+'Text').val() != '' )
		{
			$('#'+this).val($('#'+this+'Text').val());
		}
		
		if ( ($('#'+this+'Text').val() == '' || $('#'+this).val() == '') && !$('#'+this).is(':visible') )
		{
			valid = false;

			$('#'+this+'Text').fadeOut(0, function(){ $(this).css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $(this).css('background', 'white'); } );
		}
		
		if ( $('#'+this).siblings('.custom-file').val() == '' && !$('#'+this).is(':visible') )
		{
			valid = false;
			
			$('#'+this).siblings('.custom-file').fadeOut(0, function(){ $(this).css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $(this).css('background', 'white'); } );
		}
		
 	});

	if ( !valid )
	{
		window.scrollTo($('#embeddedForm'+form_id).offset().left, $('#embeddedForm'+form_id).offset().top);
	}
	
	return valid;
}

function saveEmbeddedItem( form_id, jfields, required, callback )
{
	if ( !validateEmbedded( form_id, required ) )
	{
		return false;
	}

	var cache = $('#embeddedItems'+form_id);
	
	var itemsCount = $('#embeddedItemsCount'+form_id).val();
	if ( itemsCount < 1 ) itemsCount = 1;

	var project = $('#embeddedProject'+form_id).val();
	if ( project == '' ) project = devpromOpts.project; 
	
	var method_url = 'methods.php';
	if ( devpromOpts.project != '' ) method_url = '/pm/'+project+'/'+method_url;

	$("#embeddedForm"+form_id+" .embedded_footer")
		.children("input[type='button']").attr('disabled', true);
	
	$('#embeddedFormBody'+form_id).ajaxSubmit({
		url: method_url+'?method=processembeddedwebmethod',
		dataType: 'html',
		async: false,
		cache: false,
		error: function( xhr, status, e )
		{
			$("#embeddedForm"+form_id)
				.find("input[type='button']").removeAttr('disabled');
			
			alert(e);
		},
		success: function( data ) 
		{
			try
			{
				data = jQuery.parseJSON(data);
				if ( !data ) data = {};
			}
			catch( e )
			{
	 			if ( (new RegExp('Internal Server Error')).exec( data ) != null || (new RegExp('fatal', 'i')).exec( data ) != null )
 				{
	 				resetUnloadHandlers($("#embeddedForm"+form_id).parents('form').attr('id'));
	 				window.location = '/500';
 				}
	 			else
	 			{
	 				alert(e);
	 			}
	 			
 				return;
			}
			
			$("#embeddedForm"+form_id)
				.find("input[type='button']").removeAttr('disabled');

			var display_rule = '';
			if ( typeof data.caption != 'undefined' ) {
				display_rule = data.caption;
			}
			if ( display_rule == '' ) return;
			
			jQuery.each(jfields, function()
			{
				if ( $('#'+this).attr('type') == 'file' )
				{
 					$('<input class="embval'+itemsCount+'" type="hidden" name="'+this+'Tmp'+itemsCount+
 						'" value="'+data.file+'">').appendTo(cache);
	 						
 					display_rule = '<a class="modify_image" name="'+
 						data.name+'" href="'+data.url+'">'+data.caption+'</a>';
				}

				if ( $('#'+this).attr('type') == 'checkbox' )
				{
					$('<input class="embval'+itemsCount+'" type="hidden" name="'+this+itemsCount+'">')
						.appendTo(cache)
						.val($('#'+this).is(':checked') ? 'Y' : 'N');
				}
				else
				{
					$('<input class="embval'+itemsCount+'" type="hidden" name="'+this+itemsCount+'">')
						.val($('#'+this+',[name="'+this+'"]').val()).appendTo(cache);

					$('#'+this+'Text').val('');
				}
			});
	
			jQuery.each(required, function() {
				if ( this != '' ) $('#'+this).val('');
			});

			closeEmbeddedForm(form_id);
			cache.find('.noitems').remove();

			$('<input type="hidden" id="'+form_id+'Id'+itemsCount+'" name="F'+form_id+'_Id'+itemsCount+'" value="'+data.id+'">').appendTo(cache);
			$('<input type="hidden" id="'+form_id+'Delete'+itemsCount+'" name="F'+form_id+'_Delete'+itemsCount+'" value="0">').appendTo(cache);
			
			var row = $('#embeddedTemplates'+form_id).find('> .embeddedRowTemplate').clone();

			row.find('> .embeddedRow')
				.attr('id', form_id+'Caption'+itemsCount)
					.find('> .embeddedRowTitle .title').html(display_rule);
			
			row.find('li[uid=delete] a').attr('onclick', "javascript: deleteEmbeddedItem('"+form_id+"','"+itemsCount+"');");

			$(row.html()).appendTo(cache);
		
			$('.list_embedded_popup').each(function() {
				$(this).contextMenu( $('#'+$(this).attr('menu')) );
			});
			
			if ( typeof callback == 'function' )
			{
				window.setTimeout( function() {
					callback(form_id, data, itemsCount);
				}, 300);
			}

			itemsCount++;
			$('#embeddedItemsCount'+form_id).val(itemsCount);
		}
	});				
}
 			
function deleteEmbeddedItem( form_id, item )
{
	if ( $('#embeddedMode'+form_id).val() == 'standalone' )
	{
		var method_url = 'methods.php';
		if ( devpromOpts.project != '' ) method_url = '/pm/'+devpromOpts.project+'/'+method_url;
		
		runMethod(
			devpromOpts.methodsUrl+'?method=deleteembeddedwebmethod', 
			{
				'class': $('#embedded'+form_id).val(),
				'object': $('#'+form_id+'Id'+item).val(),
				'anchorObject': $('input[name="anchorObject'+form_id+'"]').val(),
				'anchorClass': $('input[name="anchorClass'+form_id+'"]').val()
			},
			function( result ) {
				$('#'+form_id+'Caption'+item).html('');
			},
			''
		);
	}
	else
	{
		var name = $('#'+form_id+'Caption'+item).find('> .embeddedRowTitle .title');
		name.html( '<strike>'+name.html()+'</strike>');
	
		$('#'+form_id+'Delete'+item).val('1');
	
		$('.embval'+item).val('');
	}
}

function translate( text_id, callback )
{
	var method_url = devpromOpts.methodsUrl;
	if ( devpromOpts.project != '' ) method_url = '/pm/'+devpromOpts.project+'/'+method_url;

	$.ajax({
		url: method_url+'?method=translatewebmethod',
		dataType: 'text',
		data: {'text':text_id},
		error: function( xhr, status, error ) 
		{
			if ( xhr.status === 0 ) return;
			alert(ajaxErrorExplain( xhr, error ));				
		},
		success: function( data ) 
		{
			try
			{
				data = jQuery.parseJSON(data);
				
				callback( data.text );
			}
			catch( e )
			{

	 			if ( (new RegExp('Internal Server Error')).exec( data ) != null )
 				{
	 				window.location = '/500';
 				}
	 			

			}
		}
	});				
}

function getattribute( classname, objectid, attributename, callbackname, converter )
{
	var method_url = devpromOpts.methodsUrl;
	if ( devpromOpts.project != '' ) method_url = '/pm/'+devpromOpts.project+'/'+method_url;

	$.ajax({
		url: method_url+'?method=getattributewebmethod',
		dataType: 'html',
		data: { 
			'class':classname, 
			'object': objectid, 
			'attr': attributename,
			'converter': converter
		},
		error: function( xhr ) 
		{
		},
		success: function( result ) 
		{
			callbackname( result );
		}
	});				
}

function taskboxClose( index )
{
	$('#tb'+index).hide();
	$('#embeddedActive'+index).val('N');
	
	if ( $('.taskbox:hidden').first().length > 0 ) $('#btn-more-tasks').show();
}

function taskboxShow()
{
	if ( $('.taskbox:hidden').first().length < 1 ) return;
	
	var box = $('.taskbox:hidden').first();
	
	box.show();
	
	$('#embeddedActive'+box.attr('form-id')).val('Y');
	
	if ( $('.taskbox:hidden').first().length < 1 ) $('#btn-more-tasks').hide();
}

function setupAutocomplete( $e )
{
	//if ( $e.next().val() != '' ) $e.autocomplete( "search", $e.next().val() );
	
	$e.focus(function() 
	{
		if ($(this).autocomplete("widget").is(":visible")) return;

		if ( $(this).next('input').val() == '' ) {
			// if there is no default value then open the list
			$(this).autocomplete( "search", $(this).val() == '' ? " " : $(this).val() );
		}
	})
	.click(function(event)
	{
		$(this).autocomplete( "search", " " );
	})
	.keydown( function(event) 
	{
		if ($(this).autocomplete("widget").is(":visible")) return;

		if ( event.which == 40 /* arrow down */ || event.which == 34 /* page down */) {
			$(this).autocomplete( "search", $(this).val() == '' ? " " : $(this).val() );
		}
	});
}

function objectAutoComplete( jqe_field, classname, caption, attributes, additional_attributes, project )
{
	var method_url = 'methods.php';
	if ( typeof project != 'undefined' && project != '' ) {
		method_url = '/pm/'+project+'/'+method_url;
	}
	else if ( devpromOpts.project != '' ) {
		method_url = '/pm/'+devpromOpts.project+'/'+method_url;
	}

	jqe_text = jqe_field.prev('input'); 
	
	jqe_text
		.autocomplete({
			source: function( request, response ) {
				that = this;
				if ( that.xhr ) {
					that.xhr.abort();
				}
				that.xhr = $.ajax({
					url: method_url+"?method=autocompletewebmethod&class="+classname+"&attributes="+attributes.join(',')+"&additional="+additional_attributes.join(','),
					data: request,
					dataType: "json",
					success: function( data ) {
						$.each(data, function(index, item) {
							if ( item.completed ) data[index].label = data[index].label.replace(/\[([A-Z]{1}-[\d]+)\]/, '{$1}');
						});
						response( data );
					},
					error: function() {
						response( [] );
					}
				});
			},
			select: function(event, ui) 
			{
				if ( typeof event.keyCode != 'undefined' && event.keyCode == 9 ) return false; // skip selection by TAB key

				jqe_field = $(this).next('input');
				jqe_field.val(ui.item ? ui.item.id : "");
				
				$.each(additional_attributes, function(index,value) {
					if ( value != "" ) jqe_field.attr(value, ui.item ? ui.item[value] : ""); 
				});
				jqe_field.trigger('dblclick');
			},
			change: function(event, ui) 
			{
				jqe_field = $(this).next('input');

				$.each(additional_attributes, function(index,value) {
					if ( value != "" ) jqe_field.attr(value, ui.item ? ui.item[value] : ""); 
				});

				if ( ui.item ) {
					if ( ui.item == 'commit' ) {
						if ( $(this).val() == '' ) {
							jqe_field.val(jqe_field.attr("default")).trigger('change');
						}
					}
					else {
						jqe_field.val(ui.item.id).trigger('change');
					}
				}
				else {
					if ( jqe_field.is("[searchattrs]") && jqe_field.attr("searchattrs").indexOf('itself') > 0 ) {
						jqe_field.val($(this).val()).trigger('change')
					}
					else {
						if ( $(this).val() == '' ) {
							jqe_field.val(jqe_field.attr("default")).trigger('change');
						}
					}
				}
	        },
			open: function()
			{
				$('.ui-autocomplete li a').each( function(index, item) {
					$(item).html($(item).html().replace(/\{([A-Z]{1}-[\d]+)\}/, '<strike>[$1]</strike>'));
				});
				
                $('.ui-autocomplete').width($(this).width() + (jqe_text.parents('.formvalueholder').find('button').length > 0 ? 160 : 0));
            }
		})
		.keypress( function(event) {
			event.stopImmediatePropagation();
			
			if ( event.which == 13 ) {
				window.setTimeout( function() {
					$(this).autocomplete( "close" );
				}, 1000);
			}
		});
	
	var jqe_text_id = jqe_text.attr('id');
	
	registerFormValidator( jqe_field.parents('form').attr('id'), function(form)
	{
		var choosen = form.find('#'+jqe_text_id).val();
		jqe_field = form.find('#'+jqe_text_id).next('input');
		
		if ( jqe_field.is("[searchattrs]") && jqe_field.attr("searchattrs").indexOf('itself') > 0 ) {
			if ( isNaN(choosen) && (jqe_field.val() == "" || isNaN(jqe_field.val()))) {
				jqe_field.val(choosen);
			}
		}
		return true;
	});
	
	setupAutocomplete( jqe_text );
}

function filterAutoComplete( field, url, caption )
{
	$("#filter_"+field)
		.autocomplete({ 
			  source: url,
			  select: function(event, ui) { 
			  	filterLocation.setup( field + "=" + ui.item.id );
			  }
		})
		.change( function() {
			if ( $(this).val() == '' ) {
				filterLocation.setup(field + "=all");
			}		
		});

		setupAutocomplete( $("#filter_"+field) );
	
		$('.ui-autocomplete-loading').
			css('background', 'white url("/scripts/autocomplete/css/indicator.gif") right center no-repeat;');
}

function executeAutoComplete( field, url, caption )
{
	$("#filter_"+field)
		.autocomplete({
			source: url,
			select: function(event, ui) { 
					runMethod( url, {'value': ui.item.id}, '', '' );
				}
		});
	
	setupAutocomplete( $("#filter_"+field) );
}

function quickSearchAutoComplete( field )
{
	var method_url = 'methods.php';
	if ( devpromOpts.project != '' ) method_url = '/pm/'+devpromOpts.project+'/'+method_url;

	$(field)
		.keydown(function(e) {
			$(this).popover('hide');
		})
		.autocomplete({
			source: method_url+"?method=autocompletewebmethod&class="+$(field).attr("object")+"&attributes="+$(field).attr("searchattrs"),
			select: function(event, ui) { 
					runMethod( method_url+"?method=gotoreportwebmethod", {'report': ui.item.id}, '', '' );
				},
			open: function() {
				$(this).autocomplete('widget').css({
						'z-index': 9999
					});
			}
		});
	
	$(field).focus(function() 
	{
		if ($(this).autocomplete("widget").is(":visible")) return;
		if ( $(this).next('input').val() == '' ) {
			// if there is no default value then open the list
			$(this).autocomplete( "search", $(this).val() == '' ? " " : $(this).val() );
		}
	})
	.keydown( function(event) 
	{
		if ($(this).autocomplete("widget").is(":visible")) return;
		if ( event.which == 40 /* arrow down */ || event.which == 34 /* page down */) {
			$(this).autocomplete( "search", $(this).val() == '' ? " " : $(this).val() );
		}
	});
}

function focusField( form )
{
	jQuery.each($('#'+form+' input, #'+form+' textarea, #'+form+' select'), function() 
	{
		if ( $(this).is('input[type="button"]') ) return true;
		
		if ( $(this).is('input[type="hidden"]') ) return true;
		
		if ( $(this).is('[readonly]') ) return true;

		if ( $(this).is('[auto-expand=false]') ) return true;

		if ( $(this).is('[default]') && $(this).attr("default") != '' ) return true;
		
		if ( $(this).is('.wysiwyg') )
		{
			var element = $(this);
            if ( CKEDITOR.instances[element.attr('id')] ) {
                setTimeout( function() { CKEDITOR.instances[element.attr('id')].focus(); }, 510 );
            }
			return false;
		}

		var element = $(this);
		
		setTimeout( function() { element.focus(); }, 110 );
		
		return false;
	});
}

function donothing( result )
{
}

function registerBeforeUnloadHandler( id, callback )
{
	getFormHandlers(id).unloaders.unshift( callback );
}

function resetUnloadHandlers(id)
{
	getFormHandlers(id).unloaders = [];
}

function beforeUnload(id)
{
	var handlers = getFormHandlers(id).unloaders;
	for ( var i = 0; i < handlers.length; i++ )
	{
		var result = handlers[i]();
		if ( typeof result == 'string' ) return result;
	}
}

function registerFormValidator( id, callback )
{
	getFormHandlers(id).validators.push(callback);
}

function getFormHandlers( id )
{
	if ( typeof id == 'undefined' ) {
		id = 'global';
	}

	for ( var i = 0; i < formHandlers.length; i++ ) {
		if ( formHandlers[i].id == id ) {
			return formHandlers[i];
		}
	}
	return formHandlers[formHandlers.push({
		'id': id,
		'validators': [],
		'destructors': [],
		'unloaders': []
	})-1];
}

function validateForm( form )
{
	var valid = true;
	var validators = getFormHandlers(form.attr('id')).validators;
	
	for ( var i = 0; i < validators.length; i++ ) {
		if ( ! validators[i](form) ) valid = false;
	}

	if ( !valid ) return valid;

	form.find('input[required], select[required], textarea[required], div.wysiwyg[required]')
		.each( function ()
		{
			if ( $(this).is('input:not(.autocomplete-text),select,textarea') && $(this).val() == '' || $(this).is('div') && $(this).text() == '' ) 
			{
				if ( !$(this).is(':visible') ) {
					var tabId = $(this).parents('.ui-tabs-panel').attr('id');
					if ( typeof tabId != 'undefined' ) {
						$('.ui-dialog a[href="#'+tabId+'"]').click();
					}
				}

				$(this).fadeOut(0, function(){ $(this).css('background', '#ffafaf');} ).
					fadeIn().fadeOut().fadeIn(300, function(){ $(this).css('background', 'white'); } );
					
				$('#'+$(this).attr('name')+'Text').fadeOut(0, function(){ $(this).css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $(this).css('background', 'white'); } );
	
				if ( $(this).offset().top < $(document).scrollTop() || $(this).offset().top	> $(document).scrollTop() + $(window).height() ) {
					window.scrollTo($(this).offset().left, $(this).offset().top - 80);
				}
				
				valid = false;
			}
		});

	return valid;
}

function registerFormDestructorHandler( id, callback )
{
	getFormHandlers(id).destructors.push(callback);
}

function formDestroy( id )
{
	var handlers = getFormHandlers(id);
	$.each(handlers.destructors, function(i,handler) {
		handler();
	});
	handlers.validators = [];
	handlers.destructors = [];
	handlers.unloaders = [];
}

function getRGB(color) 
{
    var result;
    if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color)) return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];
    if (result = /rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3}),\s*([0-9]{1,3})\s*\)/.exec(color)) return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];
    if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color)) return [parseFloat(result[1]) * 2.55, parseFloat(result[2]) * 2.55, parseFloat(result[3]) * 2.55];
    if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color)) return [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)];
    if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color)) return [parseInt(result[1] + result[1], 16), parseInt(result[2] + result[2], 16), parseInt(result[3] + result[3], 16)];
}

function showFlotTooltip(x, y, contents) 
{
    $('<div id="charttooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#fee',
        opacity: 0.80,
        zIndex: 9999
    }).appendTo("body");
    
    $('#charttooltip').css({
        left: Math.min(x + 5, $(window).width() - $('#charttooltip').width())
    }).fadeIn(200);
}

function reportAjaxError( xhr )
{
}

var flag = false;


function initializeApp()
{
	$.fn.colorPicker.defaults.showHexField = false;

	devpromOpts.datepickerOptions = $.extend(
		$.datepicker.regional[devpromOpts.datepickerLanguage],
		{
			numberOfMonths: 2,
			dateFormat: devpromOpts.dateformat
		}
	);
	$.ajaxSetup({
		cache: true
	});
	$(window)
		.on('beforeunload', function() {
			var result = null;
			$('form[id]').each(function() {
				result = beforeUnload($(this).attr('id'));
				if ( typeof result == 'string' ) return false;
			});
			if ( typeof result == 'string' ) return result;
			return beforeUnload('global');
		});

	if ( typeof document.visibilityState != 'undefined' ) {
		devpromOpts.windowActive = pageVisibility();
		pageVisibility( function() {
			devpromOpts.windowActive = pageVisibility();
		})
	}
	else {
		$(window)
			.focus(function() {
				devpromOpts.windowActive = true;
			})
			.blur(function() {
				devpromOpts.windowActive = false;
			});
	}

	cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
	cookies.set('devprom-client-tz', jstz.determine().name());
}

function completeUIExt( jqe )
{

	// twitter bootstrap adoption
	jqe.find('.dropdown-menu').on('click', function(e)
	{
		if ( $(e.target).attr('href') == '#' ) e.stopImmediatePropagation();
		
        if( $(e.target).hasClass('radio') )
        {
        	e.stopImmediatePropagation();
        	
            $(this).find('>li>a.checked[radio-group="'+$(e.target).attr('radio-group')+'"]')
            	.removeClass('checked').trigger('onkeydown');
            
            $(e.target).addClass('checked');
            
            setTimeout(function() {$(e.target).trigger('onkeydown');}, 100);
        }

        if( $(e.target).hasClass('checkable') )
        {
        	e.stopImmediatePropagation();
        	
            $(this).find('>li>a.radio.checked[radio-group="'+$(e.target).attr('radio-group')+'"]')
            	.removeClass('checked').trigger('onkeydown');

            $(e.target).hasClass('checked') 
            	? $(e.target).removeClass('checked') : $(e.target).addClass('checked');

            setTimeout(function() {$(e.target).trigger('onkeydown');}, 100);
        }
	});

	jqe.find('.dropdown-item-search input')
		.on('click', function(e) {
			e.stopImmediatePropagation();
			return false;
		})
		.on('keyup', function(e) {
			var items = $(this).parents('ul.dropdown-menu').find('li:not([uid=none]):not([uid=all]):not([uid=search]):not(.divider):not(:has(a.checked))');
			if ( $(this).val() == "" ) {
				var visibleItems = items.slice(0, 15);
			}
			else {
				var text = $(this).val();
				var visibleItems = items.filter(function(i, el) {
					return $(el).text().match(new RegExp(text, "ig"));
				});
			}
			items.hide();
			visibleItems.show();
		})
		.trigger('keyup');
	
	jqe.find('body')
		.on('click.dropdown.data-api', function(e) {
			filterLocation.setup('', 1);
			$('.tooltip').hide();
			$('.popover.with-popover').toggleClass('in').remove();
			$('.popover.with-tooltip').toggleClass('in').remove();
			$('.popover.in-focus').toggleClass('in').remove();
			if ( $(e.target).is('.title>a, .title>a>strike') ) {
				e.stopPropagation();
			}
			$('td#operations .dropdown-fixed').addClass('last');
			var target = $(e.target).parents('.dropdown-toggle').andSelf().attr('data-target');
			if ( typeof target != 'undefined' ) {
				target = $(target);
				if ( target.is('.dropdown-fixed') ) {
					target.css(dropdownMenuPosition(e, target));
				}
			}
		});

	jqe.find( ".datepicker" ).datepicker(devpromOpts.datepickerOptions);

	jqe.find("a.image_attach").fancybox({ 'hideOnContentClick': true });

	jqe.find("img.wiki_page_image").each( function() {
		if ( $.browser.msie ) {
				this.setAttribute('href', $(this).attr('src') + '&.png'); 
		} else {
				this.href = $(this).attr('src') + '&.png'; 
		}
	});
	
	jqe.find("img.wiki_page_image").fancybox({  
		hideOnContentClick: true
	});
	
	jqe.find("input[placeholder!=''], textarea[placeholder!='']").each( function() {
		$(this).keypress( function() {
			if ( $(this).val() != $(this).attr('placeholder') ) {
				$(this).removeClass('ac_welcome');
			}
			else {
				$(this).addClass('ac_welcome');
			}
		})
		.blur( function() {
			if ( $(this).val() == $(this).attr('placeholder') ) {
				$(this).addClass('ac_welcome');
			}		
		});
		
		if ( $(this).val() == $(this).attr('placeholder') ) $(this).addClass('ac_welcome');
	});
	
	jqe.find('#rightTab a:first').tab('show');

	jqe.find('#rightTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	jqe.find('.with-popover').popover({
		placement: 'bottom',
		html: true
	});

	jqe.find('.with-tooltip').popover({
		placement: function() {
			if ( $(this.$element).is('[placement]') ) return $(this.$element).attr('placement');
			return $(this.$element).offset().left < $(window).width() / 2 ? 'right' : 'left';
		},
		html:true,
		trigger: 'manual',
		container: 'body'
	});
	
	jqe.find('a.with-tooltip[info]')
		.hover(
			function(e) {
				showTooltip($(this));
			}, function(e) {
				$(this).data('popover').hide();
			}
		)
		.bind('contextmenu', function(e) {
			e.stopPropagation();
		});
	
	// Toggle fullscreen button:
	jqe.find('#toggle-fullscreen').button().off('click').on('click', function (e) 
    {
        var button = $(this), root = document.documentElement;
        
        e.stopImmediatePropagation();

        if (!button.hasClass('active')) {
            $('.content-internal').addClass('modal-fullscreen').css('width', '');
            if (root.webkitRequestFullScreen) {
                root.webkitRequestFullScreen(
                    window.Element.ALLOW_KEYBOARD_INPUT
                );
            } else if (root.mozRequestFullScreen) {
                root.mozRequestFullScreen();
            }
            button.addClass('active');
        } else {
        	button.removeClass('active');
            $('.content-internal').removeClass('modal-fullscreen');
            (document.webkitCancelFullScreen ||
                document.mozCancelFullScreen ||
                $.noop).apply(document);
        }
        
        window.setTimeout( function() {
			if ( typeof boardItemOptions != 'undefined' ) boardItemOptions.resizeCards();
        }, 350);
        
    });
	jqe.find('#toggle-detailspanel').button().off('click').on('click', function (e) {
		var button = $(this);
		e.stopImmediatePropagation();

		if (!button.hasClass('active')) {
			button.addClass('active');
		} else {
			button.removeClass('active');
		}
		toggleMasterDetails();
	});

    if ( !$.browser.msie )
    {
    	jqe.find("input:file").filestyle({
	    	classText: 'custom-file',
	    	classButton: 'custom-file',
	    	buttonText: '',
	    	icon: true,
	    	classIcon: 'icon-folder-open'
	    });
    }
    else
    {
    	jqe.find("input:file").css({'width':'100%'});
    }
    
    jqe.find("a.modify_image").click( function(e) 
    {
    	window.location = $(this).attr('href');
    	
    	e.stopImmediatePropagation();
    });
    
    jqe.find('.collapse')
	    .on('show', function() {
	    	var element = $(this);
	    	window.setTimeout( function() { if ( element.hasClass('in') ) { element.css('overflow', 'visible'); }}, 500 );
	    })
	    .on('hide', function() {
	    	$(this).css('overflow', 'hidden');
	    });
    
    jqe.find('td').each(function()
    {
    	$(this).find('.diff-html-added').css('background', '#90EC90');
    	$(this).find('.diff-html-removed').css('background', '#F59191');
	});
    
	jqe.find('.fieldautocompleteobject').each( function() {
		if ( $(this).attr('object') == '' || $(this).attr('id') == '' ) return true;

		objectAutoComplete( 
			$(this), 
			$(this).attr('object'), 
			$(this).attr('caption'), 
			$(this).attr('searchattrs').split(','), 
			$(this).attr('additional').split(','),
			$(this).attr('project')
		);
	});
	
	jqe.find('.search-query').each( function() {
		quickSearchAutoComplete($(this));
	});

	jqe.find('.ui-dialog-content')
		.css({
			'height':'auto',
			'overflow':'inherit',
			'display':'table-cell',
			'width':function() { return $(this).parent().width()-20; }
		})
		.resize( function() {
			$(this).parent()
				.css({"height":'auto'});
		})
		.parent()
			.css({'overflow':'inherit'});
	
	jqe.find('input[type="checkbox"][name*="to_delete"]').change(function(){
		toggleBulkActions();
	});

	if ( typeof ZeroClipboard != 'undefined' ) {
		ZeroClipboard.config({ cacheBust: false });
		try {
			var client = new ZeroClipboard(jqe.find('.clipboard'), {});
			client.on( 'ready', function(event) {
				client.on('aftercopy', function(event) {
					try {
						$(event.target).popover({
							'content': $(event.target).attr('data-message'),
							'title': '',
							'placement': 'right'
						});
						$(event.target).popover('show');
					}
					catch(e) {
					}
					setTimeout(function() {$(event.target).popover('hide');}, 2000);
				});
			});
		}
		catch(e) {
		}
	}

	if ( jqe.find('body').length > 0 ) {
		jqe.scroll( function(event)
		{
			var scrollPos = $(this).scrollTop();
			var contentStyle = 0;
			$('.sticks-top').each(function() {
				if ( !$(this).is(':visible') ) return false;
				var treePos = $(this).position().top;
				var body = $(this).parent().find('.sticks-top-body');
				if ( scrollPos >= treePos ) {
					if ( body.css('position') != 'fixed' ) {
						body.attr('wasPosition', body.css('position'));
						if ( body.height() > 200 ) {
							body.attr('wasHeight', body.height());
						}
						body.css({
							position: 'fixed',
							top: 0,
							width: $(this).width(),
							'overflow-y': 'auto',
							'overflow-x': 'hidden',
							height: $(this).attr("heightStyle") == "window" ? $(window).height() : 'auto'
						});
						if ( body.is('.ps-container') ) {
							body.perfectScrollbar('update');
						}
						body.addClass('sticked');
						body.find('.cke_panel').hide();
						contentStyle = 1;
					}
				}
				else if ( body.css('position') == 'fixed' ) {
					body.css({
						position: body.attr('wasPosition'),
						height: body.attr('wasHeight'),
						top: 'auto',
						'overflow-y': 'hidden',
						'overflow-x': 'hidden'
					});
					if ( body.is('.ps-container') ) {
						body.perfectScrollbar('update');
					}
					body.find('.cke_panel').hide();
					body.removeClass('sticked');
					contentStyle = 2;
				}
			});
			if ( contentStyle == 1 ) {
				$('.content-internal').css('min-height', $(window).height() * 1.1);
				$('footer').hide();
				$('.content-internal').addClass('content-internal-fullpage');
				$('body').addClass('fullpage');
			}
			if ( contentStyle == 2 ) {
				$('footer').show();
				$('.content-internal').removeClass('content-internal-fullpage');
				$('body').removeClass('fullpage');
			}
		});
	}

	jqe.find('[data-toggle="popover"]').popover({
		trigger: 'hover'
	});
	$('[data-toggle="tooltip-bottom"]').tooltip({
		placement: 'bottom'
	});

    completeChartsUI(jqe);

	setInterval(function() {
		var txt = cookies.get('last-action-message');
		if ( txt && txt != "" ) {
			if ( !lastActionBar ) {
				lastActionBar = new $.peekABar({
					cssClass: 'alert alert-success',
					backgroundColor: '#dff0d8',
					animation: {
						type: 'fade',
						duration: 450
					},
					delay: 8000,
					html: txt.replace(/\+/g, ' '),
					autohide: true,
					closeOnClick: true,
					onHide: function() {
						cookies.set('last-action-message', '');
						lastActionBar = null;
					}
				});
			}
			if ( !$('.peek-a-bar').is(':visible') ) {
				var width = Math.max($(window).width() * 1 / 3, 600);
				$('.peek-a-bar').css({
					width: width,
					left: ($(window).width() - width) / 2
				});
				lastActionBar.show();
			}
		}
	}, 1000);

	if ( $.browser.msie && document.documentMode < 8 ) {
		translate( 1352, function( text ) {
			$('<div class="alert alert-error" style="text-align:center;">'+text+'</div>').insertBefore('.container-fluid:eq(0)');
		});
	}

	$('.nav > li.dropdown > a').click( function() {
		$('.nav > li.dropdown').removeClass('active');
		$(this).parent('li').addClass('active');
		$('.vertical-menu').hide();

		var menu = $('#menu_'+$(this).parent('li').attr('area'));
		menu.show();
		adjustContainerHeight(menu);
	});
}

function markupDiff( el )
{
	var body = el.html();
	
	body = body.replace(/<del>/gi, '<span style="background:#F59191">');
	body = body.replace(/<\/del>/gi, '</span>');
	body = body.replace(/<ins>/gi, '<span style="background:#90EC90">');
	body = body.replace(/<\/ins>/gi, '</span>');
	
	el.html( body );
}

function bindFindInTreeField( selector, url )
{
	var button = $($.find(selector)[0]);
	var area = button.parent().parent().next();
	if ( area.find('.filetree ul li').length > 0 ) {
		area.show();
		return;
	}
	area.find('.filetree').treeview(
	{
		collapsed: false,
		url: url+area.attr('field-class'),
		asyncCallback: function() {
			area.find('.filetree a.item').click(function() 
           	{
   	        	$('#'+area.attr('field-name')+'Text').val($(this).text());
   	        	
   	        	$('#'+area.attr('field-name')+'Text').data("autocomplete")
   	        		._trigger("select", {}, {
   	        			item:{
   	        				id: $(this).parents('li').attr("id"),
   	        				DocumentId: $(this).parents('li').attr("documentid")
   	        			}
   	        		}
   	        	);
   	        	
   	        	area.hide();
   			});
		}
	});
	area.show();
}

function buildSnapshotSelect( field, form_id, baseline_name, data )
{
	var documentid = $(field).attr('documentid');

	if ( !documentid ) return;

	var selected = $.grep(data, function(value) {
		return value.documentid == documentid;
	});
	
	var select = $('#'+$(field).attr('id')+baseline_name);
	
	select.children('option').remove();

	$('<option value="" selected ></option>').appendTo(select);
	
	$.each(selected, function(index, value) {
		$('<option value="'+value.id+'">'+value.label+'</option>').appendTo(select);
	});
	
	select.change( function() {
		$('#F'+form_id+"_"+baseline_name).val($(this).val());
	});

	selected.length > 0 ? select.parent().parent().show() : select.parent().parent().hide();
}

function completeChartsUI( jqe )
{
    jqe.find('.plot[url]').bind("plotclick", function (event, pos, item) {
        window.location = $(this).attr('url');
    }).css('cursor', 'pointer');

    jqe.find('.plot-wide').each(function(index) {
        $(this).css('width', $('#tablePlaceholder').width() - 20);
    });

    jqe.find('.plot').each(function(index) {
		$(this).bind("plotclick", function (event, pos, item) {
			if (!item) return;
			if ( typeof item.series.urls != 'undefined' )
			{
				var url = item.series.urls[item.datapoint[0]];
				if ( typeof url != 'undefined' ) window.location = url;
			}
		});
		$(this).bind("plothover", function (event, pos, item) {
			if ( pos && pos.x ) $("#x").text(pos.x.toFixed(2));
			if ( pos && pos.y ) $("#y").text(pos.y.toFixed(2));
			if ( item ) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;
					$("#charttooltip").remove();

					var xValue = '';
                    var yValue = '';
					switch( typeof item.datapoint[0] )
					{
						case 'number':
							if ( item.datapoint[0] > 1000000 ) {
								var dt = new Date(item.datapoint[0]);
								xValue = dt.toString(devpromOpts.datejsformat);
							}
							else {
								xValue = item.datapoint[0];
							}
							break;
						default:
							xValue = item.datapoint[0];
					}
                    if ( typeof bar_labels != 'undefined' ) {
                        xValue = bar_labels[item.datapoint[0]];
                    }
					if ( xValue == "" && typeof item.series.xaxis.ticks != 'undefined' && item.series.xaxis.ticks.length > 0 ) {
						if ( typeof xValue == 'number' ) xValue = item.series.xaxis.ticks[xValue].label;
					}
					else if ( typeof item.series.label != 'undefined' ) {
						yValue = item.series.data[item.dataIndex][1];
					}
					else {
						yValue = "";
					}

					if ( typeof item.series.axisDescription != 'undefined' ) {
						if ( typeof item.series.axisDescription.xaxis != 'undefined' ) {
							xValue = item.series.axisDescription.xaxis + ": " + xValue;
						}
						else {
							xValue = "";
						}
						if ( typeof item.series.axisDescription.yaxis != 'undefined' ) {
							yValue = item.series.axisDescription.yaxis + ": " + item.series.data[item.dataIndex][1];
						}
						else {
							yValue = "";
						}
					}
					var text = (typeof item.series.label != 'undefined' ? item.series.label + ": " : "")
						+ yValue + ( xValue != '' ? " [" + xValue + "]" : "" );

					showFlotTooltip(item.pageX, item.pageY, text);
				}
			}
			else {
				$("#charttooltip").remove();
				previousPoint = null;
			}
		});
	});
}

function ajaxErrorExplain( jqXHR, exception )
{
    if ( jqXHR.status === 0 ) 
    {
        return ('There is no connection to the server.');
    } 
    else if (jqXHR.status == 404) 
    {
    	return ('Requested page not found. [404]');
    } 
    else if (jqXHR.status == 500) 
    {
    	return ('Internal server error [500].');
    }
    else if (exception === 'parsererror') 
    {
    	return ('Requested JSON parse failed: ' + jqXHR.responseText);
    } 
    else if (exception === 'timeout') 
    {
    	return ('Time out error.');
    } 
    else if (exception === 'abort') 
    {
    	return ('Ajax request aborted.');
    } 
    else 
    {
    	return ('Uncaught Error.\n' + jqXHR.responseText);
    }
}

function updateLeftWork( capacity, left )
{
	window.setTimeout( function()
	{
		leftValue = Math.max(0, left.attr('default') - capacity.val().replace(',','.')).toFixed(1);
		
		if ( leftValue == Math.round(leftValue).toFixed(1) ) leftValue = Math.round(leftValue);
		
		left.val(leftValue); 
	}, 5);
}

function closeInfoSection( section_id )
{
	$('#'+section_id).parent().parent().hide();
	
	filterLocation.turnOff('infosections', section_id, 1);
}

var asyncFormOptions = 
{
	beforeSubmitCallback: function( formId ) 
	{
		$('#result'+formId)
			.removeClass('alert alert-success alert-error')
			.html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');

		$('#preview').html('');
	},
	successCallback: function(response) {} 
};


function makeAsyncForm( formId, url, message, options )
{
	var formOptions = {};
	
	if ( !options ) options = asyncFormOptions; 
	
	focusField(formId);

	registerBeforeUnloadHandler(formId, function() {
		if ( originalFormState != $('#'+formId).formSerialize() ) {
			return message;
		}
	});
	
	if ( !$.browser.msie )
	{
		originalFormState = $('#'+formId).formSerialize();
	}

	formOptions = {
		dataType: 'html',
		beforeSerialize: function($form, options) 
		{ 
			if ( !validateForm($('#'+formId)) ) return false;

			return true;
		},
		beforeSubmit: function(a,f,o) 
		{
			options.beforeSubmitCallback(formId);
			
			$('.btn[type=submit]').attr('disabled', true);
		},
		error: function( xhr, status, error ) 
		{
			$('#result'+formId)
				.removeClass('alert-success alert-error')
				.addClass('alert alert-error')
				.html(ajaxErrorExplain( xhr, error ));

			$('.btn[type=submit]').attr('disabled', false);
		},
		success: function( response, status, xhr ) 
		{
			options.successCallback(response);
			resetUnloadHandlers(formId);
			
			try	{
				data = jQuery.parseJSON(response);
                $('#result'+formId).html('');
			}
			catch( e )
			{
	 			if ( (new RegExp('Internal Server Error')).exec( response ) != null ) {
	 				window.location = '/500';
 				}
	 			$('#result'+formId).removeClass('alert alert-success alert-error').addClass('alert alert-error').html(response);
	 			return;
			}

			if ( data == null || typeof data != 'object' ) {
				$('.btn[type=submit]').attr('disabled', false);
				return;
			}
			
			var state = data.state;
			var message = data.message;
			var objectid = data.object;
			
			if ( state == 'redirect' )
			{
				if ( message != '' )
				{
					 $('#result'+formId)
					 	.removeClass('alert-success alert-error')
						.addClass('alert alert-success')
						.html(message);
				} else {
                    $('#result'+formId).removeClass('alert alert-success alert-error').html('');
                }
				
				window.location = data.object;
				return;
			}

            if ( state == 'redirect-error' )
            {
                if ( message != '' )
                {
                    $('#result'+formId)
                        .removeClass('alert-success alert-error')
                        .addClass('alert alert-error')
                        .html(message);
                } else {
                    $('#result'+formId).removeClass('alert alert-success alert-error').html('');
                }

                setTimeout(function() { window.location = data.object; }, 1000);
                return;
            }

			$('.btn[type=submit]').attr('disabled', false);
			
			if ( $('#action'+formId).val() == '4' && state == 'success' )
			{
				$('#preview').html(message);
				return;
			}

			if ( message != '' )
			{
				$('#result'+formId)
					.removeClass('alert-success alert-error')
					.addClass('alert alert-'+state)
					.html(message);
            } else {
                $('#result'+formId).removeClass('alert-success alert-error').html('');
            }

			if ( state == 'success' && url != '' )
			{
				if ( (new RegExp('javascript:')).exec( url ) != null )
				{
					eval(url);
				}
				else
				{
					window.location = objectid != '' ? url + objectid : url;
				}
			}
		}
	};
	
	$('#'+formId).ajaxForm( formOptions );
	
	return formOptions;
}

function makeForm( formid, action )
{
	originalState = '';
	
	if ( action == 'show' ) 
	{
		window.setTimeout( function() { 
			focusField(formid);
		}, 200);
	}

	if ( action == 'view' ) return;

	registerFormValidator( formid, function(form) 
	{
		form.find('.embedded_form').children('div[multiple]:visible').filter( function() {
			 return this.id.match(/embeddedForm\d+/);
		}).find('.btn-primary').click();
	
		return form.find('.embedded_form').children('div[multiple]:visible').filter( function() {
			 return this.id.match(/embeddedForm\d+/);
		}).length < 1;
	});

	registerBeforeUnloadHandler(formid, function(){ return checkUnsavedForm(formid); });
	
	if ( !$.browser.msie )
	{
  		originalState = $('#'+formid+' *:visible').fieldSerialize();
	}
}

function checkUnsavedForm(formid)
{
    var action = $('#'+formid+' input[type="hidden"][action="true"]').val();
    
    if ( action == 'modify' || action == 'add' ) return;
    
	var nowState = $('#'+formid+' *:visible').fieldSerialize();
	if ( originalState != nowState ) {
		return $('#'+formid+' #unsavedMessage').val();
	}
	originalState = nowState;
	
	$('input[type="button"]').attr('disabled', true);
}
	
function submitForm( formid,action )
{
	if ( action != 'cancel' && action != 'delete' && !validateForm($('#'+formid)) ) return false;

	$('#'+formid+' input[type="hidden"][action="true"]').val(action); 
	
	document.getElementById(formid).submit();
}

function filterReports( text )
{
	var visible = $('table#reportlist1 tr').filter( function() 
	{
		found_text = text == '';
		
		$.each( $(this).find('td div'), function(i, val) {
			if ( $(val).text().match(new RegExp(text, "ig")) ) {
				found_text = true;
				return false;
			}
		});
		
		return found_text;
	});
	
	$('table#reportlist1 tr').filter( function() {
		return $(this).children('th').length < 1;
	}).hide();
	
	visible.show();

	$('table#reportlist1 tr.info').filter( function() {
		return $(this).nextUntil('tr.info','tr[id]:visible').length > 0;
	}).show();
}

function workflowMoveObject(project, object_id, object_class, entity_ref, from_state, to_state, transition, transition_title, callback, parms)
{
	var method = { 
			url: '/pm/'+project+'/methods.php?method=modifystatewebmethod',
			data: $.extend({
					'source': from_state,
					'target': to_state,
					'transition': transition,
					'object': object_id,
					'class': object_class
				}, parms),
		 	className: object_class,
		 	entityName: entity_ref,
		 	transitionTitle: transition_title
	};
	
	workflowRunMethod( method, callback );
}

function workflowRunMethod(method, callback)
{
	filterLocation.showActivity();
	beforeUnload();

	runMethod( method.url, method.data, function ( result )
	{
		filterLocation.hideActivity();
		
		resultObject = jQuery.parseJSON(result);

		switch ( resultObject.message )
		{
			case '':
			case 'ok':

				if ( typeof callback == 'function' ) callback(resultObject);
				
				break;
				
			case 'denied':
				
				$('#modal-form').parent().detach();
				
				$('body').append( '<div id="modal-form" title="'+method.transitionTitle+'">'+
						resultObject.description+'</div>' );

				$('#modal-form').dialog({
					width: 570,
					modal: true,
					buttons: { "Ok": function() { $(this).dialog("close"); } }
				});
				
				if ( typeof callback == 'function' ) callback(resultObject);
				
				break;

			case 'redirect':
				if ( typeof setupEditorGlobal == 'undefined' ) resultObject.url += "&global-scripts=true";
				resultObject.url += "&screenWidth="+$(window).width();

				$.ajax({
					type: "GET",
					url: resultObject.url,
					dataType: "html",
					async: true,
					cache: false,
					success: 
						function(result) 
						{
							$('#modal-form').parent().detach();
							
							$('body').append( '<div id="modal-form" style="display:none;">'+
								result+'</div>' );
							
							$('#modal-form').attr('title', method.transitionTitle);

							$('#modal-form').dialog({
								width: (typeof resultObject.url == 'undefined' || resultObject.url.match(/\?mode\=group/)
									? $(window).width() * 0.9
									: Math.min(950,$(window).width() * 0.9)),
								modal: true,
								open: function()
								{
									workflowMakeupDialog();
								},
								create: function() 
								{
									workflowBuildDialog($(this),{});
							    },
								beforeClose: function(event, ui) 
								{
                                    return workflowHandleBeforeClose(event, ui);
								},
								buttons: [
									{
										tabindex: 10,
										text: text('form-submit'),
										id: method.entityName+'SubmitBtn',
									 	click: function() {
											var dialogVar = $(this);
											
											if ( !validateForm($('#modal-form form[id]')) ) return false;
											
											// submit the form
											$('#modal-form #'+method.entityName+'action').val('modify');
											$('#modal-form #'+method.entityName+'redirect').val(resultObject.url+'&Transition=');

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
														if ( typeof callback == 'function' ) callback(resultObject);
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
												},
												statusCode:
												{
											      500: function(xhr) {
											    	  window.location = '/500';
											       }
												}
											});
										}
									},
									{
										tabindex: 11,
										text: text('form-close'),
                                        id: method.entityName+'CancelBtn',
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
	}, '' );	
}

function workflowMakeupDialog()
{
	completeUIExt($('#modal-form').parent());
    var formId = $('#modal-form form').attr('id');

	registerFormValidator( formId, function(form)
	{
		form.find('.autocomplete-text').each(function() {
			// hack to apply field changes before async submit of the form will occur
			$(this).data('autocomplete').selectedItem = "commit";
			$(this).data('autocomplete')._change();
		});
		form.parents('#modal-form').find('form[id] .btn-primary[type=submit]').click();
		form.find('.embedded_form').children('div[multiple]:visible').filter( function() {
			 return this.id.match(/embeddedForm\d+/);
		}).find('.btn-primary').click();
		return form.find('.embedded_form').children('div[multiple]:visible').filter( function() {
			 return this.id.match(/embeddedForm\d+/);
		}).length < 1;
	});

    registerBeforeUnloadHandler(formId, function() {
        if ( originalFormState != $('#'+formId).formSerialize() ) {
            return text('form-modified');
        }
    });

	$('#modal-form form[id] input:visible:first').blur();

    if ( !$.browser.msie ) {
		originalFormState = $('#'+formId).formSerialize();
    }

	focusField('modal-form form[id]');
}

function workflowBuildDialog( dlg, options )
{
	if ( $('#modal-form .tabs>ul>li').length > 0 ) {
		$('.ui-dialog .tabs').tabs({
			create: function(e, ui) {
				$(this).parent().find('.ui-icon-closethick').click(function() {
					dlg.dialog('close');
				});
			},
			select: function(e,ui) {
				$(document).trigger('tabsactivated', [e,ui]);
			}
		});
		dlg.parent().children('.ui-dialog-titlebar').replaceWith($('#modal-form .tabs'));
		dlg.attr('id', 'dummy');
		$('.ui-dialog .tabs').attr('id', 'modal-form').attr('style', dlg.attr('style')).append(dlg.detach());
		dlg.attr('style', 'display:none');
		if ( typeof options.tab != 'undefined' ) {
			$('.ui-dialog .tabs').tabs( "option", "selected", options.tab );
		}
	}
	dlg.dialog( "option", "draggable", true );
	dlg.css("maxHeight", $(window).height() - 200);
}

function workflowCompleteData( data )
{
	$.each(data, function(i, value) {
		if ( typeof window[value] == 'function' ) {
			data[i] = window[value]();
		}
	});
	return data;
}

function workflowCloseDialog( dialog )
{
	filterLocation.hideActivity();

	$('#modal-form form[id]').each(function() {
		resetUnloadHandlers($(this).attr('id'));
	});
	dialog.dialog('option', 'beforeClose', function() {});
	dialog.dialog('close');

	formDestroy($('#modal-form form').attr('id'));
	$('#modal-form').parent().detach();
	$('.ui-widget-overlay').remove();
}

function workflowNewObject( form_url, class_name, entity_ref, absoluteUrl, form_title, data, callback )
{
	beforeUnload();

	if ( form_url.indexOf('?') < 0 )
	{
		form_url += '?formonly=true';
	}
	else 
	{
		form_url += '&formonly=true';
	}

	form_url += '&'+entity_ref+'action=show&entity='+class_name+'&'+entity_ref+'Id=';
	form_url += '&screenWidth='+$(window).width();
	
	if ( typeof setupEditorGlobal == 'undefined' ) form_url += "&global-scripts=true"; 
	 
	filterLocation.showActivity();

	$.ajax({
		type: "GET",
		url: form_url,
		dataType: "html",
		data: workflowCompleteData(data),
		async: true,
		cache: false,
		success: 
			function(result, status, xhr) 
			{
				if ( xhr.getResponseHeader('status') == '500' )
				{
					window.location = '/500';
				}
			
				$('#modal-form').parent().detach();

				if ( xhr.getResponseHeader('status') == '404' )
				{
					filterLocation.hideActivity();
					return;
				}
				
				$('body').append('<div id="modal-form" style="display:none;" title="'+form_title+'"></div>');

				$(result).prependTo($('#modal-form'));
				
				$('#modal-form form[id]').attr('action', form_url);

				$('#modal-form #'+entity_ref+'action').val('add');
				
				$('#modal-form #'+entity_ref+'redirect').val(form_url);

				$('#modal-form').dialog({
					width: getDialogWidth(),
					modal: true,
					height: 'auto',
					resizable: false,
					open: function()
					{
						$.each(data, function( key, value ) 
						{
							var fields = $('#modal-form form[id] *[name="'+key+'"]');
							
							fields.each( function() {
								$(this).val(value);
							});
							
							if ( fields.length < 1 )
							{
								$('#modal-form form[id]').append('<input type="hidden" name="'+key+'" value="'+value+'">');
							}
						});
						
						workflowMakeupDialog();
					},
					create: function() 
					{
						workflowBuildDialog($(this),{});
				    },
					beforeClose: function(event, ui) 
					{
                        return workflowHandleBeforeClose(event, ui);
					},
					buttons:
						absoluteUrl == ""
							? [
								{
									tabindex: 10,
									text: devpromOpts.saveButtonName,
									id: entity_ref+'SubmitBtn',
									click: function () {
										workflowSubmitForm($(this), callback);
									}
								},
								{
									tabindex: 12,
									text: text('form-close'),
									id: entity_ref+'CancelBtn',
									click: function()
									{
										$(this).dialog('close');
									}
								}
							]
							: [
								{
									tabindex: 10,
									text: devpromOpts.saveButtonName,
									id: entity_ref+'SubmitBtn',
									click: function () {
										workflowSubmitForm($(this), callback);
									}
								},
								{
									tabindex: 11,
									text: text('form-submit-open'),
									id: entity_ref+'SubmitOpenBtn',
									click: function () {
										workflowSubmitForm($(this), function(id, data) {
											if ( typeof data.Url != 'undefined' ) {
												window.location = data.Url;
											}
											else {
												window.location = absoluteUrl + id;
											}
										});
									}
								},
								{
									tabindex: 12,
									text: text('form-close'),
									id: entity_ref+'CancelBtn',
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

function workflowSubmitForm(dialogVar, callback)
{
	if ( !validateForm($('#modal-form form[id]')) ) return false;

	$('#modal-form').parent()
		.find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");

	$('#modal-form form[id]').ajaxSubmit({
		dataType: 'html',
		success: function( data )
		{
			try {
				var object = jQuery.parseJSON(data);
				workflowCloseDialog(dialogVar);
				if ( typeof callback == 'function' ) {
					callback( object.Id, object );
				}
			}
			catch(e) {
				var warning = '';
				try {
					warning = $(data).find('.form_warning').html();
				}
				catch(e) {
					warning = e.message;
				}
				if ( warning != '' )
				{
					$('#modal-form').parent().find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
					$('.form_warning').remove();
					$('<div class="alert alert-error form_warning">'+warning+'</div>').insertBefore($('#modal-form form[id]'));
					return false;
				}
			}
		},
		error: function( xhr )
		{
			$('#modal-form').parent()
				.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
		},
		statusCode:
		{
			500: function(xhr) {
				window.location = '/500';
			}
		}
	});
}

function workflowHandleBeforeClose( event, ui )
{
	var result = null;
	$('#modal-form form[id]').each(function() {
		result = beforeUnload($(this).attr('id'));
		if ( typeof result == 'string' ) return false;
	});
	if ( typeof result == 'string' ) {
		if ( !confirm(text('form-modified')) ) return false;
	}

    formDestroy($('#modal-form form').attr('id'));
    $('#modal-form').parent().detach();
    filterLocation.hideActivity();

    return true;
}

function workflowModify( options, callback ) 
{
	beforeUnload();

	if ( options.form_url.indexOf('?') < 0 ) {
		options.form_url += '?formonly=true';
	}
	else {
		options.form_url += '&formonly=true';
	}

	options.form_url += '&'+options.entity_ref+'action=show&entity='+options.class_name+'&'+options.entity_ref+'Id='+options.object_id;
	options.form_url += '&screenWidth='+$(window).width();
	
	if ( typeof setupEditorGlobal == 'undefined' ) options.form_url += "&global-scripts=true"; 
	 
	filterLocation.showActivity();
	
	$.ajax({
		type: "GET",
		url: options.form_url,
		dataType: "html",
		async: true,
		cache: false,
		success: 
			function(result, status, xhr) 
			{
				workflowCloseDialog($('#modal-form'));

				if ( xhr.getResponseHeader('status') == '500' ) {
					window.location = '/500';
				}
				
				if ( xhr.getResponseHeader('status') == '404' ) return;
	
				$('body').append('<div id="modal-form" style="display:none;" title="'+options.form_title+'"></div>');

				var form = $(result);
				form.prependTo($('#modal-form'));
				
				$('#modal-form form[id]').attr('action', options.form_url);
				$('#modal-form #'+options.entity_ref+'action').val('modify');
				$('#modal-form #'+options.entity_ref+'redirect').val(options.form_url);

				$('#modal-form').dialog({
					width: getDialogWidth(),
					modal: true,
					height: 'auto',
					resizable: false,
					draggable: false,
					open: function()
					{
						workflowMakeupDialog();
					},
					create: function() 
					{
						workflowBuildDialog($(this), options);
				    },
					beforeClose: function(event, ui) 
					{
                        return workflowHandleBeforeClose(event, ui);
					},
					buttons: [
						{
							tabindex: 10,
							text: text('form-submit'),
							disabled: options.can_modify == 'false',
							id: options.entity_ref+'SubmitBtn',
						 	click: function() 
						 	{
								var dialogVar = $(this);
								
								if ( !validateForm($('#modal-form form[id]')) ) return false;
								
								$('#modal-form').parent().find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
								
								$('#modal-form form[id]').ajaxSubmit({
									dataType: 'html',
									success: function( data ) 
									{
										try {
											var object = jQuery.parseJSON(data);
											workflowCloseDialog(dialogVar);
											if ( typeof callback == 'function' ) {
												callback( options.object_id );
											}
										}
										catch(e) {
											var warning = $(data).find('.form_warning');
											if ( warning.length > 0 )
											{
												$('#modal-form').parent()
													.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");

												$('.form_warning').remove();
												$('<div class="alert alert-error form_warning">'+warning.html()+'</div>').insertBefore($('#modal-form form[id]'));
											}
										}
									},
									error: function( xhr )
									{
										$('#modal-form').parent()
											.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
									},
									statusCode:
									{
									  500: function(xhr) {
									    	  window.location = '/500';
									       }
									}
								});
							}
						},
						{
							tabindex: 11,
							text: text('form-close'),
							id: options.entity_ref+'CancelBtn',
							click: function()
							{
								$(this).dialog('close');
							}
						},
						{
							tabindex: 12,
							text: text('form-delete'),
							id: options.entity_ref+'DeleteBtn',
							disabled: options.can_delete == 'false',
							title: options.can_delete == 'false' ? options.delete_reason : '',
							click: function()
							{
								if ( $('#modal-form form.delete-confirm').length > 0 && !confirm(text('form-delete-msg')) ) return;

								var dialogVar = $(this);
								$('#'+options.entity_ref+'action').val('delete');
								$('#modal-form').parent().find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
								$('#modal-form form[id]').ajaxSubmit({
									dataType: 'html',
									complete: function( data )
									{
										workflowCloseDialog(dialogVar);
										if ( typeof callback == 'function' ) callback();
									}
								});
							}
						}
					]
				});
			}
	});
}

function workflowTable( form_url, title )
{
	if ( form_url.indexOf('?') < 0 ) 
	{
		form_url += '?tableonly=true';
	}
	else 
	{
		form_url += '&tableonly=true';
	}

	if ( typeof setupEditorGlobal == 'undefined' ) form_url += "&global-scripts=true"; 
	 
	filterLocation.showActivity();
	
	$.ajax({
		type: "GET",
		url: form_url,
		dataType: "html",
		async: true,
		cache: false,
		success: 
			function(result) 
			{
				var scripts = [];
				
				$('#modal-form').parent().detach();
				
				$('body').append('<div id="modal-form" style="display:none;" title="'+title+'"></div>');

				$(result).prependTo($('#modal-form'));
				
				$('#modal-form').dialog({
					width: Math.max($(window).width() - 60, 750),
					height: $(window).height() - 60,
					modal: true,
					open: function()
					{
						completeUIExt($(this));
					},
					beforeClose: function(event, ui) 
					{
						formDestroy($('#modal-form form').attr('id'));
						
						filterLocation.hideActivity();
					},
					buttons: [
						{
							text: "Ok",
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

function workflowGetField( options, callback ) 
{
	if ( options.form_url.indexOf('?') < 0 ) 
	{
		options.form_url += '?attributeonly=true';
	}
	else 
	{
		options.form_url += '&attributeonly=true';
	}

	options.form_url += '&entity='+options.class_name+'&object='+options.object_id+'&attribute='+options.attribute;
	
	filterLocation.showActivity();
	
	$.ajax({
		type: "GET",
		url: options.form_url,
		dataType: "html",
		async: true,
		cache: false,
		success: function(result) 
		{
			var resultObject = jQuery.parseJSON(result);
			
			if ( typeof resultObject[options.attribute] != 'undefined' )
			{
				callback(resultObject[options.attribute]);
			}
		}
	});
}

function openAjaxForm(title, url, callback) 
{
	filterLocation.showActivity();
	beforeUnload();

	$.ajax({
		type: "GET",
		url: url+'&formonly=true&screenWidth='+$(window).width(),
		dataType: "html",
		async: true,
		cache: false,
		success: 
			function(result, status, xhr) 
			{
				filterLocation.hideActivity();
				if ( xhr.getResponseHeader('status') == '500' ) {
					window.location = '/500';
				}
				if ( xhr.getResponseHeader('status') == '404' ) {
					return;
				}

				$('#modal-form').parent().detach();
				$('body').append('<div id="modal-form" style="display:none;" title="'+title+'"></div>');
				$(result).prependTo($('#modal-form'));
				
				$('#modal-form').dialog({
					width: 750,
					modal: true,
					resizable: true,
					open: function() {
						workflowMakeupDialog();
					},
                    beforeClose: function(event, ui) {
                        return workflowHandleBeforeClose(event, ui);
                    },
					buttons: [
						{
							tabindex: 10,
							text: text('form-complete'),
							id: 'SubmitBtn',
						 	click: function() 
						 	{
								var dialogVar = $(this);
						 		
								$('#modal-form').parent().find('.ui-button').attr('disabled', true).addClass("ui-state-disabled");
								var form = $('#modal-form').contents().find('form');
								form.ajaxSubmit({
									dataType: 'html',
									beforeSerialize: function($form, options) 
									{ 
										if (!validateForm(form)) {
											$('#modal-form').parent().find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
											return false;
										}
										return true;
									},
									success: function(response) 
									{
										try	{
											var data = jQuery.parseJSON(response);
										}
										catch( e ) {
								 			if ( (new RegExp('Internal Server Error')).exec( response ) != null ) {
								 				window.location = '/500';
							 				}
								 			$('#modal-form').contents().find('#result')
												.removeClass('alert-success alert-error').addClass('alert alert-error').html(response);
								 			return;
										}
										
										$('#modal-form').contents().find('#result').removeClass('alert alert-success alert-error').html('');
										
										var state = data.state;
										var message = data.message;
										
										if ( message != '' ) {
											$('#modal-form').contents().find('#result')
												.removeClass('alert-success alert-error')
												.addClass('alert alert-'+(state == 'error' ? 'error' : 'success'))
												.html(message);
										}
										
										if ( state == 'error' ) {
											$('#modal-form').parent().find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
										} else {
											$('#modal-form').parent().find('.ui-button[id="CancelBtn"]').attr('disabled', false).removeClass("ui-state-disabled");
											setTimeout(function() {
												workflowCloseDialog(dialogVar);
												if ( state == 'redirect' ) {
													window.location = data.object;													
												} else {
													if ( typeof callback != 'undefined' ) callback();
												}
											}, message != "" ? 1500 : 1);
										}
										
										$('#modal-form').contents().find('input[name="action"]').val(1);
									},
									complete: function(xhr) {
									},
									error: function( xhr )
									{
										$('#modal-form').parent()
											.find('.ui-button').attr('disabled', false).removeClass("ui-state-disabled");
									},
									statusCode:
									{
									  500: function(xhr) {
									    	  window.location = '/500';
									       }
									}
								});
							}
						},
						{
							tabindex: 11,
							text: text('form-close'),
							id: 'CancelBtn',
							click: function() {
								$(this).dialog('close');
							}
						}
					]
				});
			}
	});
}

function disableScrolling( element )
{
	var handler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		return false;
	};
	
	element.bind('scroll, mousewheel', handler);
	
	return handler;
}

function enableScrolling( element, handler )
{
	element.unbind('scroll, mousewheel', handler);
}

function wikiChangeTemplate(class_name, editor_name, $callback)
{
	if ( $('#WikiPageTemplate').val() == '' ) return;
	
	runMethod( 'methods.php?method=GetWikiContentWebMethod',
		{ 
			'object':$('#WikiPageTemplate').val(),
			'class':class_name,
			'encoding':'native',
			'editor':editor_name
		},
		$callback, 
		'');
}

function updateLeftWorkAttribute( form_id, data, row_number )
{
	$('#F'+form_id+"_LeftWork").attr('default', $('input[name="F'+form_id+'_LeftWork'+row_number+'"]').val());
}

function setUXData()
{
	if ( devpromOpts.url == "" ) return;
	sendUXData( window.location.toString() );
}

function sendUXData( url )
{
	try
	{
		$.ajax({
			type:"POST", 
			global: false, 
			processData:false, 
			cache: false, 
			url: devpromOpts.url,
			dataType: "text",
			async: true,
			headers: {
				"IID":devpromOpts.iid, 
				"VER":devpromOpts.version,
				"URL": url
			}
		});
	}
	catch(e)
	{
	}
}

function addToFavorites( widget_uid, widget_url, widget_type )
{
	$.ajax({
		type: "POST",
		url: '/pm/'+devpromOpts.project+'/menu/rest/functionalareas/favs/nodes',
		dataType: "json",
		data: {
			id: widget_uid, 
			type: widget_type
		},
		success: 
			function(result, status, xhr) 
			{
				widget_url = decodeURIComponent(widget_url);
				
				widget_url += widget_url.indexOf('?') < 0 ? '?' : '&';
				
				widget_url += "area=favs";
			
				window.location = widget_url;
			}
	});	
}

function uiShowSpentTime()
{
	var section = $('a[href*=pagesectionspenttime]');
	if ( section.length < 1 ) return;
	section.click();
	window.scrollTo(section.offset().left, section.offset().top);
}

function reloadPage()
{
	window.location.reload();
}

function getCheckedRows()
{
	ids = '';
	$('.checkbox').each(function() {
		if ( this.checked )
		{
			ids += this.name.toString().replace('to_delete_','')+'-';
		}
	});
	return ids;
}

function setupLocationParameter( param )
{
	var location = window.location.toString();
	location = updateLocation(param, location);
	window.location = location; 
}

function moveNextCase()
{
	if ( $('.pagination a.btn-primary').length < 1 ) {
		window.location.reload();
		return;
	}
	try {
		if ($('.pagination a.btn-primary').parent('li').is(':last-child')) return;
		var nextCase = parseInt($('.pagination a.btn-primary').text());
		setupLocationParameter('offset1='+nextCase);
	}
	catch(e) {
	}
}

function completeUICustomFields( formId, anchor, fields, values )
{
	$('#'+formId+' '+anchor).change( function() {
		jQuery.each(fields, function(key, value) {
			$('#'+formId+' #fieldRow'+value).hide();
		});
		var selected = '';
		var option = $(this).find('option[value="'+$(this).val()+'"]');
		if ( option.length > 0 ) {
			selected = option.attr('referenceName');
		}
		else {
			selected = $(this).attr('referenceName');
		}
		jQuery.each(fields, function(key, value) {
			if ( selected == values[key] ) $('#'+formId+' #fieldRow'+value).show();
		});
	});
	$(anchor).change();
}

function showTooltip(el)
{
	$('.dropdown-menu').parent('.open').toggleClass('open');

	var tooltip = el;
	if ( !tooltip.data('popover').enabled ) return;

	if ( typeof tooltip.attr('loaded') != 'undefined' ) {
		$('.popover').toggleClass('in').remove();
		if ( tooltip.parents('.open').length < 1 ) {
			tooltip.data('popover').show();
			var parent = tooltip.parents('.board_item_body');
			if ( parent.length > 0 ) {
				var popover = tooltip.data('popover');
				popover.tip().addClass('in-focus');
				if ( tooltip.offset().left < $(window).width() / 2 ) {
					popover.tip().css({
						'left': parent.length > 0 ? parent.offset().left + parent.width() : tooltip.offset().left + tooltip.width()
					});
				}
			}
		}
		return;
	}

	$.ajax({
		url: tooltip.attr('info'),
		dataType: 'html',
		error: function (xhr, status, error) {
			if (xhr.status === 0) return;
			tooltip.attr('data-content', ajaxErrorExplain(xhr, error));
		},
		success: function (data, status, xhr) {
			if (xhr.getResponseHeader('status') == '500') {
				window.location = '/500';
			}
			if (xhr.getResponseHeader('status') == '404') {
				return;
			}

			tooltip.attr('data-content', data);
			tooltip.attr('loaded', 'true');

			var popover = tooltip.data('popover');
			if (typeof popover != 'undefined') {
				popover.tip().find('.popover-content').css({
					'width': $(window).width() / 3
				});
			}
			if (tooltip.is(':hover') && tooltip.parents('.open').length < 1) {
				$('.popover').toggleClass('in').remove();
				tooltip.data('popover').show();
				var parent = tooltip.parents('.board_item_body');
				if ( parent.length > 0 ) {
					popover.tip().addClass('in-focus');
					if ( tooltip.offset().left < $(window).width() / 2 ) {
						popover.tip().css({
							'left': parent.length > 0 ? parent.offset().left + parent.width() : tooltip.offset().left + tooltip.width()
						});
					}
				}
			}
		}
	});
}

function showSelectedCards()
{
	$('.board_item_body input[type=checkbox]').each(function() {
		if ( this.checked ) {
			$(this).parents('.board_item_body').addClass('selected');
		} else {
			$(this).parents('.board_item_body').removeClass('selected');
		}
	});
}

function bindTabHandler( tabname, handler )
{
	$(document).on('tabsactivated', function(event, e,ui) {
		if ( $(ui.tab).attr('href').indexOf(tabname) > -1 ) {
			handler();
		}
	});
	$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
		if ( $(e.target).attr('href').indexOf(tabname) > -1 ) {
			handler();
		}
	});

}

function showTraces(attribute) {
	window.location = '?export=traces&ids=' + getCheckedRows() + '&attribute=' + attribute;
}

function openURLItems(url) {
	window.location = url.replace('%ids%',getCheckedRows());
}

function toggleMasterDetails() {
	$('div.table-details').toggle();

	cookies.set('toggle-detailspanel-' + $('.table-master table[uid]').attr('uid'), $('div.table-details').is(':visible'));
	if ( $('div.table-details').is(':visible') ) {
		$('div.table-master').addClass('details-visible');
		detailsRefresh('');
	}
	else {
		$('div.table-master').removeClass('details-visible');
	}
}

var detailsOptions = {
	setHeight: function() {
		var details = this.el.parents('.page-details');
		var master = $('div.table-master');
		details.css('min-height', Math.max(master.height(),details.height()));
		master.parents('table').css('min-height', Math.max(master.height(),details.height()));
	}
};
function detailsInitialize( el, url, visible, doRefresh )
{
	detailsOptions.url = url;
	detailsOptions.el = el;
	detailsOptions.refresh = doRefresh;
	detailsOptions.setHeight();
	if ( visible ) {
		detailsRefresh('');
	}
}

function detailsRefresh( parms )
{
	if ( detailsOptions.waitRequest ) {
		detailsOptions.waitRequest.abort();
		detailsOptions.waitRequest = null;
	}
	if ( parms == '' && detailsOptions.el ) {
		detailsOptions.el.html('<div class="document-loader"></div>');
	}
	detailsOptions.waitRequest = $.ajax({
		type: "GET",
		url: detailsOptions.url + parms,
		async: true,
		cache: false,
		dataType: "html",
		success: function(data) {
			if ( detailsOptions.el ) {
				detailsOptions.el.html(data);
				detailsOptions.setHeight();
				completeUIExt(detailsOptions.el);
			}
		},
		complete: function(xhr, textStatus) {
			if ( textStatus == "abort" || xhr.responseText == "" || $.inArray(xhr.status, [302,301,500,404]) != -1 ) {
				detailsOptions.el.html('<div class="document-empty"></div>');
				return;
			}
			if ( !detailsOptions.refresh ) return;

			setTimeout( function() {
				detailsRefresh('&wait=true');
			}, $.inArray(textStatus, ["error","timeout","parsererror"]) < 0 ? 100 : 180000);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			if ( detailsOptions.el ) {
				detailsOptions.el.html('<div class="document-empty"></div>');
			}
			if ( !detailsOptions.refresh ) return;

			setTimeout( function() {
				detailsRefresh('&wait=true');
			}, 180000);
		}
	});
}

function dropdownMenuPosition(e, $menu) {
	var mouseX = e.clientX
		, mouseY = e.clientY
		, boundsX = $(window).width()
		, menuWidth = $menu.find('.dropdown-menu').outerWidth()
		, tp = {}
		, Y
		, X;
	if ((mouseX + menuWidth > boundsX) && ((mouseX - menuWidth) > 0)) {
		X = {"left": mouseX - menuWidth};
	} else {
		var alignTo = $menu.prevAll('.btn-group');
		if ( alignTo.length > 0 ) {
			X = {"left": Math.min(mouseX,alignTo.offset().left)};
			Y = {"top": Math.max(mouseY,alignTo.offset().top + alignTo.height())};
		}
	}
	return $.extend(tp, X, Y);
}

function switchMenuState(state) {
	cookies.set('menu-state', state);
	window.location.reload();
}

function adjustContainerHeight(menu) {
	if ( $('.content-internal').height() < menu.height() ) {
		$('.content-internal').css('min-height',menu.height());
	}
}

function saveReportSettings() {
	$('li[uid=personal-persist]>a').addClass('checked');
	window.location = $('li[uid=personal-persist]>a[href]').length > 0
		? $('li[uid=personal-persist]>a').attr('href')
		: $('li[uid=personal-persist]>a').attr('onkeydown');
}

function getDialogWidth() {
	var scale = $('#modal-form').find('#tab-main .control-column').length < 3 ? 3/5 : 4/5;
	if ( $('#modal-form').find('.source-text').length > 0 ) scale = 5/6;
	return Math.max(Math.min(950,$(window).width() * 0.9), $(window).width()*scale);
}

var pageVisibility = (function(){
	var stateKey, eventKey, keys = {
		hidden: "visibilitychange",
		webkitHidden: "webkitvisibilitychange",
		mozHidden: "mozvisibilitychange",
		msHidden: "msvisibilitychange"
	};
	for (stateKey in keys) {
		if (stateKey in document) {
			eventKey = keys[stateKey];
			break;
		}
	}
	return function(c) {
		if (c) document.addEventListener(eventKey, c);
		return !document[stateKey];
	}
})();

function enterKeyUp(e) {
	e.which = e.which || e.keyCode;
	return e.which == 13;
}