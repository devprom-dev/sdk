var annotaion = {
    popupTimeout: null,
};

function highlightComment() {
    var locstr = window.location.hash;
    if ( locstr.indexOf('#comment') > -1 ) {
        var parts = locstr.split('#');
        var section = $('#'+parts[1]).parent('.comment-thread-container');
        if ( section.length > 0 ) {
            section.attr('active', '');
            window.location.hash = '';
            history.replaceState(null, null, ' ');
        }

        var parts = locstr.split('#comment');
        if ( parts.length > 1 ) {
            annotationShowComment($('mark.commid' + parts[1]), function() {
                window.location.hash = '';
                history.replaceState(null, null, ' ');
            });
        }
    }
}

function setupFilterComments( attributes ) {
    $('.comments-filter').each(function() {
        $(this).removeAttr('checked');
    });
    $.each(attributes, function(key,item) {
        if ( item == 'order-asc' ) {
            $('.comments-filter[name=order]').attr('checked','checked');
            return true;
        }
        $('.comments-filter[name='+item+']').attr('checked','checked');
    });
    filterCommentsUpdate(attributes);
}

function filterComments() {
    var attributes = [];
    $('.comments-filter').each(function() {
        if ( $(this).attr('name') == 'order' ) {
            attributes.push($(this).is(':checked') ? 'order-asc' : 'order-desc');
            return true;
        }
        if ( $(this).is(':checked') ) {
            attributes.push($(this).attr('name'));
        }
    });
    localStorage.setItem('comments-filter-settings', JSON.stringify(attributes));
    filterCommentsUpdate(attributes);
}

function filterCommentsUpdate( attributes ) {
    var selectorAttributes = [];
    $.each(attributes, function(key,item) {
        if ( item == 'order-asc' ) {
            sortComments('1');
            return true;
        }
        if ( item == 'order-desc' ) {
            sortComments('-1');
            return true;
        }
        selectorAttributes.push('['+item+']')
    });
    var selector = '.comment-thread-container[active],.comment-thread-container' + selectorAttributes.join('');
    $('.comment-thread-container').removeClass('in');
    $(selector).addClass('in').parents('.comment-thread-container').addClass('in');
}

function filterItemComments(item)
{
    $('.comment-well[item='+item+']').each(function() {
        var commentId = $(this).parents('tr[object-id]').attr('object-id');
        makeCommentActive(commentId);
        return false;
    })
    $('.details-body').animate({
        scrollTop: $('.comment-well[item='+item+']').parent().position().top
    }, 300);
}

function makeCommentActive(item)
{
    $('tr[object-class="Comment"][object-id]').removeClass('active');
    var object = $('tr[object-class="Comment"][object-id='+item+']');
    if ( object.length < 1 ) return;
    object.addClass('active');
    object.find('.comment-well .collapse:not(.in)').addClass('in').attr("style", "");
    object.find('.comment-well .plus-minus-toggle').removeClass('collapsed');
}

function filterExactComment(commentId, callback)
{
    makeCommentActive(commentId);
    var object = $('tr[object-class="Comment"][object-id='+commentId+']');
    if ( object.length < 1 ) return;
    $('.details-body').animate({
        scrollTop: object.find('.comment-well').parent().position().top
    }, 300);
    if ( typeof callback == 'function' ) {
        callback();
    }
}

function sortComments( sort )
{
    if ( sort != "-1") sort = "1";
    cookies.setOptions({expires:new Date(new Date().getFullYear() + 1, 1, 1)});
    cookies.set('sort-comments', sort);
    if ( $('.comments-thread:has(.editor-area)').length > 0 ) return;
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
            var editorInstance;
            if ( typeof CKEDITOR != 'undefined' ) {
                editorInstance = CKEDITOR.instances[container.attr('id')];
            }
            var annotationJson = JSON.parse($(this).attr('annotation'));
            $.each(annotationJson, function(i,j) {
                var pathElement = container.find(j.p);
                if ( pathElement.length < 1 ) return;
                pathElement.mark(j.t, {
                    separateWordSearch: false,
                    accuracy: 'exactly',
                    acrossElements: true,
                    className: 'commid' + j.i,
                    noMatch: function(t) {
                        if ( pathElement.text().indexOf(j.t) < 0 ) {
                            container.mark(j.t, {
                                separateWordSearch: false,
                                acrossElements: true,
                                className: 'commid' + j.i
                            });
                        }
                        else {
                            pathElement.markRanges([{start:j.s,length:j.l}], {
                                className: 'commid' + j.i,
                                noMatch: function(t) {
                                    container.mark(j.t, {
                                        separateWordSearch: false,
                                        accuracy: 'exactly',
                                        acrossElements: true,
                                        className: 'commid' + j.i
                                    });
                                }
                            });

                        }
                    }
                });
            });
            if ( editorInstance ) editorInstance.resetDirty();
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

function annotationShowComment(el, callback) {
    if ( !el.is('mark[class]') ) return;
    var result = el.attr('class').match(/commid(\d+)/);
    if ( !result ) return;
    var commentId = result[1];
    var commentsPanel = $('.details-header a[did=comments]');
    if ( commentsPanel.length > 0 ) {
        commentsPanel.attr('active-object', commentId).click();
        setTimeout(function() {
            filterExactComment(commentId, callback);
        }, 500);
    }
    else {
        var project = el.parents('.wysiwyg[project]').attr('project');
        workflowNewObject('/pm/'+project+'/comment/'+commentId+'/reply','Comment','Comment','',[],devpromOpts.UpdateUI);
    }
}

function annotationSelectComment(comment, anchor)
{
    makeCommentActive(comment);
    gotoRandomPage(anchor,1,true, function() {
        var commentElement = $('mark.commid'+comment);
        if ( commentElement.length < 1 ) return;
        if ( !commentElement.isInViewport() ) {
            $('.table-master .list-container').animate({
                scrollTop:  commentElement.offset().top - 210
            }, 300);
        }
    });
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