<?php
include_once "FieldComponentTrace.php";
include_once "ComponentTraceInverseFormEmbedded.php";

class FieldComponentInverseTrace extends FieldComponentTrace
{
 	function setFilters( & $trace )
 	{
		$trace->addFilter( 
			new ComponentTraceObjectPredicate(
				is_object($this->object_it) 
					? get_class($this->object_it->object).','.$this->object_it->getId() : 0 )
		);
 	}
 	
 	function getForm( & $trace )
	{
	    $form = new ComponentTraceInverseFormEmbedded( $trace, 'ObjectId', $this->getName() );
        $form->setTraceObject(getFactory()->getObject('Component'));
		return $form;
	}
}
