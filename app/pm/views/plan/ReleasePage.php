<?php
include_once "ReleaseForm.php";
include "ReleaseTable.php";
include "ReleaseBurndownSection.php";

include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelMetricsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/ReleaseModelArtefactsBuilder.php";

class ReleasePage extends PMPage
{
 	function __construct()
 	{
        getSession()->addBuilder( new ReleaseModelMetricsBuilder() );
		getSession()->addBuilder( new ReleaseModelArtefactsBuilder() );

		parent::__construct();

		if ( $this->needDisplayForm() ) {
			$object_it = $this->getObjectIt();
			if ( is_object($object_it) && $object_it->getId() > 0 )
			{
                $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
                if ( $methodology_it->IsAgile() ) {
                    $this->addInfoSection( new ReleaseBurndownSection($object_it) );
                }

                $object = $this->getFormRef()->getObject();
                $stage = $this->getObject();
				$this->addInfoSection( new PageSectionAttributes($object, 'tabissues',
                    $stage->getAttributeType('Issues') != '' ? $stage->getAttributeUserName('Issues') : $stage->getAttributeUserName('Increments')
                ));

				$this->addInfoSection( new PageSectionAttributes($object,'tabtasks',translate('Задачи')) );
                $this->addInfoSection( new PageSectionAttributes($object,'artefacts',translate('Документация')) );
                $this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
                $this->addInfoSection( new PMLastChangesSection($object_it) );
			}
		}
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Release');
 	}
 	
 	function getTable() {
		return new ReleaseTable( $this->getObject() );
 	}
 	
 	function getEntityForm() {
        return new ReleaseForm($this->getObject());
 	}
}