<?php

namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class ExecutorFilterResolver
{
	public function __construct( $parms = '', $field = 'Owner' )
	{
		$this->field = $field;
		$this->executors = array_filter(preg_split('/,/', $parms), function($value) {
				return $value != ''; 
		});
	}
	
	public function resolve()
	{
		$filters = array();
		foreach( $this->executors as $executor ) {
			if ( $executor == 'iam' ) {
				$filters[] = new \FilterAttributePredicate($this->field, getSession()->getUserIt()->getId());
			}
			else {
				$filters[] = new \FilterAttributePredicate(
					$this->field,
					getFactory()->getObject('User')->getByRef('Email', $executor)->getId()
				);
			}
		}
		return $filters;
	}

	private $field = '';
	private $executors = array();
}