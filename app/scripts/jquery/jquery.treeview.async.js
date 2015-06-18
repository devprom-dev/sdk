/*
 * Async Treeview 0.1 - Lazy-loading extension for Treeview
 * 
 * http://bassistance.de/jquery-plugins/jquery-plugin-treeview/
 *
 * Copyright (c) 2007 Jörn Zaefferer
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 *
 */

;(function($) {

function load(settings, root, child, container) {
	function createNode(parent) {
		var current = $("<li class=\"treeview\"/>").attr("id", this.id || "").attr("documentid", this.documentid || "").html(this.text).appendTo(parent);
		if (this.classes) {
			current.children("span").addClass(this.classes);
		}
		if (this.expanded) {
			current.addClass("open");
		}
		if (this.hasChildren || this.children && this.children.length) {
			var branch = $("<ul class=\"treeview\"/>").appendTo(current);
			if (this.hasChildren) {
				current.addClass("hasChildren");
				createNode.call({
					classes: "placeholder",
					text: "&nbsp;",
					children:[]
				}, branch);
			}
			if (this.children && this.children.length) {
				$.each(this.children, createNode, [branch])
			}
		} else { // add empty sublist to allow nestable sorting
            current.append('<ul class="treeview"></ul>');
		}
	}
	
	function buildTree(response) {
		child.empty();
		$.each(response, createNode, [child]);
        $(container).treeview({add: child});

        if ( typeof settings.asyncCallback != 'undefined' ) settings.asyncCallback();

        cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
		if ( root != 'source' ) cookies.set(settings.cookieId, root );
    }
	
	if ( typeof settings.treeData != 'undefined' && settings.treeData.length > 0 )
	{
		buildTree(settings.treeData);
		settings.treeData = [];
	}
	else
	{
		$.ajax($.extend(true, {
			url: settings.url,
			dataType: "json",
			data: {
				root: root
			},
			success: function(response) {
				buildTree(response);
			},
		    error: function (xhr, status, error ) {
		    }
		}, settings.ajax));
	}
}

var proxied = $.fn.treeview;
$.fn.treeview = function(settings) {
	if (!settings.url) {
		return proxied.apply(this, arguments);
	}
	var container = this;
	if (!container.children().size())
		load(settings, settings.root, this, container);
	var userToggle = settings.toggle;
	return proxied.call(this, $.extend({}, settings, {
		collapsed: true,
		toggle: function() {
			var $this = $(this);
			if ($this.hasClass("hasChildren")) {
				var childList = $this.removeClass("hasChildren").find("ul.treeview");
				load(settings, this.id, childList, container);
			}
			if (userToggle) {
				userToggle.apply(this, arguments);
			}
		}
	}));
};

})(jQuery);