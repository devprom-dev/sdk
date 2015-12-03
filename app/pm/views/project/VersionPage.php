<?php

include SERVER_ROOT_PATH."pm/classes/plan/StageModelBuilder.php";

include(SERVER_ROOT_PATH.'pm/methods/c_version_methods.php');
include(SERVER_ROOT_PATH.'pm/methods/c_stage_methods.php');

include "ReleaseForm.php";
include "IterationForm.php";
include "VersionTable.php";
include "ReleaseBurndownSection.php";
include "IterationBurndownSection.php";

include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelMetricsBuilder.php";

class VersionPage extends PMPage
{
 	function __construct()
 	{
 		getSession()->addBuilder( new IterationModelMetricsBuilder() );
        getSession()->addBuilder( new ReleaseModelMetricsBuilder() );
        getSession()->addBuilder( new StageModelBuilder() );

		parent::__construct();

		$object_it = $this->getObjectIt();
		if ( is_object($object_it) && $object_it->getId() > 0 ) {
			if ( $object_it->object instanceof Release ) {
				$this->addInfoSection( new ReleaseBurndownSection($object_it) );
			}
			if ( $object_it->object instanceof Iteration ) {
				$this->addInfoSection( new IterationBurndownSection($object_it) );
			}
		}
 	}
 	
 	function getObject()
 	{
 		return getFactory()->getObject('Stage');
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
}