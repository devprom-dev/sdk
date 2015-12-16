<?php
namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class RequirementFilterResolver
{
	public function __construct( $baseline = '' ) {
		$this->baseline = $baseline;
	}
	
	public function resolve()
	{
		$predicates = array();
		return $predicates;
	}

	private $baseline = '';
}