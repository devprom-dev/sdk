var tc = underi18n.MessageFactory(resource);
(function ($) {
	/**
	* @function
	* @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
	* @param {function} handler A function to execute at the time when the element is inserted
	* @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
	* @example $(selector).waitUntilExists(function);
	*/
	$.fn.waitUntilExists    = function (handler, shouldRunHandlerOnce, isChild) {
	    var found       = 'found';
	    var $this       = $(this.selector);
	    var $elements   = $this.not(function () { return $(this).data(found); }).each(handler).data(found, true);

	    if (!isChild) {
	        (window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
	            window.setInterval(function () { $this.waitUntilExists(handler, shouldRunHandlerOnce, true); }, 500);
	    }
	    else if (shouldRunHandlerOnce && $elements.length) {
	        window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
	    }
	    return $this;
	};
}(jQuery));

toursQueue.unshift(new Tour({
	steps: [
	  {
	    element: "table.board-table th:eq(0) span.title",
	    title: 'Task board',
	    content: tc('taskboard-intro'),
	    placement: 'right',
	  },
	  {
	    element: "table.board-table tr.info:eq(0) td",
	    title: 'Support swimlane',
	    content: tc('taskboard-support'),
	    placement: 'bottom',
	  },
	  {
	    element: "table.board-table tr.info:eq(1) td",
	    title: 'Incidents swimlane',
	    content: tc('taskboard-incidents'),
	    placement: 'bottom'
	  },
	  {
	    element: "table.board-table th:eq(0) span.title",
	    title: 'Development Kanban board',
	    content: tc('kanban-backlog'),
	    placement: 'right',
	    path: '/pm/dev/module/kanban/requests/kanbanboard?report=kanbanboard&basemodule=kanban/requests&&area=favs'
	  },
	  {
		orphan: true,
	    title: 'Kanban metrics',
	    content: tc('kanban-charts'),
		path: '/pm/dev/module/kanban/avgleadtime/avgleadtime?report=avgleadtime&basemodule=kanban/avgleadtime&&area=favs'
	  },
	  {
	    element: "a#navbar-project",
	    title: 'Support activity',
	    content: tc('support-intro'),
	    placement: 'bottom',
	    path: '/pm/supportA/issues/board/issuesboard?report=issuesboard&basemodule=issues-board&&area=favs'
	  },
	  {
	    element: "a#helpdesk-url",
	    title: 'HelpDesk web site',
	    content: tc('support-helpdesk'),
	    placement: 'bottom'
	  },
	  {
	    element: "table.table-inner tr:eq(1)",
	    title: 'Support mailboxes',
	    content: tc('support-mailboxes'),
	    placement: 'bottom',
	    path: '/pm/supportA/module/support/mailboxes?area=stg'
	  },
	  {
	    element: "a#navbar-project",
	    title: 'Analyze incidents',
	    content: tc('incidents-intro'),
	    placement: 'bottom',
	    path: '/pm/incidentsA/project/dicts/Environment?area=favs'
	  },
	  {
	    element: "table.table-inner tr:eq(1)",
	    title: 'Setup auto actions',
	    content: tc('incidents-autoactions'),
	    placement: 'bottom',
	    path: '/pm/incidentsA/module/support/autoactions?area=favs'
	  },
	  {
		path: '/pm/all',
		orphan: true,
		onShow: function(tour) {
			setTimeout(function(){tour.end();},1000);
		}
	  }
	],
	name: "devOpsBoardTour",
	duration: 1000 * 120,
	template: 
		"<div class='popover tour' style='max-width:450px;'>"+
		  "<div class='arrow'></div>"+
		  "<h3 class='popover-title' style='color:#fff;background-color: #428bca;border: 2px solid #428bca;'></h3>"+
		  "<div class='popover-content'></div>"+
		  "<div class='popover-navigation text-center' style='padding: 9px 14px;'>"+
		    "<button class='btn btn-default pull-left' data-role='prev'><span class='ui-button-text'><i class='icon-backward'></i></span></button>"+
		    "<button class='btn btn-default' data-role='stop' title='"+ptt('end-tour')+"'><span class='ui-button-text'><i class='icon-stop'></i></span></button>"+
		    "<button class='btn btn-default pull-right' data-role='next'><span class='ui-button-text'><i class='icon-forward'></i></span></button>"+
		    "<div class='clearfix'></div>"+
		  "</div>"+
		"</div>",
	onShow: function(tour) {
		$('.with-tooltip').popover('disable');
	},
	onEnd: function(tour) {
		$('.with-tooltip').popover('enable');
		startNextTour();
	},
	onShown: function(tour) {
		$('[data-role=stop]').click( function() {
			tour.end();
        });
	}
}));