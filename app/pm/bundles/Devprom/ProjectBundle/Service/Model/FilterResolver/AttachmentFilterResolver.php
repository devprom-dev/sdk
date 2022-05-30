<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class AttachmentFilterResolver
{
	public function __construct( $class_name, $object_id = '' )
	{
		$this->class_name = $class_name;
		$this->object_id = $object_id;
	}
	
	public function resolve()
	{
		$predicates = array(
            new \FilterAttributePredicate('ObjectClass',
                $this->class_name == 'request'
                        ? array('request','issue')
                        : $this->class_name
                )
		);
		if ( $this->object_id != '' ) {
			$predicates[] = new \FilterAttributePredicate('ObjectId', $this->object_id);
		}
		return $predicates;
	}

	private $class_name = '';
	private $object_id = '';
}