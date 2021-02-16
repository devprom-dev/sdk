<?php
include SERVER_ROOT_PATH."pm/classes/communications/QuestionModelExtendedBuilder.php";
include "QuestionTable.php";
include "QuestionForm.php";

class QuestionPage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();

		if ( $this->needDisplayForm() )	{
			$object_it = $this->getObjectIt();
			if ( is_object($object_it) && $object_it->count() > 0 ) {
 				$this->addInfoSection( new PageSectionComments($object_it) );
			    $this->addInfoSection( new StatableLifecycleSection($object_it) );

			}
            $form = $this->getFormRef();
			if ( is_object($form) ) {
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'additional', translate('Дополнительно')));
            }
		}
 	}
 	
	function getObject()
	{
		getSession()->addBuilder(new QuestionModelExtendedBuilder());
 		return getFactory()->getObject('pm_Question');
	}
	
 	function getTable() 
 	{
 		return new QuestionTable( $this->getObject() );
 	}
 	
 	function getEntityForm()
 	{
 		return new QuestionForm( $this->getObject() );
 	}

 	function needDisplayForm() 
 	{
 		if ( $_REQUEST['kind'] == 'ask' ) {
 			return true;
 		}
 		return parent::needDisplayForm();
 	}

    function getPageWidgets()
    {
        return array('project-question');
    }
}