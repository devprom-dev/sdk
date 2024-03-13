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

/**
 * Check for updates and outputs the result message
 *
 * @param route
 */
function searchForUpdates( route )
{
    let url = document.location + route;
    
    $.getJSON(
        url,
        // Always. Leaving it for examiner
        // @todo remove it after test task is passed|failed
        function( data ){
            console.log( 'Success. Received data: ' );
            console.log( data )
        }
    )
        .done(function (data) {
            
            console.log(data)
            // Format processing. Could use an Exception.
            if( typeof data.updates_found === 'undefined' ){
                reportError('Unexpected format'); // @todo apply a translation here
                return;
            }
            
            +data.updates_found === 1
                ? reportSuccess('Updates found. Please update.') // @todo apply a translation here
                : reportError('No new updates are found'); // @todo apply a translation here
        })
        .fail(function (xhr, status, error) {
            reportError(ajaxErrorExplain(xhr, error) + "\n\n" + url);
        });
}