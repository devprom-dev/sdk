<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCustomsBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
   		global $model_factory;
 		
 		$module = $model_factory->getObject('Module');
 		
		$object->addReport(
			array ( 'name' => 'workflowanalysis', 
					'description' => text('customs2'),
					'category' => FUNC_AREA_MANAGEMENT,
			        'module' => $module->getExact('customs/workflowanalysis')->getId() )
		);
    }
}