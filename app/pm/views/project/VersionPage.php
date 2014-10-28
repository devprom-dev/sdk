<?php

include SERVER_ROOT_PATH."pm/classes/plan/StageModelBuilder.php";

include(SERVER_ROOT_PATH.'pm/methods/c_version_methods.php');
include(SERVER_ROOT_PATH.'pm/methods/c_stage_methods.php');

include "ReleaseForm.php";
include "IterationForm.php";
include "VersionTable.php";

include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelMetricsBuilder.php";

class VersionPage extends PMPage
{
 	function __construct()
 	{
 		getSession()->addBuilder( new IterationModelMetricsBuilder() );
 		
        getSession()->addBuilder( new ReleaseModelMetricsBuilder() );
 		
        parent::__construct();
 	}
 	
 	function getObject()
 	{
 		global $model_factory;
 		
        getSession()->addBuilder( new StageModelBuilder() );
 		
 		return $model_factory->getObject('Stage');
 	}
 	
 	function getTable() 
	{
		return new VersionTable( $this->getObject() );		
 	}
 	
 	function getForm() 
 	{
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['entity'] )
 		{
 			case 'pm_Release':
 			case 'Iteration':
 				return new IterationForm();

 			default:
 		 		return new ReleaseForm();
 		}
 	}
 	
 	function getHint()
 	{
 		if ( getFactory()->getObject('Module')->getExact('ee/msproject')->getId() != '' )
 		{
 			return text('ee221').'<br/><br/><img src="/plugins/ee/resources/msproject.png"><br/><br/>'.text('ee222'); 
 		}
 	}
}