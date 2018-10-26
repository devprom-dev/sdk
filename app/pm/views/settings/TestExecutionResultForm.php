<?php

class TestExecutionResultForm extends PMPageForm
{
	function __construct() {
		parent::__construct( getFactory()->getObject('pm_TestExecutionResult') );
	}

	function createField( $attribute ) 
	{
	    $field = parent::createField($attribute);
	    
	    switch ( $attribute )
	    {
	        case 'ReferenceName':
	            $object_it = $this->getObjectIt();
	            if ( is_object($object_it) ) {
	                $field->setReadonly( in_array($object_it->get($attribute), array('succeeded', 'failed')) );
	            }
	    }
	    
	    return $field;
	}
}