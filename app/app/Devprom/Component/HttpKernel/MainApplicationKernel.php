<?php

namespace Devprom\Component\HttpKernel;
include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;
use Devprom\ApplicationBundle\ApplicationBundle;
use Symfony\Component\Routing\Exception;

class MainApplicationKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
        	new \Devprom\ApplicationBundle\ApplicationBundle(),
            new \Devprom\CommonBundle\CommonBundle()
        );

        return $bundles;
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(SERVER_ROOT_PATH . 'co/bundles/Devprom/ApplicationBundle/Resources/config/config.yml');
        $dynamicSettingsFile = SERVER_ROOT_PATH . 'co/bundles/Devprom/ApplicationBundle/Resources/config/settings.yml';
        if (file_exists($dynamicSettingsFile)) $loader->load($dynamicSettingsFile);
    }

    public function getRootDir()
    {
    	return SERVER_ROOT_PATH."co/bundles/Devprom/ApplicationBundle";
    }

    public function getLogDir()
    {
        return defined('SERVER_LOGS_PATH') ? SERVER_LOGS_PATH : dirname($this->getCacheDir()) . '/logs';
    }

    public function getCacheDir()
    {
    	return CACHE_PATH.'/symfony2app';
    }

    public function getCharset()
    {
        return APP_ENCODING;
    }

    function initializeContainer()
    {
        $lock = new \CacheLock();
        try {
            parent::initializeContainer();
        }
        catch( \Exception $e ) {
            error_log($e->getMessage().PHP_EOL.$e->getTraceAsString());
        }
    }
}
