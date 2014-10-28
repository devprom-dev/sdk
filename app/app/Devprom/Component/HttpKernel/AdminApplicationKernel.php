<?php

namespace Devprom\Component\HttpKernel;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;
use Devprom\AdministrativeBundle\AdministrativeBundle;

class AdminApplicationKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
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

    public function getRootDir()
    {
    	return SERVER_ROOT_PATH."admin/bundles/Devprom/AdministrativeBundle";
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
