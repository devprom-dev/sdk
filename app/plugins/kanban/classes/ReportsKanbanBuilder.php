<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsKanbanBuilder extends ReportsBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build( ReportRegistry & $object )
    {
     	$project_it = $this->session->getProjectIt();
     	
     	if ( $project_it->getMethodologyIt()->get('IsKanbanUsed') != 'Y' || $project_it->IsPortfolio() ) return;

     	$module_it = getFactory()->getObject('Module')->getExact('kanban/requests');
     	
		$object->addReport( array ( 
            'name' => 'kanbanboard',
			'title' => text('kanban17'),
			'description' => $module_it->get('Description'),
			'category' => FUNC_AREA_MANAGEMENT,
	        'module' => $module_it->getId() 
		));
     	
		$module_it = getFactory()->getObject('Module')->getExact('issues-chart');
		
     	$object->addReport( array ( 
            'name' => 'commulativeflow',
			'title' => text('kanban18'),
			'category' => FUNC_AREA_MANAGEMENT,
		    'query' => 'group=history&aggby=State&state=all&infosections=none&modifiedafter=last-month',
	        'type' => 'chart',
     		'description' => text('kanban30'),
	        'module' => $module_it->getId() 
		));
		
     	$module_it = getFactory()->getObject('Module')->getExact('kanban/avgleadtime');
     	
		$object->addReport( array ( 
	        'name' => 'avgleadtime',
			'title' => text('kanban19'),
			'category' => FUNC_AREA_MANAGEMENT,
		    'query' => 'chartdata=hide&chartlegend=hide&aggregator=AVG&group=FinishDate&aggby=LifecycleDuration&state='.
		        join(',',getFactory()->getObject('Request')->getTerminalStates()).'&infosections=none',
	        'type' => 'chart',
			'description' => $module_it->get('Description'),
	        'module' => $module_it->getId() 
		));
    }
}