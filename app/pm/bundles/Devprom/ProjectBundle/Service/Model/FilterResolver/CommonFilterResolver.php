<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class CommonFilterResolver
{
	public function __construct( $ids = '' )
	{
		$this->ids = array_filter(preg_split('/,/', $ids), function($value) {
				return is_numeric($value) && $value > 0; 
		});
	}
	
	public function resolve()
	{
		$filters = array();
		
		if ( count($this->ids) > 0 )
		{
			$filters[] = new \FilterInPredicate($this->ids);
		}
		
		return $filters;
	}
	
	private $ids = '';
}