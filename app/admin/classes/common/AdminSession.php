<?php

include SERVER_ROOT_PATH."admin/classes/notificators/AdminChangeLogNotificator.php";
include SERVER_ROOT_PATH."admin/classes/notificators/AdminSystemTriggers.php";
include SERVER_ROOT_PATH."admin/classes/notificators/ProcessFirstUserEvent.php";
include SERVER_ROOT_PATH."admin/classes/model/events/RecentBackupCreatedEvent.php";
include SERVER_ROOT_PATH."admin/classes/maintenance/MaintenanceModuleBuilder.php";
include SERVER_ROOT_PATH."admin/classes/maintenance/MaintenanceJSBuilder.php";
include_once SERVER_ROOT_PATH."co/classes/ResourceBuilderCoLanguageFile.php";

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
 	    return array_merge(
 	    		array (
 	    				new ResourceBuilderCoLanguageFile(),
 	    				new MaintenanceJSBuilder(getSession())
 	    		),
 	    		parent::createBuilders(),
 	    		array (
		 	            new AdminChangeLogNotificator(),
		 	            new AdminSystemTriggers(),
		 	    		new ProcessFirstUserEvent(),
		 	    		new MaintenanceModuleBuilder(),
		 	    		new RecentBackupCreatedEvent()
 	    		)
 	    );
 	}
}
