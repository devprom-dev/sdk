<?php

class TaskTypeBaseCategoryPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $references = preg_split('/,/', $filter);
 	    
 		return " AND EXISTS (SELECT 1 FROM pm_TaskType b WHERE b.pm_TaskTypeId = t.ParentTaskType ".
 		       "                AND b.ReferenceName IN ('".join("','", $references)."') ) ";
 	}
}
