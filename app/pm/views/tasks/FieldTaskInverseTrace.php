<?php

include_once "FieldTaskTrace.php";
include "TaskTraceInverseFormEmbedded.php";
 
class FieldTaskInverseTrace extends FieldTaskTrace
{
 	function setFilters( & $trace )
 	{
		$trace->addFilter( new TaskTraceObjectPredicate( 
				is_object($this->getObjectIt()) ? $this->getObjectIt() : $trace->getEmptyIterator() 
        ));
 	}
 	
 	function getForm( & $trace )
	{
		return new TaskTraceInverseFormEmbedded( $trace, 'ObjectId', $this->getName() );
	}
}
