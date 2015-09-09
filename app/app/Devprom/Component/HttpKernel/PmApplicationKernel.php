<?php
/*
 * Application kernel for 'pm' system section
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
namespace Devprom\Component\HttpKernel;
include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Exception;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PmApplicationKernel extends Kernel
{
    function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
        if($environment === 'dev'){
            $this->errorReportingLevel = E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE;
        }
    }
    
    public function registerBundles()
    {
        $bundles = array(
        	new \Symfony\Bundle\MonologBundle\MonologBundle(),
        	new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
        	new \FOS\RestBundle\FOSRestBundle(),
	    	new \JMS\SerializerBundle\JMSSerializerBundle(),
	    	new \Devprom\CommonBundle\CommonBundle(),
        	new \Devprom\ProjectBundle\ProjectBundle(),
	    	new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
        );

        return $bundles;
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(SERVER_ROOT_PATH.'pm/bundles/Devprom/ProjectBundle/Resources/config/config.yml');
    }

    public function getRootDir()
    {
    	return SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle";
    }

    public function getCacheDir()
    {
    	return CACHE_PATH.'/symfony2-pm';
    }

    public function getCharset()
    {
        return APP_ENCODING;
    }

    public function getLogDir()
    {
        return defined('SERVER_LOGS_PATH') ? SERVER_LOGS_PATH : dirname($this->getCacheDir()) . '/logs';
    }
    
    function initializeContainer()
    {
    	$lock = new \CacheLock();
		$lock->Locked(1) ? $lock->Wait(10) : $lock->Lock();
    	parent::initializeContainer();
    }
}
