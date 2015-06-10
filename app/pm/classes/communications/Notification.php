<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "NotificationRegistry.php";

class Notification extends PMObjectCacheable
{
 	function __construct()
 	{
 		parent::__construct('entity', new NotificationRegistry($this));
 	}
 	
 	function getParticipantIt( $notification_type )
 	{
 		global $model_factory;
 		
 		$sql = " SELECT p.* " .
 			   "   FROM pm_Participant p " .
 			   "  WHERE p.IsActive = 'Y' " .
 			   "	AND EXISTS (SELECT 1 FROM pm_Project t WHERE t.pm_ProjectId = p.Project AND IFNULL(t.IsClosed,'N') = 'N') ". 
 			   "	AND EXISTS (SELECT 1 FROM pm_UserSetting t " .
 			   "				 WHERE t.Value = CONCAT('email=', '".$notification_type."')" .
 			   "    			   AND t.Setting = '".md5( 'emailnotification' )."' ".
 			   "    			   AND p.pm_ParticipantId = t.Participant ) ";

 		$part = $model_factory->getObject('pm_Participant');
 		return $part->createSQLIterator( $sql );
 	}
 
 	function getType( $participant_it )
 	{	
 	    $settings = getSession()->getUserSettings();
 	    
		$parts = preg_split('/=/', 
			$settings->getSettingsValue( md5( 'emailnotification' ), $participant_it->getId() ) 
			);
		
		return $parts[1];
 	}
 	
 	function store( $value, $participant_it = null )
 	{
 	    $settings = getSession()->getUserSettings();
 	     		
		$settings->setSettingsValue(
			md5( 'emailnotification' ), 'email='.$value, is_object($participant_it) ? $participant_it->getId() : 0 
		);
 	}
} 
