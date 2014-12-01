var tour = new Tour({
	steps: [
	  {
	    element: "ul.nav:first()",
	    title: ptt('vertical-menu'),
	    content: ptc('vertical-menu'),
	    placement: 'bottom'
	  },
	  {
	    element: ".filter div.btn-group:eq(1)",
	    title: ptt('filters'),
	    content: ptc('filters'),
	    placement: 'bottom'
	  },
	  {
	    element: "#filter-settings ~ul",
	    title: ptt('filter-settings'),
	    content: ptc('filter-settings'),
	    onShow: function() { $('#filter-settings ~ul').css({'display':'block'}); },
	    onHide: function() { $('#filter-settings ~ul').css({'display':''}); },
	  },
	  {
	    element: "li[uid=add-favorites]",
	    title: ptt('add-favorites'),
	    content: ptc('add-favorites'),
	    onShow: function() { if ( $("li[uid=add-favorites]").length > 0 ) { $('#filter-settings ~ul').css({'display':'block'}); } },
	    onHide: function() { $('#filter-settings ~ul').css({'display':''}); },
	  },
	  {
	    element: "div.filter-actions>div:last>a",
	    title: ptt('filter-actions'),
	    content: ptc('filter-actions'),
	    placement: 'left',
	    onShow: function() { $('div.filter-actions>div:last>ul').css({'display':'block'}); },
	    onHide: function() { $('div.filter-actions>div:last>ul').css({'display':''}); },
	  },
	  {
	    element: "ul#menu_favs li#setup a span:last()",
	    title: ptt('adjust-menu-settings'),
	    content: ptc('adjust-menu-settings'),
	    placement: 'right',
	    onShow: function() { setTimeout(function(){$('li#tab_favs a').click();},1); }
	  },
	  {
	    element: "li#tab_stg a",
	    title: ptt('project-settings'),
	    content: ptc('project-settings'),
	    placement: 'bottom',
	    onShow: function() { setTimeout(function(){$('li#tab_stg a').click();},1); }
	  },
	  {
	    element: "a#navbar-company-name ~ ul i.icon-plus ~ a",
	    title: ptt('project-creation'),
	    content: ptc('project-creation'),
	    onShow: function() { $('a#navbar-company-name ~ ul').css({'display':'block'}); },
	    onHide: function() { $('a#navbar-company-name ~ ul').css({'display':''}); },
	  },
	  {
	    element: "a[uid=taskassignee]",
	    title: ptt('my-tasks'),
	    content: ptc('my-tasks'),
	    path: /pm\/my/i,
	    placement: 'bottom'
	  },
	  {
	    element: "a#navbar-company-name ~ ul i.icon-wrench ~ a",
	    title: ptt('system-administration'),
	    content: ptc('system-administration'),
	    onShow: function() { if ( $("a#navbar-company-name ~ ul i.icon-wrench ~ a").length > 0 ) $('a#navbar-company-name ~ ul').css({'display':'block'}); },
	    onHide: function() { $('a#navbar-company-name ~ ul').css({'display':''}); },
	  }
	],
	name: "devpromTour",
	duration: 1000 * 60,
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
	onNext: function(tour) {
		sendUXData(window.location.protocol + "//" + window.location.host + "/product/tour/" + tour.getCurrentStep());
	},
	onShow: function(tour) {
		$('.with-tooltip').popover('disable');
	},
	onEnd: function(tour) {
		$('.with-tooltip').popover('enable');
    	runMethod(
            	'methods.php?method=SettingsWebMethod', 
            	{
                	'setting' : 'skip-product-tour',
                	'value' : 'true'
                }, 
            	function() {}, 
            	''
            );
	},
	onShown: function(tour) {
		$('[data-role=stop]').click( function() {
			tour.end();
        });
	}
});

$(document).ready(function() {
	// Initialize the tour
	tour.init();
	// Start the tour
	tour.start(true);
});
