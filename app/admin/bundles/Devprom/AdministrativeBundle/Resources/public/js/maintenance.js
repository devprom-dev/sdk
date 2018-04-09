function downloadUpdate()
{
    var payed = window.location.protocol+"//devprom.ru/download?payed&iid=%iid%";
    $.getJSON( payed, function( data ) {
        try {
            if ( !data.till || data.till < new Date().toJSON().split('T')[0] ) {
                alert('%notpayed%');
                return;
            }
            var url = window.location.protocol+"//devprom.ru/download?json&iid=%iid%";
            $.getJSON( url, function( data ) {
                if ( data.length < 1 ) {
                    alert('%error%');
                    return;
                }
                $.each( data, function(key, value) {
                    window.location = value.download_url + '&iid=%iid%';
                    return false;
                });
            });
        }
        catch(e) {
            alert('%error%');
        }
    });
}