<?php

class BuildCodeRevisionPersister extends ObjectSQLPersister
{
		function getSelectColumns( $alias )
		{
			return array(
				" ( SELECT GROUP_CONCAT(DISTINCT CAST(r.pm_SubversionRevisionId AS CHAR))
 			 	  FROM pm_SubversionRevision r, pm_SubversionRevision r2
 			 	  WHERE r.RecordCreated <= r2.RecordCreated
 			 	  	AND t.BuildRevision = r2.pm_SubversionRevisionId
 			 	  	AND r2.Repository = r.Repository
 			 	    AND r.RecordCreated >
 			 	    	(SELECT MAX(r.RecordCreated) FROM pm_Build b, pm_SubversionRevision r
 			 	    	  WHERE b.pm_BuildId < t.pm_BuildId
 			 	    	    AND IFNULL(b.Application,'') = IFNULL(t.Application,'')
 			 	    	    AND b.BuildRevision = r.pm_SubversionRevisionId
 			 	    	    AND b.VPD IN ('".join("','",$this->getObject()->getVpds())."'))) Commits ",
				" (SELECT sr.Repository FROM pm_SubversionRevision sr WHERE sr.pm_SubversionRevisionId = t.BuildRevision) Repository "
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
