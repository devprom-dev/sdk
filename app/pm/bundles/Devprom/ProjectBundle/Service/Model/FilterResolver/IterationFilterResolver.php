<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class IterationFilterResolver
{
	public function __construct( $filter = '' )
	{
		$this->filter = $filter;
	}
	
	public function resolve()
	{
		$object = getFactory()->getObject('Iteration');
		
		$filters = array();
		
		$names = preg_split('/,/', $this->filter);
		
		foreach( $names as $filter )
		{
			switch($filter)
			{
			    case 'current':
			    	
			    	$filters[] = new \IterationTimelinePredicate(\IterationTimelinePredicate::CURRENT); 
			    	
			    	break;
			}
		}
		
		return $filters;
	}
	
	private $filter = '';
}