/*
 * Content Management System
 * corecms.js
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <saveug@mail.ru>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

function drawFrame( placeholder )
{
	$.ajax({
		type: "GET",
		url: globalFrames[placeholder],
		dataType: "html",
		success: 
			function(result) 
			{
				$('#'+placeholder).html(result);
			}
	});
}

function webmethod( item, frames, redirect, warning )
{
	if( warning != '' && !confirm(warning) ) return;
	
	$.ajax({
		type: "POST",
		url: encodeURI($('#'+item).attr('rel')),
		dataType: "html",
		async: false,
		success: 
			function(result) 
			{
				if ( frames.length > 0 )
				{
					for ( i = 0; i < frames.length; i++ )
					{
						drawFrame(frames[i]);
					}
				}
				else
				{
					if ( redirect != '' )
					{
						window.location = encodeURI(jQuery.trim(redirect + result));
					}
					else
					{
						window.location.reload();
					}
				}
			},
		error: 
			function(result)
			{
				alert(result);
			}
	});
}