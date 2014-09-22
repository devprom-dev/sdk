<?php

class TaskTypeStageRelatedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$stage = $model_factory->getObject('pm_ProjectStage');
 		$stage_it = $stage->getExact( $filter );
 		
 		if ( $stage_it->count() > 0 )
 		{
 			return " AND EXISTS ( SELECT 1 FROM pm_TaskTypeStage s " .
 				   "			   WHERE s.TaskType = t.pm_TaskTypeId" .
 				   "				 AND s.ProjectStage = ".$stage_it->getId().") ";
 		}
 	}
}
