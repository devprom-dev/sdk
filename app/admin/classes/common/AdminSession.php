<?php

include SERVER_ROOT_PATH."admin/classes/notificators/AdminEmailNotificator.php";
include SERVER_ROOT_PATH."admin/classes/notificators/AdminChangeLogNotificator.php";
include SERVER_ROOT_PATH."admin/classes/notificators/AdminSystemTriggers.php";
include SERVER_ROOT_PATH."admin/classes/notificators/ProcessFirstUserEvent.php";
include SERVER_ROOT_PATH."admin/classes/model/events/UpdateBlockedParticipantsEvent.php";
include SERVER_ROOT_PATH."admin/classes/model/events/RecentBackupCreatedEvent.php";
include SERVER_ROOT_PATH."admin/classes/maintenance/MaintenanceModuleBuilder.php";

class AdminSession extends SessionBase
{
	function __construct()
	{
		parent::__construct();
		
		getLanguage();
	}

 	function getSite()
 	{
 	    return 'admin';
 	}
	
	function getCacheKey()
 	{
 		return 'admin';	
 	}
 	
 	function getApplicationUrl()
 	{
 	    return '/admin/';
 	}
 	
 	function createBuilders()
 	{
 	    return array_merge( parent::createBuilders(), array (
 	            new AdminEmailNotificator(),
 	            new AdminChangeLogNotificator(),
 	            new AdminSystemTriggers(),
 	    		new ProcessFirstUserEvent(),
 	    		new UpdateBlockedParticipantsEvent(),
 	    		new MaintenanceModuleBuilder(),
 	    		new RecentBackupCreatedEvent()
 	    ));
 	}
}
