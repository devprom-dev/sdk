<?php

class FeatureStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		switch ( $filter )
		{
			case 'closed':
				return " AND EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function = t.pm_FunctionId)
				         AND NOT EXISTS (
				            SELECT 1 FROM pm_ChangeRequest r 
				             WHERE r.Function = t.pm_FunctionId
				               AND r.FinishDate IS NULL ) ";
			case 'open':
                return " AND (
                            NOT EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function = t.pm_FunctionId)
				            OR EXISTS (
                                SELECT 1 FROM pm_ChangeRequest r 
                                 WHERE r.Function = t.pm_FunctionId
                                   AND r.FinishDate IS NULL )
                         ) ";

			default:
				return '';
		}
 	}
}
