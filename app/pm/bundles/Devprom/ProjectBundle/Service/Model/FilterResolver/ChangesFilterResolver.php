<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class ChangesFilterResolver
{
	public function __construct( $classes = '', $date = '', $from = '' )
	{
		$this->classes = preg_split('/,/', $classes);
		
		array_walk($this->classes, function(&$value)
		{
				return $value = strtolower(trim($value));
		});
		
		$this->date = $date;
		$this->from = $from;
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
			$filters[] = new \FilterSubmittedDatePredicate($this->date);
		}
			
		if ( $this->from != '' )
		{
			$filters[] = new \FilterModifiedAfterPredicate($this->from);
		}
		
		$filters[] = new \SortAttributeClause('ObjectChangeLogId.D');
		
		return $filters;
	}
	
	private $classes = '';
	private $date = '';
	private $from = '';
}