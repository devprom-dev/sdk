function uploadFiles(element)
{
    var project = element.attr('project');
    if ( !project ) project = devpromOpts.project;

    var flow = new Flow({
        target: '/pm/' + project + '/attachments/upload',
        testChunks: false,
        query:{
            objectClass: element.attr('objectclass'),
            objectId: element.attr('objectid'),
            attachmentClass: element.attr('attachmentClass')
        }
    });

    flow.assignBrowse(element.get());

    if ( element.parents('.file-drop-target').length > 0 ) {
        flow.assignDrop(element.parents('.file-drop-target').get());
    }

    flow.on("filesSubmitted", function(items, event) {
            flow.upload();
        });
    flow.on('fileSuccess', function(file,message){
            try {
                var fileid = $.base64.encode(file.uniqueIdentifier).replace(/=/g, '');
                fileInfo = jQuery.parseJSON(message);
                element.parents('form').append(
                    '<input type="hidden" unique="'+fileid+'" name="file:'+fileInfo['class']+':'+fileInfo['id']+'" value="new">'
                );
            }
            catch( e ) {
            }
        });
    flow.on("complete", function() {
        if ( element.attr("post-action") == "refresh" ) {
            devpromOpts.updateUI();
        }
    });
    flow.on("fileAdded", function(file, chunk)
    {
        var fileid = $.base64.encode(file.uniqueIdentifier).replace(/=/g, '');
        if ( $('#' + fileid).length > 0 ) return;

        element.parent().parent().find('.attachment-items').append(
            '<span id="'+fileid+'" class="badge-file"><a class="att-act" href="">&#10006;</a> '+file.name+' </span>'
        );
        $('#' + fileid).circleProgress({
            value: 0,
            thickness: 4,
            animation: false,
            size: 25
        });
        $('#' + fileid + ' canvas').click(function() {
            flow.removeFile(file);
            $(this).parent().remove();
            $('input[unique="'+fileid+'"]').remove();
        });
    });
    flow.on("fileProgress", function(file, chunk)
    {
        var fileid = $.base64.encode(file.uniqueIdentifier).replace(/=/g, '');
        $('#' + fileid).circleProgress({
            value: chunk.endByte / file.size,
            thickness: 4,
            animation: false,
            size: 25,
            fill: '#1ee044'
        });
    });
}

function filesFormDelete( id, className ) {
    $('form').append(
        '<input type="hidden" name="file:'+className+':'+id+'" value="delete">'
    );
    $('form .badge-file[objectid="'+id+'"][objectclass="'+className+'"]').css('text-decoration','line-through');
}