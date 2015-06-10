var project = '';
var object = '';
var objectid = '';

function initComments( proj, obj, obid )
{
	project = proj;
	object = obj;
	objectid = obid;
	
	$('#comments'+objectid).html('<div class="combody" id="combody'+objectid+'"></div>');

	getComments();
}

function updateLocation( component, original )
{
	if ( component == '' ) return original;

	var parms = component.split('=');
	var location = original;
	
	if ( parms[1] == '' )
	{
		location = location.replace(new RegExp(parms[0]+'=[^\\&]*\\&?', 'i'), ''); 
	}
	else
	{
		var re = new RegExp('\\?'+parms[0]+'=[^\\&]*', 'gi');
		var match = re.exec( location );
		
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

function getComments()
{
	if ( $('#combody'+objectid).html() == '' )
	{
		$('#combody'+objectid).html('<img src="/images/ajax-loader.gif">');
	}
	
	$.ajax({
		type: "GET",
		url: '/comment/'+project+'/object='+object+'&id='+objectid,
		dataType: "html",
		async: true,
		success: 
			function(result) 
			{
				$('#comment0').html('');
				$('#combody'+objectid).html(result);
				
 				var locstr = new String(decodeURI(window.location));
				if ( locstr.indexOf('#comment') > 0 )
				{
					window.scrollTo( 0, $('#combody'+objectid).position().top );
				}
			},
		error: 
			function(result)
			{
				$('#comment0').html('');
				$('#combody'+objectid).html('');
			}
	});
}

function getPostComment( comment )
{
	$('.postreply').hide();
	$('#reply'+comment).hide();
	
	$('#comment'+comment).html('<div style="clear:both;padding-bottom:6px;"><textarea id="comtext'+comment+'" rows=5></textarea></div>'+
		'<div class="blackbutton"><div id="body">'+
		'<a id="post'+comment+'" href="">Отправить</a></div><div id="rt"></div></div> '+
		'<div class="blackbutton"><div id="body">'+
		'<a id="close'+comment+'" href="">Закрыть</a></div><div id="rt"></div></div>');
	
	$('#post'+comment).click( function() {
		var text = $('#comtext'+comment).val();
		if ( text == '' ) return false;
		
		$('#comment'+comment).html('<img src="/images/ajax-loader.gif">');
		
		if ( comment == 0 )
		{
			postComment( object, objectid, text );
		}
		else
		{
			postComment( 'Comment', comment, text );
		}

		return false;
	});
	
	$('#close'+comment).click( function() {
		$('#comment'+comment).hide();
		$('#reply'+comment).show();

		return false;
	});

	$('#comment'+comment).show();
}

function postComment( object, id, text )
{
	$.ajax({
		type: "POST",
		url: '/comment/'+project+'/object='+object+'&id='+id,
		data: { 'text': text },
		dataType: "html",
		async: true,
		success: 
			function(result) 
			{
				getComments();
			},
		error: 
			function(result)
			{
				getComments();
			}
	});
}

function submitForm( action, successhandler )
{
	if ( $('#submit').css('display') == 'none' )
	{
		return;
	}

	$('#action').val( action );

	$('#myForm').ajaxForm({
		dataType: 'html',
		beforeSubmit: function(a,f,o) 
		{
			$('#result').html('<img src="/images/ajax-loader.gif">');
			
			$('.blackbutton').hide();
			$('#submit').hide();
		},
		error: function( xhr ) 
		{
			$('.blackbutton').show();
			$('#submit').show();
			
			$('#result').html('<div class="error">'+xhr.error+'</div>');
		},
		success: function( data ) 
		{
			data = jQuery.parseJSON(data);
			
			$('#result').html('');

			if ( typeof data != 'object' ) return;

			var state = data.state;
			var message = data.message;
			var objectid = data.object;
			
			if ( state == 'redirect' )
			{
				if ( message != '' )
				{
					$('#result').html('<div class="success">'+message+'</div>');
					
					setTimeout( function() {
						window.location = data.object;
					}, 2000);
				}
				else
				{
					window.location = data.object;
				}				
				
				return;
			}
			
			$('#result').html('<div class="'+state+'">'+message+'</div>');
			$('#result_bottom').html('<div class="'+state+'">'+message+'</div>');

			if ( $('#loginform').css('height') == 'auto' )
			{
				$('#loginform').css({
					'top': ($(window).height() - $('#loginform').height()) / 2
				});
			}

			if ( state != 'success' )
			{
				$('.blackbutton').show();
				$('#submit').show();
			}
			else
			{
				$('#myForm').ajaxFormUnbind();
				
				if ( typeof successhandler != 'undefined' )
				{
					successhandler();
				}
			}
		}
	});
		
	$('#myForm').submit();
}

function openMenu( id, height, width, extent )
{
	if ( $('#'+id+' .menucontent').css('display') == 'none' )
	{
		$('#'+id+' .submenu').animate( {'width':width + extent}, 100, 'linear',
			function() {
				$('#'+id+' .btmr').css({'width': width + extent - 10});
				$('#'+id+' .menucontent').animate( {'height':height,'width':width + extent - 10}, 200).show();
		});
	}
	else
	{
		$('#'+id+' .menucontent').hide();
		$('#'+id+' .submenu').animate( {'width':width}, 200);
	}
}

function userMenu( height, width )
{
	var width = Math.max($('#usermenu .submenu').width(), 100);
	openMenu('usermenu', height, width, 0 );
}

function mainMenu( height, width )
{
	openMenu('mainmenu', height, width, 50 );
}

function actionMenu( height, width )
{
	openMenu('actionmenu', height, width, 137 );
}

function disableUI()
{
	if ( $('#loginbg').css('position') != 'relative' )
	{
		$('#loginbg').css({
			'opacity': '0.5'
		});
		$('#loginbg').show();
	}

	if ( $('#loginform').css('position') != 'relative')
	{
		$('#loginform').css({
			'top': ($(window).height() - 32) / 2,
			'left': ($(window).width() - 32) / 2,
			'width': 32,
			'height': 32
		});

		if ( $.browser.msie )
		{
			$('#loginform').css({'position':'absolute', 'z-index':'2'});
		}
		else
		{
			$('#loginform').css({'position': 'fixed'});
		}
	}

	$('#loginform').html('<img src="/images/ajax-loader.gif">');
	$('#loginform').show();
}

function showForm( result, width, heigth )
{
	$('#loginform').hide();

	if ( $('#loginform').css('position') != 'relative')
	{
		if ( $.browser.msie )
		{
			$('#loginform').css({'position':'absolute', 'z-index':'3'});
		}
		else
		{
			$('#loginform').css({'position': 'fixed'});
		}
	}
	
	$('#loginform').html(result);

	$('#myForm table').css( {'font-size': '18px'} );
	$('#loginform').show();
	
	if ( $('#loginform').css('position') != 'relative')
	{
		$('#loginform').css({'height': 'auto', 'width': width});

		$('#loginform').css({
			'top': ($(window).height() - $('#loginform').height()) / 2,
			'left': ($(window).width() - width) / 2
		});
	}
	
}

function getLoginForm( url )
{
	disableUI();
	
	$('#loginbg').html( '<input id="globlru" type="hidden" value="">'+$('#loginbg').html());
	if ( typeof url != 'undefined' )
	{
		$('#globlru').val( url );
	}

	$.ajax({
		type: "GET",
		url: '/loginfrm',
		dataType: "html",
		success: 
			function(result) 
			{
				showForm ( result, 420, 380 );
				
				if ( $('#lru').attr('type') == 'hidden' )
				{
					if ( $('#globlru').val() == '' || $('#globlru').val().indexOf('javascript') > -1 )
					{
						$('#lru').val( window.location );
						$('#lrs').val( $('#globlru').val() );
					}
					else
					{
						$('#lru').val( $('#globlru').val() );
					}
				}
			},
		error: 
			function(result)
			{
			}
	});
}

function getJoinForm(url)
{
	disableUI();
	
	var data = { Email: '' };
	
	if ( email != null && typeof email != 'undefined' )
	{
		data.Email = email;
	}
	
	if ( typeof url != 'undefined' )
	{
		$('#globlru').val( url );
	}
	
	$.ajax({
		type: "GET",
		url: '/join',
		data: data,
		dataType: "html",
		success: 
			function(result) 
			{
				showForm ( result, 410, 490 );
			},
		error: 
			function(result)
			{
			}
	});
}

function getRestoreForm(key)
{
	disableUI();
	
	$.ajax({
		type: "GET",
		url: '/restore',
		dataType: "html",
		data: { key: key },
		success: 
			function(result) 
			{
				showForm ( result, 410, 290 );
			},
		error: 
			function(result)
			{
			}
	});
}

function getRestoreRequestForm()
{
	disableUI();
	
	$.ajax({
		type: "GET",
		url: '/restorerequest',
		dataType: "html",
		success: 
			function(result) 
			{
				showForm ( result, 410, 190 );
			},
		error: 
			function(result)
			{
			}
	});
}

function prepareToRestore()
{
	$('#submitbutton').show();
	$('#submit').show();
	$('#submit').attr('href', 'javascript: getRestoreForm(\'\');');
}

function getJoinSimpleForm()
{
	disableUI();
	
	$.ajax({
		type: "GET",
		url: '/joinsimple',
		dataType: "html",
		success: 
			function(result) 
			{
				showForm ( result, 410, 490 );
				
				$('#agreementField').hide();
				$('#agr').val('yes');
			},
		error: 
			function(result)
			{
			}
	});
}
 
function authorizedDownload( url )
{
	disableUI();
	
	$('#loginbg').html( '<input id="loginRedirectUrl" type="hidden" value="1">'+$('#loginbg').html());
	$('#loginRedirectUrl').val(url);

	$.ajax({
		type: "GET",
		url: '/downloads',
		dataType: "html",
		success: 
			function(result) 
			{
				if ( result != '' )
				{
					showForm ( result, 580, 380 );
				}
				else
				{
					closeLoginForm();
					refreshWindow();
				}
			},
		error: 
			function(result)
			{
			}
	});
}

function getLicense( url )
{
	disableUI();
	
	$('#loginbg').html( '<input id="loginRedirectUrl" type="hidden" value="1">'+$('#loginbg').html());
	$('#loginRedirectUrl').val(url);

	$.ajax({
		type: "GET",
		url: '/joinlicense',
		dataType: "html",
		success: 
			function(result) 
			{
				if ( result != '' )
				{
					showForm ( result, 580, 380 );

					$('#lru').val(url);
				}
				else
				{
					closeLoginForm();
					
					refreshWindow();
				}
			},
		error: 
			function(result)
			{
			}
	});
}

function refreshWindow()
{
	var url = $('#globlru').val();
	if ( typeof url != 'undefined' && url != '' )
	{
		closeLoginForm();
		window.location = url;
	}
	else
	{
		var url = $('#loginRedirectUrl').val();
		if ( typeof url != 'undefined' && url != '' )
		{
			closeLoginForm();
			window.location = url;
		}
		else
		{
			window.location.reload();
		}
	}
}

function closeLoginForm()
{
	$('#loginform').html('');
	$('#loginform').hide();
	$('#loginbg').hide();
}

function logoff()
{
	disableUI();

	$.ajax({
		type: "GET",
		url: '/logoff',
		dataType: "html",
		success: 
			function(result) 
			{
				refreshWindow();
			},
		error: 
			function(result)
			{
				refreshWindow();
			}
	});
}

function storeCaret(textEl) 
{
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}
	
function countLineBreaks(textarea)
{
	var area = textarea;

	textAreaWith = area.clientWidth == 0 ? area.offsetWidth : area.clientWidth;
	nCols = Math.ceil(textAreaWith / 6.7);

	lines = textarea.value.split('\n');
 	nRowCnt = lines.length; 
		
	for(i=0;i<lines.length;i++) {
		wrapLine = Math.ceil(lines[i].length / nCols);
		if(wrapLine > 1) nRowCnt += (wrapLine - 1); 
	}
			
	if(nRowCnt < 3) nRowCnt = 3;
	if (typeof ActiveXObject != 'undefined') {
		nRowCnt += 7;
	}
	return nRowCnt;
} 

function adjustRows (textarea) 
{
	textarea.rows = countLineBreaks(textarea) + 1;
}		

function choosebutton( id )
{
	if ( $("div[name='but"+id+"']").attr('class') == 'active' )
	{
		$("div[name='but"+id+"']").attr('class', 'nonactive');
		$("div#desc"+id).hide();
	}
	else
	{
		$("div[name='but"+id+"']").attr('class', 'active');
		$("div#desc"+id).show(200);
	}
}

function searchProject()
{
	window.location = '/projects/search/'+
		encodeURIComponent($("#search input").val());
}

var timeout = 500;
var closetimer = 0;
var ddmenuitem = 0;
var ddrootitem = 0;

function dropdown_open()
{  
	dropdown_canceltimer();
	dropdown_close();
	ddrootitem = $(this).find('a#wmtp').attr('class', 'open');
	$(this).find('a.last').width($(this).find('a.first').width() - ($.browser.msie ? -2 : 10));
	ddmenuitem = $(this).find('ul').css('visibility', 'visible');
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

function startopenid( id, moveCaret )
{
    $.fn.selectRange = function(start, end) {
        return this.each(function() {
                if(this.setSelectionRange) {
                        this.focus();
                        this.setSelectionRange(start, end);
                } else if(this.createTextRange) {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', start);
                        range.select();
                }
        });
	};

	$('#openid').val(id);
	$('#openid').focus();
	
	if ( typeof moveCaret == 'undefined' || moveCaret )
	{
		$('#openid').selectRange(0, 0);
	}
}

function startdemo( template )
{
	var url = 'http://devprom.ru/module/saasassist/create';
	
	if ( template != '' ) {
		url += '?template='+ template;
	}
		
	setTimeout(function() { window.location = url; }, 300);
}

function createinstance()
{
	_gat._getTracker("UA-10541243-1")._trackEvent('demo-start', 'blank', 'quick-form');

	$('#try-form-result').hide();
	$('#try-form input, #try-form button').attr('disabled', '');
    $('#try-form button').addClass('loading');
    $('.disable-block').fadeIn();
    var interval = setInterval( function() {
    	$('#try-form .form-group').hide();
    	$('#try-form .form-message').css('display','inline-block');
    }, 4000);
    
	$.ajax({
		type: "GET",
		url: '/co/command.php?class=createinstance&namespace=saasassist&action=1',
		data: {
			instance: $('#try-form #try-form-instance').val(),
			email: $('#try-form #try-form-email').val(),
			username: $('#try-form #try-form-username').val()
		},
		dataType: "html",
		success: 
			function(result) {
				try {
					data = jQuery.parseJSON(result);
					if ( data.state == 'error' ) {
						$('#try-form-result').html(data.message).show();
						$('#try-form input, #try-form button').removeAttr('disabled');
					    $('#try-form button').removeClass('loading');
					    $('.disable-block').fadeOut();
					    clearInterval(interval);
						return;
					}
					window.location = data.object;
				}
				catch( e ) {
		 			$('#try-form-result').html(result).show();
		 			$('#try-form input, #try-form button').removeAttr('disabled');
		 		    $('#try-form button').removeClass('loading');
		 		    $('.disable-block').fadeOut();
		 		   clearInterval(interval);
				}
			},
		error: 
			function(xhr, status, error)
			{
				$('#try-form-result').html(error).show();
				$('#try-form input, #try-form button').removeAttr('disabled');
				$('#try-form button').removeClass('loading');
	 		    $('.disable-block').fadeOut();
	 		   clearInterval(interval);
			}
	});
}