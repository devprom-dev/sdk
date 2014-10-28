<?php

namespace Devprom\AdministrativeBundle;

use Devprom\Component\HttpKernel\Bundle\DevpromBundle;

include_once SERVER_ROOT_PATH."admin/classes/model/ModelFactoryAdmin.php";
include_once SERVER_ROOT_PATH."admin/classes/common/AdminAccessPolicy.php";
include_once SERVER_ROOT_PATH."admin/classes/common/AdminSession.php";
include_once SERVER_ROOT_PATH."admin/views/Common.php";

class AdministrativeBundle extends DevpromBundle
{
	protected function buildModelFactory()
	{
		return new \ModelFactoryAdmin(
				$this->getPluginsFactory(), $this->getCacheService(), $this->buildAccessPolicy()
			);
	}

	protected function buildAccessPolicy()
	{
		return new \AdminAccessPolicy($this->getCacheService());
	}

	protected function buildSession()
	{
		return new \AdminSession(null, null, null, $this->getCacheService());		
	}
	
	protected function buildCacheService()
	{
		$service = parent::buildCacheService();
		
		$service->setDefaultPath('admin');
		
		return $service;
	}
}
