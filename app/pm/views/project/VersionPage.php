<?php
include SERVER_ROOT_PATH."pm/classes/plan/StageModelBuilder.php";
include(SERVER_ROOT_PATH . 'pm/methods/ResetBurndownWebMethod.php');

include "VersionTable.php";
include "VersionPageSettingBuilder.php";

include_once SERVER_ROOT_PATH."pm/views/plan/IterationForm.php";
include_once SERVER_ROOT_PATH."pm/views/plan/ReleaseForm.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelArtefactsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelArtefactsBuilder.php";

class VersionPage extends PMPage
{
 	function __construct()
 	{
 		getSession()->addBuilder( new IterationModelMetricsBuilder() );
        getSession()->addBuilder( new ReleaseModelMetricsBuilder() );
        getSession()->addBuilder( new StageModelBuilder() );
		getSession()->addBuilder( new ReleaseModelArtefactsBuilder() );
		getSession()->addBuilder( new IterationModelArtefactsBuilder() );
        getSession()->addBuilder( new VersionPageSettingBuilder() );

		parent::__construct();
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Stage');
 	}
 	
 	function getTable() {
		return new VersionTable( $this->getObject() );		
 	}
 	
 	function getEntityForm()
    {
        switch ( $_REQUEST['entity'] ) {
            case 'pm_Release':
            case 'Iteration':
                return new IterationForm();
            default:
                return new ReleaseForm();
        }
    }
}