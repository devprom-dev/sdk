<?php

namespace Devprom\Component\HttpKernel;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ServiceDeskAppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \Devprom\CommonBundle\CommonBundle(),
            new \Devprom\ServiceDeskBundle\DevpromServiceDeskBundle(),
            new \Shapecode\Bundle\HiddenEntityTypeBundle\ShapecodeHiddenEntityTypeBundle(),
            new \FR3D\LdapBundle\FR3DLdapBundle()
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $dynamicSettingsFile = SERVER_ROOT_PATH . 'co/bundles/Devprom/ServiceDeskBundle/Resources/config/settings.yml';
    	
        // setup initial custom parameters
        if (file_exists($dynamicSettingsFile)) $loader->load($dynamicSettingsFile);
        
        $loader->load(SERVER_ROOT_PATH . 'co/bundles/Devprom/ServiceDeskBundle/Resources/config/config_' . $this->getEnvironment() . '.yml');

        // override parameters using custom values
        if (file_exists($dynamicSettingsFile)) $loader->load($dynamicSettingsFile);
    }

    public function getRootDir()
    {
        return SERVER_ROOT_PATH . "co/bundles/Devprom/ServiceDeskBundle";
    }

    public function getCacheDir()
    {
        return CACHE_PATH . '/symfony2sd';
    }

    public function getLogDir()
    {
        return defined('SERVER_LOGS_PATH') ? SERVER_LOGS_PATH : dirname($this->getCacheDir()) . '/logs';
    }

    public function getCharset()
    {
        return APP_ENCODING;
    }

    public static function loadWithoutRequest()
    {
        $kernel = new ServiceDeskAppKernel('prod', false);
        $kernel->boot();
        $requestContext = new \Symfony\Component\Routing\RequestContext();

        list($requestScheme, $host, $baseUrl) = self::getRequestContextSettings($kernel->getContainer());

        $requestContext->setHost($host);
        $requestContext->setScheme($requestScheme);
        $requestContext->setBaseUrl($baseUrl);
        $kernel->getContainer()->get('router')->setContext($requestContext);
        return $kernel;
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    protected static function getRequestContextSettings($container)
    {
        if ($container->hasParameter("appUrl") && ($appUrl = $container->getParameter("appUrl")) !== null) {
            $pathParts = parse_url($appUrl);
            $requestScheme = $pathParts['scheme'];
            $host = $pathParts['host'];
            $baseUrl = $pathParts['path'];
            return array($requestScheme, $host, $baseUrl);
        } else {
            $requestScheme = \EnvironmentSettings::getServerSchema();
            $host = \EnvironmentSettings::getServerName();
            $baseUrl = "/servicedesk";
            return array($requestScheme, $host, $baseUrl);
        }
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
}
