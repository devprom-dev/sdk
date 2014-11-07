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
 		$request_it = getFactory()->getObject('Request')->getRegistry()->Query(
	    		array (
	    				new RequestCodeCommitPredicate($commit),
	    				new FilterVpdPredicate()
	    		)
	    );

	    while ( !$request_it->end() )
	    {
	        $request_it->modify( 
	        		array( 
	                		'ClosedInVersion' => $build
	        		)
	        );
	        
	        $request_it->moveNext();
	    }
 	}
}
