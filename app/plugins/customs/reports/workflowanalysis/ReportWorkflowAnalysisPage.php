<?php

include 'ReportWorkflowAnalysisTable.php';
include 'PageSettingWorkflowAnalysisBuilder.php';
include "model/RequestModelWorkflowBuilder.php";

class ReportWorkflowAnalysisPage extends PMPage
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getObject()
	{
        getSession()->addBuilder( new RequestModelWorkflowBuilder() );
    	return getFactory()->getObject('Request');
	}
	
    function getTable()
    {
        getSession()->addBuilder( new PageSettingWorkflowAnalysisBuilder() );
        return new ReportWorkflowAnalysisTable( $this->getObject() );
    }

    function getForm()
    {
        return null;
    }
}
