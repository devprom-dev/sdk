<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class StateFilterResolver
{
	public function __construct( $states = '' )
	{
		$this->states = array_filter(preg_split('/,/', $states), function($value) {
				return $value != ''; 
		});
	}
	
	public function resolve()
	{
		$filters = array();
		
		if ( count($this->states) > 0 )
		{
			$filters[] = new \StatePredicate($this->states);
		}
		
		return $filters;
	}
	
	private $states = array();
}