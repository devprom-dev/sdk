<?php

class FeatureStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $states = \WorkflowScheme::Instance()->getNonTerminalStates(getFactory()->getObject('Request'));
		switch ( $filter )
		{
			case 'closed':
				return " AND EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function = t.pm_FunctionId)
				         AND NOT EXISTS (
				            SELECT 1 FROM pm_ChangeRequest r 
				             WHERE r.Function = t.pm_FunctionId
				               AND r.State IN ('".join("','", $states)."')) ";
			case 'open':
                return " AND (
                            NOT EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function = t.pm_FunctionId)
				            OR EXISTS (
                                SELECT 1 FROM pm_ChangeRequest r 
                                 WHERE r.Function = t.pm_FunctionId
                                   AND r.State IN ('".join("','", $states)."'))
                         ) ";

			default:
				return '';
		}
 	}
}
