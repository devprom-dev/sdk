<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class ChangesFilterResolver
{
	public function __construct( $classes = '', $date = '' )
	{
		$this->classes = preg_split('/,/', $classes);
		
		array_walk($this->classes, function(&$value)
		{
				return $value = strtolower(trim($value));
		});
		
		$this->date = $date;
	}
	

	public function resolve()
	{
		$filters = array();
		
		if ( count($this->classes) )
		{
			$filters[] = new \FilterAttributePredicate('ClassName', $this->classes);
		}

		if ( $this->date != '' )
		{
			$filters[] = new \FilterModifiedAfterPredicate($this->date);
		}
			
		return $filters;
	}
	
	private $classes = '';
	
	private $date = '';
}