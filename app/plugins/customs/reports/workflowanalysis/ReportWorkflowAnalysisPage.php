<?php

include 'ReportWorkflowAnalysisTable.php';
include 'PageSettingWorkflowAnalysisBuilder.php';
include "model/RequestModelWorkflowBuilder.php";

class ReportWorkflowAnalysisPage extends PMPage
{
	public function __construct()
	{
		getSession()->addBuilder( new PageSettingWorkflowAnalysisBuilder() );
		
		parent::__construct();
	}
	
	public function getObject()
	{
    	global $model_factory;

		getSession()->addBuilder( new RequestModelWorkflowBuilder() );
		
    	$object = $model_factory->getObject('Request');
    	
    	return $object;
	}
	
    function getTable()
    {
        return new ReportWorkflowAnalysisTable( $this->getObject() );
    }

    function getForm()
    {
        return null;
    }
}
