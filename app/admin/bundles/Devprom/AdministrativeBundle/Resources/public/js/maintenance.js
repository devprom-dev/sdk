function downloadUpdate()
{
	var url = window.location.protocol+"//devprom.ru/download?json&iid=%iid%";
	$.getJSON( url, function( data ) {
		$.each( data, function(key, value) {
			window.location = value.download_url + '&iid=%iid%';
			return false;
		});
	});	
}