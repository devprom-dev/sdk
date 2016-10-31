<?php

include SERVER_ROOT_PATH."pm/classes/plan/StageModelBuilder.php";
include(SERVER_ROOT_PATH.'pm/methods/c_stage_methods.php');

include "ReleaseForm.php";
include "IterationForm.php";
include "VersionTable.php";
include "ReleaseBurndownSection.php";
include "IterationBurndownSection.php";

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

		parent::__construct();

		if ( $this->needDisplayForm() ) {
			$object_it = $this->getObjectIt();
			if ( is_object($object_it) && $object_it->getId() > 0 )
			{
				if ( $object_it->object instanceof Release ) {
					$this->addInfoSection( new ReleaseBurndownSection($object_it) );
				}
				if ( $object_it->object instanceof Iteration ) {
					$this->addInfoSection( new IterationBurndownSection($object_it) );
				}
				$this->addInfoSection( new PageSectionAttributes($this->getFormRef()->getObject(),'tab-issues',translate('Пожелания')) );
				$this->addInfoSection( new PageSectionAttributes($this->getFormRef()->getObject(),'tab-tasks',translate('Задачи')) );
			}
		}
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Stage');
 	}
 	
 	function getTable() {
		return new VersionTable( $this->getObject() );		
 	}
 	
 	function getForm() 
 	{
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