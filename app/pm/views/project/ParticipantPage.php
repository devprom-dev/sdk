<?php

include_once SERVER_ROOT_PATH."pm/classes/participants/UserParticipatesModelBuilder.php"; 

include "ParticipantForm.php";
include "ParticipantRoleForm.php";
include "ParticipantTable.php";
include "ParticipantsPageSettingBuilder.php";

class ParticipantPage extends PMPage
{ 
 	function ParticipantPage()
 	{
 	    getSession()->addBuilder( new ParticipantsPageSettingBuilder() );
 	    getSession()->addBuilder( new UserParticipatesModelBuilder() );
 	    
 		parent::PMPage();
 	}
 	
 	function getObject()
 	{
 		$object = new User();
 		
 		$object->addFilter( new UserStatePredicate('active') );
 		
 		return $object;
 	} 
 	
 	function getTable() 
 	{
		return new ParticipantTable( $this->getObject() );	
 	}

 	function getForm() 
 	{
 		switch ( $_REQUEST['entity'] )
 		{
 			case 'ParticipantRole':
 				return new ParticipantRoleForm;

   			default:
 				return new ParticipantForm();
 		}
 	}
}