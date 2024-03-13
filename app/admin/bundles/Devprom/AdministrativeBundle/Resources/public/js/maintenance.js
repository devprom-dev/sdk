function downloadUpdate()
{
    var payed = window.location.protocol+"//"+devpromOpts.serviceUrl+"/download?payed&iid=%iid%";
    var url = window.location.protocol+"//"+devpromOpts.serviceUrl+"/download?json&iid=%iid%";
    $.getJSON( payed, function( data ) {
        try {
            if ( !data.till || data.till < new Date().toJSON().split('T')[0] ) {
                reportError('%notpayed%');
                return;
            }
            $.getJSON( url, function( data ) {
                if ( data.length < 1 ) {
                    reportError('%error%');
                    return;
                }
                $.each( data, function(key, value) {
                    window.location = value.download_url + '&iid=%iid%';
                    return false;
                });
            })
            .error(function (xhr, status, error) {
                reportError(ajaxErrorExplain(xhr, error) + "\n\n" + url);
            });
        }
        catch(e) {
            alert('%error%');
        }
    })
    .fail(function (xhr, status, error) {
        reportError(ajaxErrorExplain(xhr, error) + "\n\n" + url);
    });
}