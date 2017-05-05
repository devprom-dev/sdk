<?php

namespace Devprom\Component\HttpKernel;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;
use Devprom\AdministrativeBundle\AdministrativeBundle;
include_once SERVER_ROOT_PATH."admin/classes/common/SessionBuilderAdmin.php";

class AdminApplicationKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        	new \Devprom\AdministrativeBundle\AdministrativeBundle(),
	    	new \Devprom\CommonBundle\CommonBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(SERVER_ROOT_PATH."admin/bundles/Devprom/AdministrativeBundle/Resources/config/config.yml");
    }

    public function getRootDir() {
    	return SERVER_ROOT_PATH."admin/bundles/Devprom/AdministrativeBundle";
    }

    public function getCacheDir() {
    	return CACHE_PATH.'/symfony2admin';
    }

    public function getLogDir() {
        return defined('SERVER_LOGS_PATH') ? SERVER_LOGS_PATH : dirname($this->getCacheDir()) . '/logs';
    }

    public function getCharset() {
        return APP_ENCODING;
    }

    function initializeContainer()
    {
        try {
            parent::initializeContainer();
        }
        catch( \Exception $e ) {
            error_log($e->getMessage().PHP_EOL.$e->getTraceAsString());
        }
    }

    function boot()
    {
        global $session, $model_factory;
        $lock = new \CacheLock();

        $model_factory = new \ModelFactoryExtended(
            \PluginsFactory::Instance(),
            \CacheEngineFS::Instance(),
            'global',
            new \AccessPolicy(\CacheEngineFS::Instance())
        );
        $session = \SessionBuilderAdmin::Instance()->openSession(array(), \CacheEngineFS::Instance());

        parent::boot();
        $lock->Release();
    }
}
