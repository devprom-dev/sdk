<?php

class PMWikiStageFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$stage = $model_factory->getObject('Stage');
 		
 		$stage_it = $stage->getExact($filter);
 		
 		$object = $this->getObject();
 		
 		if ( $stage_it->get('Release') > 0 )
 		{
 			return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestTrace tr, pm_Task ts" .
 				   "			  WHERE tr.ObjectId = t.WikiPageId" .
 				   "				AND tr.ObjectClass = '".strtolower(get_class($object))."'" .
 				   "				AND ts.ChangeRequest = tr.ChangeRequest" .
 				   "				AND ts.Release = ".$stage_it->get('Release')."" .
 				   "			  UNION " .
 				   "			 SELECT 1 FROM pm_TaskTrace tr, pm_Task ts" .
 				   "			  WHERE tr.ObjectId = t.WikiPageId" .
 				   "				AND tr.ObjectClass = '".strtolower(get_class($object))."'" .
 				   "				AND ts.pm_TaskId = tr.Task" .
 				   "				AND ts.Release = ".$stage_it->get('Release').") ";
 		}

 		if ( $stage_it->get('Version') > 0 )
 		{
 			return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestTrace tr, pm_ChangeRequest req " .
 				   "			  WHERE tr.ObjectId = t.WikiPageId" .
 				   "				AND tr.ObjectClass = '".strtolower(get_class($object))."'" .
 				   "				AND tr.ChangeRequest = req.pm_ChangeRequestId" .
 				   "			    AND req.PlannedRelease = ".$stage_it->get('Version').") ";
 		}
 	}
}
