<?php
include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCustomsBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
		$object->addReport(
			array (
			    'name' => 'workflowanalysis',
                'description' => text('customs2'),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => 'customs/workflowanalysis'
            )
		);
    }
}