<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class TimeFilterResolver
{
	public function __construct( $class_name, $object_id = '' )
	{
		$this->class_name = $class_name;
		$this->object_id = $object_id;
	}
	
	public function resolve()
	{
		if ( $this->class_name == 'request' ) {
			$predicates = array(
				new \ActivityRequestPredicate($this->object_id)
			);
		}
		else {
			$predicates[] = new \FilterAttributePredicate('Task', $this->object_id);
		}
		return $predicates;
	}

	private $class_name = '';
	private $object_id = '';
}