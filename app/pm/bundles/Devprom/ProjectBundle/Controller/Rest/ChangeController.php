<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Devprom\ProjectBundle\Service\Model\FilterResolver\ChangesFilterResolver;

class ChangeController extends RestController
{
	function getEntity()
	{
		$registry = new \ChangeLogGranularityRegistry();
		
		$granularity_map = array (
				'day' => \ChangeLogGranularityRegistry::DAY,
				'hour' => \ChangeLogGranularityRegistry::HOUR
		);
				
		$registry->setGranularity(
				isset($granularity_map[$this->getRequest()->get('granularity')]) 
						? $granularity_map[$this->getRequest()->get('granularity')] : \ChangeLogGranularityRegistry::SECOND    
		);
		
		return new \ChangeLog($registry);
	}
	
	function getFilterResolver()
	{
		return new ChangesFilterResolver(
				$this->getRequest()->get('classes'),
				$this->getRequest()->get('date'),
				$this->getRequest()->get('from')
		);
	}
}