<?php

class BuildCodeRevisionPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT '' ) BuildRevision " 
 		);
 	}
 	
 	function add( $object_id, $parms )
 	{
 		if ( $parms['BuildRevision'] == '' ) return;

 		$this->resolveIssues($parms['BuildRevision'], $parms['Caption']);
 	}
 	
 	function modify( $object_id, $parms )
 	{
 		if ( $parms['BuildRevision'] == '' ) return;

 		$this->resolveIssues($parms['BuildRevision'], $parms['Caption']);
 	}
 	
 	private function resolveIssues( $commit, $build )
 	{
 		$request = getFactory()->getObject('Request');
 		
 		$request_it = $request->getRegistry()->Query(
	    		array (
	    				new RequestCodeCommitPredicate($commit),
	    				new FilterVpdPredicate()
	    		)
	    );

	    while ( !$request_it->end() )
	    {
	    	$request->modify_parms( $request_it->getId(),
	        		array( 
	                		'ClosedInVersion' => $build
	        		)
	        );
	        
	        $request_it->moveNext();
	    }
 	}
}
