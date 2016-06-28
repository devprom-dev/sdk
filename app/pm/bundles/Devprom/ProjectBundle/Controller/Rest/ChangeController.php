<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ProjectBundle\Service\Model\FilterResolver\ChangesFilterResolver;

class ChangeController extends RestController
{
	function getEntity(Request $request)
	{
		$registry = new \ChangeLogGranularityRegistry();
		
		$granularity_map = array (
				'day' => \ChangeLogGranularityRegistry::DAY,
				'hour' => \ChangeLogGranularityRegistry::HOUR
		);
				
		$registry->setGranularity(
				isset($granularity_map[$request->get('granularity')])
						? $granularity_map[$request->get('granularity')] : \ChangeLogGranularityRegistry::SECOND
		);
		
		return new \ChangeLog($registry);
	}
	
	function getFilterResolver(Request $request)
	{
		return array_merge(
			parent::getFilterResolver($request),
			array (
				new ChangesFilterResolver(
					$request->get('classes'),
					$request->get('date'),
					$request->get('from')
				)
			)
		);
	}
}