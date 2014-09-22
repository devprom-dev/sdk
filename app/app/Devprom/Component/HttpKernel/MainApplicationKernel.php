<?php

namespace Devprom\Component\HttpKernel;

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
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
        	new \Devprom\ApplicationBundle\ApplicationBundle(),
            new \Devprom\CommonBundle\CommonBundle()
        );

        return $bundles;
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(SERVER_ROOT_PATH.'co/bundles/Devprom/ApplicationBundle/Resources/config/config.yml');
    }

    public function getRootDir()
    {
    	return SERVER_ROOT_PATH."co/bundles/Devprom/ApplicationBundle";
    }

    public function getCacheDir()
    {
    	return CACHE_PATH.'/symfony2';
    }

    public function getCharset()
    {
        return 'windows-1251';
    }
}
