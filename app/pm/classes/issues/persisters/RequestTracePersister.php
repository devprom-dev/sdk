<?php

class RequestTracePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$trace = getFactory()->getObject('TaskTraceTask');
 		
 		return array( 
 			" '' TraceTask ",
 				
 			" '' TraceInversedTask ",
 			
 			" '' TraceTaskInfo "
 		);
 	}
}

