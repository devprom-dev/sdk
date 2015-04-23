var issueSetupFormFieldsTour = new Tour({
	steps: [
	  {
	    path: '/pm/'+devpromOpts.project+'/project/workflow/IssueState?'
	  },
	  {
	  },
	  {
	    onNext: function(tour) {
	    	$('tr[id*=statelist]:eq(0)').dblclick();
	    	setTimeout(function() { tour.goTo(3); }, 2500);
	    }
	  },
	  {
	    element: "span[id=pm_StateAttributes] a.dashed",
	    title: ptt('state-fields'),
	    content: ptc('state-fields'),
	    duration: 20 * 1000,
	    prev: -1
	  }
	],
	name: "issueSetupFormFieldsTour",
	duration: 1500,
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
		sendUXData(window.location.protocol + "//" + window.location.host + "/product/setupformfields/" + tour.getCurrentStep());
	}
});

var taskSetupFormFieldsTour = new Tour({
	steps: [
	  {
	    path: '/pm/'+devpromOpts.project+'/project/workflow/TaskState?'
	  },
	  {
	  },
	  {
	    onNext: function(tour) {
	    	$('tr[id*=statelist]:eq(0)').dblclick();
	    	setTimeout(function() { tour.goTo(3); }, 2500);
	    }
	  },
	  {
	    element: "span[id=pm_StateAttributes] a.dashed",
	    title: ptt('state-fields'),
	    content: ptc('state-fields'),
	    duration: 20 * 1000,
	    prev: -1
	  }
	],
	name: "taskSetupFormFieldsTour",
	duration: 1500,
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
		sendUXData(window.location.protocol + "//" + window.location.host + "/product/setupformfields/" + tour.getCurrentStep());
	}
});

$(document).ready(function() {
	issueSetupFormFieldsTour.init();
	taskSetupFormFieldsTour.init();
});