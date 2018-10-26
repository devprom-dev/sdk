<?php

include SERVER_ROOT_PATH."pm/classes/plan/StageModelBuilder.php";
include(SERVER_ROOT_PATH.'pm/methods/c_stage_methods.php');

include "ReleaseForm.php";
include "IterationForm.php";
include "VersionTable.php";
include "ReleaseBurndownSection.php";
include "IterationBurndownSection.php";
include "VersionPageSettingBuilder.php";

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

		if ( $this->needDisplayForm() ) {
			$object_it = $this->getObjectIt();
			if ( is_object($object_it) && $object_it->getId() > 0 )
			{
                $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
                if ( $methodology_it->IsAgile() ) {
                    if ( $object_it->object instanceof Release ) {
                        $this->addInfoSection( new ReleaseBurndownSection($object_it) );
                    }
                    if ( $object_it->object instanceof Iteration ) {
                        $this->addInfoSection( new IterationBurndownSection($object_it) );
                    }
                }
                $object = $this->getFormRef()->getObject();
                $stage = $this->getObject();
				$this->addInfoSection( new PageSectionAttributes($object, 'tab-issues',
                    $stage->getAttributeType('Issues') != '' ? $stage->getAttributeUserName('Issues') : $stage->getAttributeUserName('Increments')
                ));
				$this->addInfoSection( new PageSectionAttributes($object,'tab-tasks',translate('Задачи')) );
                $this->addInfoSection( new PageSectionAttributes($object,'artefacts',translate('Документация')) );
                $this->addInfoSection( new PageSectionComments($object_it) );
                $this->addInfoSection( new PMLastChangesSection($object_it) );
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