<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class IterationFilterResolver
{
	public function resolve( $filter )
	{
		$object = getFactory()->getObject('Iteration');
		
		$filters = array();
		
		$names = preg_split('/,/', $filter);
		
		foreach( $names as $filter )
		{
			switch($filter)
			{
			    case 'current':
			    	
			    	$filters[] = new \IterationTimelinePredicate(\IterationTimelinePredicate::CURRENT); 
			    	
			    	break;
			    	
			    default:
			    	
			    	$filters[] = new \IterationTimelinePredicate('dummy'); 
			}
		}
		
		return $filters;
	}
}