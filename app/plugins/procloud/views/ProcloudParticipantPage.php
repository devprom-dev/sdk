<?php

include "FindParticipantForm.php";
include "InviteParticipantForm.php";

class ProcloudParticipantPage extends PMPage
{
    function needDisplayForm()
    {
        return true;
    }
    
 	function getTable() 
 	{
		return null;	
 	}

 	function getForm() 
 	{
 		global $model_factory, $_REQUEST;
 		
		switch ( $_REQUEST['mode'] )
		{
			case 'invite':
				return new InviteParticipantForm ( 
					$model_factory->getObject('pm_Participant'), $_REQUEST['Email'] );
				
			default:
				return new FindParticipantForm ( 
					$model_factory->getObject('pm_Participant') );
		}
 	}
}