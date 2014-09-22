<?php

include "FieldIssueTrace.php";
include "RequestTraceInverseFormEmbedded.php";
 
class FieldIssueInverseTrace extends FieldIssueTrace
{
 	function getFilters()
 	{
		return array ( 
				new RequestTraceObjectPredicate(is_object($this->getObjectIt()) ? $this->getObjectIt() : 0) 
		);  
 	}
 	
 	function getForm( & $trace )
	{
		return new RequestTraceInverseFormEmbedded( $trace, 'ObjectId' );
	}
}
