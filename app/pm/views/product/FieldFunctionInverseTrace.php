<?php

include_once "FieldFunctionTrace.php";
include_once "FunctionTraceInverseFormEmbedded.php";

class FieldFunctionInverseTrace extends FieldFunctionTrace
{
 	function setFilters( & $trace )
 	{
		$trace->addFilter( 
			new FunctionTraceObjectPredicate( 
				is_object($this->object_it) 
					? get_class($this->object_it->object).','.$this->object_it->getId() : 0 ) 
		);
 	}
 	
 	function getForm( & $trace )
	{
		return new FunctionTraceInverseFormEmbedded( $trace, 'ObjectId' );
	}
}
