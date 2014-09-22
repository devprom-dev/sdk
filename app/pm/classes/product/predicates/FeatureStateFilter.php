<?php

class FeatureStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$request = $model_factory->getObject('pm_ChangeRequest');

 		$request->addFilter( new StatePredicate('notresolved') );
 			
		switch ( $filter )
		{
			case 'closed':
			    
				return " AND t.pm_FunctionId NOT IN (" .
					   "		SELECT t.Function FROM pm_ChangeRequest t " .
					   "		 WHERE 1 = 1 ".$request->getFilterPredicate().
				       "           AND t.Function IS NOT NULL ) ";

			case 'open':
 		        
			    return " AND (t.pm_FunctionId IN (" .
					   "		SELECT t.Function FROM pm_ChangeRequest t " .
					   "		 WHERE 1 = 1 ".$request->getFilterPredicate()." )" .
					   "	  OR " .
					   "	  NOT EXISTS (SELECT 1 FROM pm_ChangeRequest r " .
					   "			  	   WHERE r.Function = t.pm_FunctionId )" .
					   "	  )";

			default:
				return '';
		}
 	}
}
