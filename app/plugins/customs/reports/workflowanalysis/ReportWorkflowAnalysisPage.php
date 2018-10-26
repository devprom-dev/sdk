<?php
include_once SERVER_ROOT_PATH."pm/views/issues/RequestPage.php";
include 'ReportWorkflowAnalysisTable.php';
include 'PageSettingWorkflowAnalysisBuilder.php';
include "model/RequestModelWorkflowBuilder.php";

class ReportWorkflowAnalysisPage extends RequestPage
{
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
