var annotaion = {
    popupTimeout: null,
};

function highlightComment() {
    var locstr = String(window.location);
    if ( locstr.indexOf('#comment') > 0 ) {
        var commentString = locstr.substring(locstr.indexOf('#comment'));
        var parts = commentString.split('#');
        var section = $('#'+parts[1]).parent('.comment-thread-container').attr('active', '');
    }
}

function filterComments() {
    var attributes = [];
    $('.comments-filter').each(function() {
        if ( $(this).attr('name') == 'order' ) {
            sortComments($(this).is(':checked') ? '1' : '-1');
            return true;
        }
        if ( $(this).is(':checked') ) {
            attributes.push('['+$(this).attr('name')+']');
        }
    });
    var selector = '.comment-thread-container[active],.comment-thread-container' + attributes.join('');
    $('.comment-thread-container').removeClass('in');
    $(selector).addClass('in').parents('.comment-thread-container').addClass('in');
}

function filterItemComments(item)
{
    $('.comment-well .collapse.in').removeClass('in');
    $('.comment-well .plus-minus-toggle').addClass('collapsed');
    var object = $('.comment-well[item='+item+']');
    if ( object.length < 1 ) return;
    object.find('.collapse:not(.in)').addClass('in');
    object.find('.plus-minus-toggle').removeClass('collapsed');
    $('.details-body').animate({
        scrollTop: object.offset().top
    }, 300);
}

function filterExactComment(item)
{
    $('.comment-well .collapse.in').removeClass('in');
    $('.comment-well .plus-minus-toggle').addClass('collapsed');
    var object = $('.comment-well[object-id='+item+']');
    if ( object.length < 1 ) return;
    object.find('.collapse:not(.in)').addClass('in').removeAttr('style').scrollTop(0);
    object.find('.plus-minus-toggle').removeClass('collapsed');
    $('.details-body').animate({
        scrollTop: object.offset().top
    }, 300);
}

function sortComments( sort )
{
    if ( sort != "-1") sort = "1";
    cookies.setOptions({expires:new Date(new Date().getFullYear() + 1, 1, 1)});
    cookies.set('sort-comments', sort);
    sortByModified('.comments-thread', sort);
    if ( sort == "-1" ) {
        $('.sort-btn-desc').hide();
        $('.sort-btn-asc').show();
    }
    else {
        $('.sort-btn-desc').show();
        $('.sort-btn-asc').hide();
    }
}

function annotatePopup()
{
    var s = window.getSelection();
    var oRange = s.getRangeAt(0);
    var oRect = oRange.getClientRects()[0];

    var pathItems = getDomPath(oRange.commonAncestorContainer.parentElement);
    var container = $(pathItems[0]);
    if ( !container.is('[project][objectclass][objectid]') ) return;
    if ( container.parents('.wiki-page-document').length < 1 ) return;

    $('body')
        .append('<div id="annopop" style="position: absolute;"/>');
    $('#annopop')
        .css('top', oRect.bottom)
        .css('left', oRect.left + oRect.width - 10 )
        .popover({
            content: '<button class="btn btn-large" onclick="javascript:annotateSelection();" title="'+text('sc-key-edt-comment')
                        +'"><i class="icon-comment"></i></button>',
            html: true,
            trigger: 'manual',
            placement: 'bottom'
        }).popover('show');
}

function annotateSelection()
{
    var s = window.getSelection();
    var oRange = s.getRangeAt(0);

    var pathItems = getDomPath(oRange.commonAncestorContainer.parentElement);
    var container = $(pathItems[0]);
    var path = pathItems.slice(1).join('>');
    var text = s.toString();

    $('#annopop').popover('hide');
    workflowNewObject(
        '/pm/' + container.attr('project') + '/comments/' + container.attr('objectclass') + '/' + container.attr('objectid'),
        'Comment',
        'Comment',
        '',
        {
            Caption: '<blockquote>'+text+'</blockquote> <p></p>',
            AnnotationPath: path,
            AnnotationText: text,
            AnnotationStart: oRange.startOffset,
            AnnotationLength: oRange.endOffset - oRange.startOffset
        },
        function(id) {
            container.find(path).markRanges([{
                start:oRange.startOffset,
                length:oRange.endOffset - oRange.startOffset
            }], {
                className: 'commid' + id
            });
        },
        true
    );
}

function makeupAnnotations(jqe, editable)
{
    var selector = '.wysiwyg[annotation]';
    if ( editable ) selector += '[contenteditable]';

    var containerList = jqe.find(selector).addBack(selector);
    containerList.each(function() {
        var container = $(this);
        try {
            var annotationJson = JSON.parse($(this).attr('annotation'));
            $.each(annotationJson, function(i,j) {
                if ( j.t != '' ) {
                    container.mark(j.t, {
                        separateWordSearch: false,
                        accuracy: 'exactly',
                        acrossElements: true,
                        className: 'commid' + j.i,
                        noMatch: function(t) {
                            container.find(j.p).markRanges([{start:j.s,length:j.l}], {
                                className: 'commid' + j.i
                            });
                        }
                    });
                } else {
                    container.find(j.p).markRanges([{start:j.s,length:j.l}], {
                        className: 'commid' + j.i
                    });
                }
            })
        }
        catch(e) {}
    });
    containerList.find('mark[data-markjs]').on('click', function(e) {
        annotationShowComment($(this));
    })
    containerList.bind('scroll, mousewheel', function() {
        if ( $('#annopop + .popover:visible').length > 0 ) {
            $('#annopop').popover('hide');
        }
    });
}

function initalizeAnnotations()
{
    $(document).on('selectionchange', function(e) {
        if (annotaion.popupTimeout) {
            clearTimeout(annotaion.popupTimeout);
            annotaion.popupTimeout = null;
        }

        var s = window.getSelection();
        try {
            var oRange = s.getRangeAt(0);
            if (!oRange) {
                $('#annopop').popover('hide');
                return;
            }
            if (oRange.collapsed) {
                $('#annopop').popover('hide');
                return;
            }
            annotaion.popupTimeout = setTimeout(function () {
                annotaion.popupTimeout = null;
                annotatePopup();
            }, 500);
        }
        catch(e) {}
    });
}

function annotationShowComment(el) {
    if ( !el.is('mark[class]') ) return;
    var result = el.attr('class').match(/commid(\d+)/);
    if ( !result ) return;
    var commentId = result[1];
    $('mark').removeClass('selected');
    el.addClass('selected');
    var commentsPanel = $('.details-header a[did=comments]');
    if ( commentsPanel.length > 0 ) {
        commentsPanel.attr('active-object', commentId).click();
        setTimeout(function() {
            filterExactComment(commentId);
        }, 500);
    }
    else {
        var project = el.parents('.wysiwyg[project]').attr('project');
        workflowNewObject('/pm/'+project+'/comment/'+commentId+'/reply','Comment','Comment','',[],devpromOpts.UpdateUI);
    }
}

function annotationSelectComment(comment, anchor)
{
    gotoRandomPage(anchor,1,true);
    $('mark').removeClass('selected');
    var commentElement = $('mark.commid'+comment);
    if ( commentElement.length < 1 ) return;
    commentElement.addClass('selected');
    if ( !commentElement.isInViewport() ) {
        $('.table-master .list-container').animate({
            scrollTop:  commentElement.offset().top - 210
        }, 300);
    }
}

function getDomPath(el) {
    var stack = [];
    while ( el.parentNode != null ) {
        var sibCount = 0;
        var sibIndex = 0;
        for ( var i = 0; i < el.parentNode.childNodes.length; i++ ) {
            var sib = el.parentNode.childNodes[i];
            if ( sib.nodeName == el.nodeName ) {
                if ( sib === el ) {
                    sibIndex = sibCount;
                }
                sibCount++;
            }
        }
        if ( el.hasAttribute('id') && el.id != '' ) {
            stack.unshift(el.nodeName.toLowerCase() + '#' + el.id);
        } else if ( sibCount > 1 ) {
            stack.unshift(el.nodeName.toLowerCase() + ':eq(' + sibIndex + ')');
        } else {
            stack.unshift(el.nodeName.toLowerCase());
        }
        if ( el.hasAttribute('objectid') && el.getAttribute('objectid') != '' ) break;
        el = el.parentNode;
    }

    return stack; // removes the html element
}