var tc = underi18n.MessageFactory({
	"taskboard-intro": "Here is the <strong>common task board</strong> holds all issues across applications, infrastructure and development process related to your product or service.</p><p><br />Use this dashboard to <strong>identify</strong> issues, <strong>assign</strong> priorities and owners, <strong>resolve</strong> issues or <strong>push</strong> it into development pipeline.",
	"taskboard-support": "Under swimlane called \"Support\" you\'ll find customers requests sent by <strong>emails</strong> to support mailbox or submitted using <strong>HelpDesk</strong> web site.</p><p><br />Use <strong>context menu</strong> on card to ask more info, put request to work or resolve by moving the card into corresponding column.</p><p>&nbsp;</p><p>If development is required then move the card under \"Product\" swimlane to put it into <strong>development pipeline</strong>.",
	"taskboard-incidents": "Under swimlane called \"Incidents\" you\'ll find bugs, incidents and alerts raised by <strong>exception handlers</strong> somewhere in your application stack, raised by monitoring, APM, ARA, CI, CD tools and any other sources of applications and infrastructure <strong>issues</strong>.</p><p><br />Use <strong>context menu</strong> on card to define <strong>auto actions</strong> to change incidents priority, e.g. set Critical to unhandled exceptions and Low to hide not important incidents.</p><p><br />To <strong>resolve</strong> the incident put it into development pipeline by moving the card under \"Product\" swimlane.",
	"kanban-backlog": "Development <strong>Backlog</strong> is full of submitted features, bugs, customers requests, applications bugs and infrastructure incidents should be resolved.</p><p><br />Use <strong>context menu</strong> on card to set <strong>priority</strong> or bulk actions to make <strong>prioritized backlog</strong> used by the team. Elaborate and estimate issues if required and put it in Ready queue by moving the card.</p><p><br />Team members do their work and <strong>visualize it state</strong> by moving cards into corresponding columns.",
	"kanban-charts": "Adjust <strong>Kanban boar</strong>d correponding to your process: rename, append or remove <strong>columns</strong>, set <strong>WIPs</strong>, define fields should be filled on <strong>transitions</strong> between states, etc.</p><p><br />Use the <strong>charts</strong> do identify bottle necks of your process, to control the development velocity and gather other required <strong>metrics</strong>.",
	"support-intro": "To be focused on <strong>support activity</strong> just drilldown to \"Support\" project. Here you can find backlog of customers requests, tickets board, knowledge base and correposnding reports.",
	"support-helpdesk": "Provide your customers with HelpDesk <strong>web site</strong> where they can submit requests and check it states.</p><p><br />You can <strong>change address</strong> of HelpDesk web site and <strong>bind it</strong> to your domain.",
	"support-mailboxes": "To convert support requests sent by emails into cards on tickets board just append <strong>mailboxes</strong> here.</p><p><br />There are automatic <strong>email notifications</strong> when ticket is submitted or resolved. Comments related to tickets sent by emails also.",
	"incidents-intro": "To be focused on <strong>applications bugs</strong> and <strong>infrastructure incidents</strong> drilldown to \"Incidents\" project.</p><p><br />Check for <strong>builds</strong> bring more issues. Identify servers (or <strong>environments</strong>) where issues were found. Comment how the server was changed (patches, configration changes, etc) to <strong>share knowledge</strong> across the team.",
	"incidents-autoactions": "<strong>Auto actions</strong> allows you to set type, priority, assignee and other attributes <strong>automatically</strong> for all current and upcoming issues.</p><p><br />It allows your team to be focused on <strong>most important</strong> issues and <strong>do not waste time</strong> on manual analysing of dozen of incidents raised.",
	"incidents-settings": "To convert alerts sent by emails into incidents adjust <strong>integration settings</strong>. You can plug in as many <strong>mailboxes</strong> you have.</p><p><br />Check <strong>the docs</strong> to plug in other sources of incidents like APM, ARA, CI or CD tools, bugs reporters and exception handlers you use."
});
 
toursQueue.unshift(new Tour({
	debug: true,
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
	    placement: 'bottom',
	    path: '/pm/productA/issues/board/issuesboardcrossproject?report=issuesboardcrossproject&basemodule=issues-board&&area=favs'
	  },
	  {
	    element: "table.board-table th:eq(0) span.title",
	    title: 'Development Kanban board',
	    content: tc('kanban-backlog'),
	    placement: 'right',
	    path: '/pm/productA/module/kanban/requests/kanbanboard?report=kanbanboard&basemodule=kanban/requests&&area=favs'
	  },
	  {
	    element: "ul#menu_favs a#menu-group-reports",
	    title: 'Kanban metrics',
	    content: tc('kanban-charts'),
	    placement: 'right',
	    onShow: function() { setTimeout(function(){$('a#menu-group-reports').click();},1); }
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
	    element: "ul#menu_stg a[uid=support-mailboxes]",
	    title: 'Support mailboxes',
	    content: tc('support-mailboxes'),
	    placement: 'right',
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
	    element: "ul#menu_favs a[uid=support-autoactions]",
	    title: 'Setup auto actions',
	    content: tc('incidents-autoactions'),
	    placement: 'right',
	    path: '/pm/incidentsA/module/support/autoactions?area=favs'
	  },
	  {
	    element: "ul#menu_favs a[uid=incidents-settings]",
	    title: 'Integration settings',
	    content: tc('incidents-settings'),
	    placement: 'right',
	    path: '/pm/incidentsA/module/incidents/settings?area=favs'
	  },
	  {
		path: '/pm/productA/issues/board/issuesboardcrossproject?report=issuesboardcrossproject&basemodule=issues-board&&area=favs',
		orphan: true,
		duration: 1
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
		startTour();
	},
	onShown: function(tour) {
		$('[data-role=stop]').click( function() {
			tour.end();
        });
	}
}));