<?php
include_once "IterationForm.php";
include "IterationTable.php";
include "IterationBurndownSection.php";

include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/IterationModelArtefactsBuilder.php";

class IterationPage extends PMPage
{
 	function __construct()
 	{
 		getSession()->addBuilder( new IterationModelMetricsBuilder() );
		getSession()->addBuilder( new IterationModelArtefactsBuilder() );

		parent::__construct();

		if ( $this->needDisplayForm() ) {
			$object_it = $this->getObjectIt();
			if ( is_object($object_it) && $object_it->getId() > 0 )
			{
                $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
                if ( $methodology_it->IsAgile() ) {
                    $this->addInfoSection( new IterationBurndownSection($object_it) );
                }

                $object = $this->getFormRef()->getObject();
                $stage = $this->getObject();
				$this->addInfoSection( new PageSectionAttributes($object, 'tabissues',$stage->getAttributeUserName('Issues')));
				if ( getSession()->IsRDD() ) {
                    $this->addInfoSection( new PageSectionAttributes($object, 'tabincrements',$stage->getAttributeUserName('Increments')));
                }
				$this->addInfoSection( new PageSectionAttributes($object,'tabtasks',translate('Задачи')) );
                $this->addInfoSection( new PageSectionAttributes($object,'artefacts',translate('Документация')) );
                $this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
                $this->addInfoSection( new PMLastChangesSection($object_it) );
			}
		}
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Iteration');
 	}
 	
 	function getTable() {
		return new IterationTable($this->getObject());
 	}
 	
 	function getEntityForm() {
        return new IterationForm($this->getObject());
 	}
}