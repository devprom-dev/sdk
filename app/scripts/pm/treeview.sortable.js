/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */

var sortableTree = (function() {

    var sortableTree,
        formerItemLocation,
        backupItem,
        options;

    var init = function(el, _options) {
        options = _options;
        sortableTree = $(el);
        sortableTree.sortable2(
            {
                containerSelector: 'ul.treeview',
                itemSelector: 'li.treeview',
                placeholder: '<li class="treeitem-placeholder treeview"/>',
                handle: 'div.treeview-label',
                delay: 200,
                onDragStart: _onDragStart,
                afterMove: _afterMove,
                isValidTarget: _isValidTarget,
                onDrop: _onDrop,
                nestTolerance: 30,
                tolerance: 5
            }
        );
    };

    var _getItemTreeLocation = function(container, item) {
        var parentId = container.el.parents('li.treeview:first').attr('id');
        var prevSibling = item.prevAll('li:not(.old-item):first');
        var prevSiblingId = prevSibling.size() > 0 ? prevSibling.attr('id') : -1;
        return {parentId: parentId, prevSiblingId: prevSiblingId};
    };

    var _isItemInsideTheTree = function(item) {
        var parentDiv = sortableTree.parents('div.treeview');

        if (parentDiv.height() == 0)
            parentDiv = sortableTree.parents('ul.filetree');

        var parentOffset = parentDiv.offset(),
            parentWidth = parentDiv.width(),
            parentHeight = parentDiv.height(),
            itemOffset = item.offset(),
            tolerance = 15;

        return itemOffset.left >= parentOffset.left - tolerance
            && itemOffset.top >= parentOffset.top - tolerance
            && (itemOffset.left <= parentOffset.left + parentWidth + tolerance)
            && (itemOffset.top <= parentOffset.top + parentHeight + tolerance);
    };

    var _onDragStart = function(item, container, _super) {
        // Duplicate item
        formerItemLocation = _getItemTreeLocation(container, item);
        backupItem = item.clone().insertAfter(item).addClass("old-item");
        _super(item);
    };

    var _afterMove = function(placeholder, container) {
        // add styling to active list
        sortableTree.parents("ul.treeview:first").find('li.active-container').removeClass('active-container');
        placeholder.parents('li.treeview:first').addClass('active-container');
        // put dragged item name into the placeholder
        placeholder.html(backupItem.html());

        if (placeholder.next('li').size() == 0) {
            placeholder.addClass('last');
        } else {
            placeholder.removeClass('last');
        }
        // if container is collapsed, expand it by clicking toggle icon
        placeholder.parents("ul.treeview:first").filter(":hidden").siblings('.hitarea').each(function() {
            this.click();
            sortableTree.data('sortable2').clearDimensions();
        });
    };

    var _isValidTarget = function(item, container) {
        // do not allow to drop item into sublist for itself
        var result = !container.el.parents("li.treeview").hasClass('old-item');

        var insideTheTree = _isItemInsideTheTree(item);

        if (!insideTheTree) {
            // clear active list styling
            sortableTree.parents("ul.treeview:first").find('li.active-container').removeClass('active-container');
            $("body").addClass("forbidden");
        } else {
            $("body").removeClass("forbidden");
        }

        return result && insideTheTree;
    };

    var _rollbackItemChange = function (item) {
        item.remove();
        backupItem.show().removeClass("old-item");
    };


    function _showLoadingIndicator(item) {
        item.find(">div.treeview-label").removeClass('wiki_document folder folder_page').addClass('updating');
    }

    function _hideLoadingIndicator(item) {
        item.find(">div.treeview-label").removeClass('updating');
    }

    function _showServerError(msg) {
        $("#tree-error-msg").remove();
        $("<div>").attr("id", "tree-error-msg").addClass("alert alert-danger")
            .insertBefore(".wikitreesection").text(msg)
            .append('<a class="close" data-dismiss="alert" href="#">&times;</a>').alert();
    }

    function _sendServerRequest(item, location) {
        var itemId = item.attr("id");
        var url = options.applicationUrl + "command.php?class=wikipagemove" +
            "&object_id=" + itemId + "&ParentPage=" + location.parentId + "&PrevSiblingPage=" + location.prevSiblingId
            + "&ObjectClass=" + options.objectClass + "&action=2";

        backupItem.hide();
        _showLoadingIndicator(item);
        $("#tree-error-msg").remove();

        sortableTree.sortable2("disable");
        $.ajax({ url: url, dataType: 'json', type: 'POST'})
            .success(function (data) {
                if (data.state === 'success') {
                    _hideLoadingIndicator(item);
                    backupItem.remove();
                } else {
                    _showServerError(data.message);
                    _rollbackItemChange(item);
                }
            })
            .error(function() {
                _showServerError(data.message);
                _rollbackItemChange(item);
            })
            .always(function (data) {
                sortableTree.sortable2("enable");
                sortableTree.parents("ul.treeview:first").reapplyClasses();
            });
    }

    var _onDrop = function(item, container, _super) {
        // clear active list styling
        sortableTree.parents("ul.treeview:first").find('li.active-container').removeClass('active-container');
        $("body").removeClass("forbidden");
        _super(item);

        var location = _getItemTreeLocation(container, item);

        // check if item has been actually moved
        if (location.parentId != formerItemLocation.parentId || location.prevSiblingId != formerItemLocation.prevSiblingId) {
            _sendServerRequest(item, location);
        } else {
            _rollbackItemChange(item);
        }
    };

    // public API
    return {
        init: init
    }

})();


