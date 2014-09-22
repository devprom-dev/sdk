<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class VelocityReportsBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
     	global $model_factory;

     	$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
     	
     	if ( $methodology_it->get('UseScrums') != 'Y' ) return;
     	
		$module_it = $model_factory->getObject('Module')->getExact('scrum/velocitychart');
		
		$object->addReport(
			array ( 'name' => 'velocitychart',
					'title' => text('scrum9'),
			        'description' => text('scrum17'),
					'category' => FUNC_AREA_MANAGEMENT,
				    'query' => 'group=Caption&aggby=Velocity&aggregator=MAX',
			        'type' => 'chart',
			        'module' => $module_it->getId() )
		);
    }
}