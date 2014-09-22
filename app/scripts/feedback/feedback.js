/*
*
*/

var feedbackOpts = {
	tagWidth: 17,
	tagHeight: 105,
	tagBackground: 'blue',
	tagTextColor: 'white',
	tagBorder: '',
	tagImage: 'feedback.gif',
	fontFamily: 'tahoma',
	fontSize: '8pt',
	formBackground: '#375FD1',
	formColor: 'white',
	formWidth: 500,
	formTitle: 'Помогите нам сделать проект лучше: предложите идею, добавьте пожелание или сообщите об ошибке.',
	bugTitle: 'Сообщить об ошибке',
	bugDescription: 'Опишите найденную ошибку, мы постараемся исправить ее как можно скорее',
	issueTitle: 'Предложить доработку',
	issueDescription: 'Расскажите нам какой функиональности не хватает на ваш взгляд, предлагайте свои идеи, вместе мы сделаем продукт лучше',
	issueSuccess: 'Пожелание успешно добавлено и доступно по ссылке',
	questionTitle: 'Задать вопрос разработчикам',
	questionDescription: 'Спрашивайте, не стесняйтесь, мы обязательно ответим на ваш вопрос',
	questionSuccess: 'Вопрос отправлен разработчикам и доступен по ссылке',
	buttonClose: 'Закрыть',
	buttonSubmit: 'Добавить',
	buttonNew: 'Очистить',
	fieldDescription: 'Описание',
	fieldFile: 'Файл',
	fieldEmail: 'Ваш email для обратной связи',
	fieldRefresh: 'Обновить',
	errorTimeout: 'Превышен таймаут ожидания ответа от сервера',
	errorRequest: 'Ошибка выполнения запроса к серверу',
	errorParse: 'Ошибка разбора результата, полученного с сервера',
	errorUnsaved: 'Введенная вами информация не была передана в проект. Если вы покините эту страницу, то потеряете введенные вами данные.',
	messageSuccess: 'Действие выполнено успешно',
	sourceVersion: '',
	authorEmail: '',
	maxAttachmentSizeBytes: 6291456,
	formState: ''
	};

var feedbackFrmOptions = {};

function addFeedback( project, caption, url )
{
	var opts = feedbackOpts;
	
	if ( $.browser.msie )
	{
		if ( $('body').css('background') == '' )
		{
			$('body').css({
	      		'background-image': 'url("/images/trans.gif")',
	      		'background-repeat': 'no-repeat',
	      		'background-attachment': 'fixed'
	    	});
		}
		
		tagDiv = '<div id="febt" '+
			'style="left: expression( ( ( ignoreMe2 = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ) ) + \'px\' );'+
			'top: expression( ( '+(($(window).height() - opts.tagHeight) / 2)+' + ( ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ) ) + \'px\' );">';
	}
	else
	{
		tagDiv = '<div id="febt">';
	}
	
	$(tagDiv+'<a id="felk" style="text-decoration:none;" href="javascript:;">'+
		'<img border=0 src="'+url+'/images/'+opts.tagImage+'"></a>'+
	  	'</div><form id="fefrm" action="" method="post" onsubmit="javascript: return false;"></form>').appendTo('body');
	
	var tag = $('#febt');

	tag.css( { 
		'border': opts.tagBorder,
		'width': opts.tagWidth + 'px',
		'height': opts.tagHeight + 'px',
		'text-align': 'center',
		'vertical-align': 'middle',
		'font-family': opts.fontFamily,
		'font-size': opts.fontSize,
		'opacity': '0.5'
		});
		
	if ( opts.tagBackground != '' )
	{
		tag.css( { 
			'background': opts.tagBackground,
			'color': opts.tagTextColor
			});
	}
		
	if ( $.browser.msie )
	{
		tag.css({'position':'absolute'});
	}
	else
	{
		tag.css({
			'top': ($(window).height() - opts.tagHeight) / 2,
			'left': '0px',
			'position': 'fixed'
		});
	}

	tag.hover(
		function(){tag.animate({opacity:"1"}, 200);}, 
		function(){tag.animate({opacity:"0.5"}, 200);});

	$('#felk').css({
		'color': opts.tagTextColor,
		'vertical-align': 'middle'	
		});
		
	$('#felk').attr('title', opts.formTitle);
	opts.formState = $('#fefrm').formSerialize();
	
	$('#felk').click(function() 
	{
		var frm = $('#fefrm');
		window.onbeforeunload = null;

		if ( opts.formState != frm.formSerialize() )
		{
			return;
		}

		if ( frm.is(':visible') && frm.html() != '' )
		{
			frm.hide(200);
			return;
		}

		$('#fefrm').focus();
		frm.hide();

		frm.css( {
			'width': opts.formWidth + 'px'
		});
		
		if ( opts.formBackground != '' )
		{
			frm.css( {
				'background': opts.formBackground,
				'color': opts.formColor
			});
		}
			
		if ( $.browser.msie )
		{
			frm.css({
				'position': 'absolute',
				'top': $('#febt').css('top').split('px')[0] - 100 + 'px',
				'left': opts.tagWidth + 'px'
			});
		}
		else
		{
			frm.css({
				'top': ($(window).height() - 250) / 2,
				'left': opts.tagWidth + 'px',
				'position': 'fixed'
			});
		}
		
		frm.html('<div id="fefrmbdy" style="text-align:left;padding:10px;">'+
			'<div id="fefrmttl" style="padding:5px 10px 20px 10px;"></div>'+
			'<div id="fefrmcnt">'+
			'<div style="padding:2px 10px 5px 10px;"><a id="feactbug" href="javascript:">'+opts.bugTitle+'</a></div>'+
			'<div style="padding:2px 10px 25px 10px;">'+opts.bugDescription+'</div>'+
			'<div style="padding:2px 10px 5px 10px;"><a id="feactenh" href="javascript:">'+opts.issueTitle+'</a></div>'+
			'<div style="padding:2px 10px 25px 10px;">'+opts.issueDescription+'</div>'+
			'<div style="padding:2px 10px 5px 10px;"><a id="feactqst" href="javascript:">'+opts.questionTitle+'</a></div>'+
			'<div style="padding:2px 10px 25px 10px;">'+opts.questionDescription+'</div>'+
			'<div style="padding:10px;text-align:right;"><input style="width:90px;" id="febtncls" type="submit" value="'+opts.buttonClose+'"></div>'+
			'</div>'+
			'</div>');

		$('#fefrmttl').html('<b><a target="_blank" '+
			';text-decoration:none;" href="'+url+'/main/'+project+'">' + caption + '</a></b> &nbsp; ' + opts.formTitle);
		
		if ( opts.formColor != '' )
		{
			$('#fefrmbdy a').css({'color': opts.formColor});
		}
		
		$('#feactenh').click(function() {
			switchIssueFeedback( frm, project, url, 'enhancement' );
		});

		$('#feactbug').click(function() {
			switchIssueFeedback( frm, project, url, 'bug' );
		});

		$('#feactqst').click(function() {
			switchQuestion( frm, project, url );
		});

		$('#febtncls').click(function() {
			frm.hide(200);
		});

		frm.css({
			'top': ($(window).height() - frm.height() - 25) / 2
		});

		feedbackFrmOptions = 
		{
			dataType: 'jsonp',
			beforeSubmit: function( a, o, f ) {},
			complete: function ( xhr, state ) {},
			error: parseError,
			success: function ( result ) 
			{
				displayResult( result );

				frm.css({
					'top': ($(window).height() - frm.height() - 25) / 2
				});
			}
		};

		frm.show(200);
	});
}

function displayResult( result )
{
	var opts = feedbackOpts;

	if ( typeof result.issue != 'undefined' )
	{
		var result = '<div style="padding:10px;">'+opts.issueSuccess+
			' <a target="_blank" href="'+result.issue+'">'+result.issue+'</a>';
		
		result += '</div>';
		$('#fersph').html(result);
	}
	else if ( typeof result.question != 'undefined' )
	{
		var result = '<div style="padding:10px;">'+opts.questionSuccess+
			' <a target="_blank" href="'+result.question+'">'+result.question+'</a>';
		
		result += '</div>';
		$('#fersph').html(result);
	}
	else if ( typeof result.error != 'undefined' )
	{
		$('#fersph').html('<div style="padding:10px;">'+
			result.error+'</div>');
	}
	else if ( typeof result.message != 'undefined' )
	{
		$('#fersph').html('<div style="padding:10px;">'+
			result.message+'</div>');
	}
	else
	{
		var resp = eval(result);
		
		if ( typeof resp.issue != 'undefined' || typeof resp.question != 'undefined' || typeof resp.error != 'undefined'  )
		{
			displayResult( resp );
			return;
		}
		else
		{
			$('#fersph').html('<div style="padding:10px;">'+
				result+'</div>');
		}
	}

	$('#febtnsub').attr('disabled', '');
	$('#febtncls').attr('disabled', '');

	$('#febtnnew').show();
	$('#febtnsub').hide();
	
	window.onbeforeunload = null;
}

function switchIssueFeedback( frm, project, url, kind )
{
	var opts = feedbackOpts;
	
	body = '<div style="padding:2px 10px 2px 10px;">'+opts.fieldDescription+'</div>'+
		'<div style="padding:5px 10px 5px 10px;"><textarea name="description" id="feisdes" rows="5"></textarea></div>';
	
	body += '<input type="hidden" name="kind" value="'+kind+'">'+
		'<div id="feemph"></div>'+
		'<div id="feqsph"></div>'+
		'<div id="fersph"></div>'+
		'<div style="padding:10px;text-align:right;"><input style="width:90px;display:none;" id="febtnnew" type="button" value="'+opts.buttonNew+'">'+
		'<input style="width:90px;" id="febtnsub" type="button" value="'+opts.buttonSubmit+'"> '+
		'&nbsp; <input style="width:90px;" id="febtncls" type="button" value="'+opts.buttonClose+'"></div>';
	
    $('#fefrmcnt').html(body);

	getQuestion(project, url);
	getCommon();
	
	$('#feisdes').focus();

	$('#febtnnew').click(function() {
		switchIssueFeedback ( frm, project, url, kind );
	});

    $('#feisfil').css('width', opts.formWidth - 40);
    $('#feisdes').css('width', opts.formWidth - 40);

	feedbackFrmOptions.beforeSubmit = function( a, object, options ) 
	{
		$('#febtnsub').attr('disabled', true);
		$('#febtncls').attr('disabled', true);
		
		if ( $('#feisdes').val() == '' )
		{
			$('#feisdes').fadeOut(0, function(){ $('#feisdes').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $('#feisdes').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return false;
		}

		if ( opts.authorEmail == '' && $('#feiseml').val() == '' )
		{
			$('#feiseml').fadeOut(0, function(){ $('#feiseml').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $('#feiseml').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return false;
		}
		
		if ( $('#feisans').val() == '' )
		{
			$('#feisans').fadeOut(0, function(){ $('#feisans').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $('#feisans').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return false;
		}
	};

	$('#fefrm').attr('action', url+"/issue/"+project);
	$('#fefrm').ajaxForm(feedbackFrmOptions);
	
	$('#fefrm').css({
		'top': ($(window).height() - frm.height() - 25) / 2
	});

	opts.formState = $('#fefrm').formSerialize();
}

function switchQuestion( frm, project, url )
{
	var opts = feedbackOpts;

    $('#fefrmcnt').html(
		'<div style="padding:2px 10px 2px 10px;">'+opts.fieldDescription+'</div>'+
		'<div style="padding:5px 10px 5px 10px;"><textarea name="caption" id="feiscap" rows="5"></textarea></div>'+
		'<div id="feemph"></div>'+
		'<div id="feqsph"></div>'+
		'<div id="fersph"></div>'+
		'<div style="padding:10px;text-align:right;"><input style="width:90px;display:none;" id="febtnnew" type="button" value="'+opts.buttonNew+'">'+
		'<input style="width:90px;" id="febtnsub" type="button" value="'+opts.buttonSubmit+'"> '+
		'&nbsp; <input style="width:90px;" id="febtncls" type="button" value="'+opts.buttonClose+'"></div>'
	);
    
    $('#feiscap').css({'width': opts.formWidth - 40});

	getQuestion(project, url);
	getCommon();
	
	$('#feiscap').focus();

	$('#febtnnew').click(function() {	
		switchQuestion ( frm, project, url );
	});

	feedbackFrmOptions.beforeSubmit = function( a, object, options ) 
	{
		$('#febtnsub').attr('disabled', true);
		$('#febtncls').attr('disabled', true);
		
		if ( $('#feiscap').val() == '' )
		{
			$('#feiscap').fadeOut(0, function(){ $('#feiscap').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){$('#feiscap').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return;
		}
		
		if ( opts.authorEmail == '' && $('#feiseml').val() == '' )
		{
			$('#feiseml').fadeOut(0, function(){ $('#feiseml').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $('#feiseml').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return;
		}
		
		if ( $('#feisans').val() == '' )
		{
			$('#feisans').fadeOut(0, function(){ $('#feisans').css('background', '#ffafaf');} ).
				fadeIn().fadeOut().fadeIn(300, function(){ $('#feisans').css('background', 'white');} );

			$('#febtnsub').attr('disabled', '');
			$('#febtncls').attr('disabled', '');
			
			return;
		}
	};

	$('#fefrm').attr('action', url+"/ask/"+project);
	$('#fefrm').ajaxForm(feedbackFrmOptions);

	frm.css({
		'top': ($(window).height() - frm.height() - 25) / 2
	});

	opts.formState = $('#fefrm').formSerialize();
}

function getCommon()
{
	var opts = feedbackOpts;

	if ( opts.authorEmail == '' )
	{
		$('<div style="padding:2px 10px 2px 10px;">'+opts.fieldEmail+'</div>'+
		  '<div style="padding:5px 10px 5px 10px;"><input name="author" id="feiseml" style="width:100%;"></div>').appendTo('#feemph');
	}
	else
	{
		$('<input name="author" type="hidden" value="'+opts.authorEmail+'">').appendTo('#feemph');
	}
	
	if ( opts.sourceVersion != '' )
	{
		$('<input name="version" type="hidden" value="'+opts.sourceVersion+'">').appendTo('#feemph');
	}

	$('#febtncls').click(function() 
	{
		$('#fefrm').hide(200);
		$('#fefrm').html('');

		opts.formState = '';
		window.onbeforeunload = null;
	});

	$('#febtnsub').click(function() {
		$('#fefrm').submit();
	});
}

function feedbackCheckUnsaved()
{
	var opts = feedbackOpts;

	if ( opts.formState = $('#fefrm').formSerialize() )
	{
		return opts.errorUnsaved;
	}
}

function getQuestion(project, url)
{
	var opts = feedbackOpts;

	$.ajax({
		type: "GET",
		url: url+"/feedback/"+project+"/auth/question",
		dataType: "jsonp",
		success: 
			function(result) {
				$('#feqsph').html(
					'<input name="hash" type="hidden" id="feishash" value="'+result.hash+'">'+
					'<div style="padding:2px 10px 2px 10px;width:100%;">'+result.caption+
						' &nbsp; &nbsp; <a tabindex="-1" style="color:'+feedbackOpts.formColor+'" href="javascript: getQuestion(\''+url+'\');">'+opts.fieldRefresh+'</a></div>'+
					'<div style="padding:5px 10px 5px 10px;"><input name="answer" id="feisans" style="width:100%;"></div>');

				window.onbeforeunload = feedbackCheckUnsaved;
			}
	});
}

function parseError( XMLHttpRequest, textStatus, errorThrown )
{
	var opts = feedbackOpts;
	var result = '';
	
	switch ( textStatus )
	{
		case 'timeout':
			result = opts.errorTimeout;
			break;
		case 'error':
			if ( XMLHttpRequest.statusText == 'n/a' )
			{
				displayResult({message:opts.messageSuccess});
				return;
			}
			else
			{
				result = opts.errorRequest;
				break;
			}
		case 'parseError':
			result = opts.errorParse;
			break;
	}

	$('#fersph').html(
		'<div style="padding:10px 10px 3px 10px;">'+result+'</div>'+
		'<div style="padding:10px 10px 3px 10px;">URL: '+this.url+'</div>'+
		'<div style="padding:10px 10px 10px 10px;">Response: ['+
			XMLHttpRequest.statusText+'] '+errorThrown+'</div>'
	);

	$('#febtnsub').attr('disabled', '');
	$('#febtncls').attr('disabled', '');
}