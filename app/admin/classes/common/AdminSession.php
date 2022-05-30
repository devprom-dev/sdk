<?php
include_once SERVER_ROOT_PATH."core/classes/project/PortfolioAllBuilder.php";
include SERVER_ROOT_PATH."admin/classes/model/ModelFactoryAdmin.php";
include SERVER_ROOT_PATH."admin/classes/common/AdminAccessPolicy.php";
include SERVER_ROOT_PATH."admin/classes/notificators/AdminChangeLogNotificator.php";
include SERVER_ROOT_PATH."admin/classes/notificators/AdminSystemTriggers.php";
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
 		return 'admin-'.$this->getLanguageUid();
 	}
 	
 	function getApplicationUrl()
 	{
 	    return '/admin/';
 	}

    function buildFactories()
    {
        global $model_factory;

        $model_factory = new \ModelFactoryAdmin(
            \PluginsFactory::Instance(),
            getFactory()->getCacheService(),
            $this->getCacheKey(),
            new \AdminAccessPolicy(getFactory()->getCacheService(), $this->getCacheKey())
        );

        parent::buildFactories();
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
                new PortfolioAllBuilder(),
                new AdminChangeLogNotificator(),
                new AdminSystemTriggers(),
                new MaintenanceModuleBuilder(),
                new RecentBackupCreatedEvent()
            )
 	    );
 	}
}
