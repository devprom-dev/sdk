<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class WikiFileFilterResolver
{
	public function __construct( $object_id = '' ) {
		$this->object_id = $object_id;
	}
	
	public function resolve()
	{
		if ( $this->object_id != '' ) {
			$predicates[] = new \FilterAttributePredicate('WikiPage', $this->object_id);
		}
		return $predicates;
	}

	private $object_id = '';
}