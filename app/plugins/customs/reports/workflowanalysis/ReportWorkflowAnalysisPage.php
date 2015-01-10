<?php

include 'ReportWorkflowAnalysisTable.php';
include 'PageSettingWorkflowAnalysisBuilder.php';
include "model/RequestModelWorkflowBuilder.php";

class ReportWorkflowAnalysisPage extends PMPage
{
	public function __construct()
	{
		getSession()->addBuilder( new PageSettingWorkflowAnalysisBuilder() );
		getSession()->addBuilder( new RequestModelWorkflowBuilder() );
		
		parent::__construct();
	}
	
	public function getObject()
	{
    	return getFactory()->getObject('Request');
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
