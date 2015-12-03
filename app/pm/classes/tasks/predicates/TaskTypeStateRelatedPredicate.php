<?php

class TaskTypeStateRelatedPredicate extends FilterPredicate
{
    private $any_allowed;

    function __construct( $filter, $any_allowed = false )
    {
        $this->any_allowed = $any_allowed;
        parent::__construct($filter);
    }

 	function _predicate( $filter )
 	{
		$stage_it = getFactory()->getObject('IssueState')->getByRef('ReferenceName', $filter);
 		if ( $stage_it->count() > 0 )
 		{
 			$sql = " EXISTS ( SELECT 1 FROM pm_TaskTypeState s " .
 				   "	       WHERE s.TaskType = t.pm_TaskTypeId" .
 				   "		     AND s.State = '".$stage_it->get('ReferenceName')."') ";

            if ( $this->any_allowed ) {
                $sql .= " OR NOT EXISTS (SELECT 1 FROM pm_TaskTypeState s " .
                        "			      WHERE s.State = '".$stage_it->get('ReferenceName')."'".
                        "                   AND s.VPD = t.VPD ) ";
            }

            return "AND (".$sql.")";
 		}
		else if ( !$this->any_allowed )
        {
			return " AND 1 = 2 ";
		}
 	}
}
