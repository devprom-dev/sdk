<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class VelocityReportsBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
        if ( !getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ) return;

        $module_it = getFactory()->getObject('Module')->getExact('scrum/velocitychart');
		$object->addReport(
			array ( 'name' => 'velocitychart',
					'title' => text('scrum9'),
			        'description' => text('scrum17'),
					'category' => FUNC_AREA_MANAGEMENT,
				    'query' => 'group=Caption&aggby=Velocity&aggregator=AVG',
			        'type' => 'chart',
			        'module' => $module_it->getId() )
		);
    }
}