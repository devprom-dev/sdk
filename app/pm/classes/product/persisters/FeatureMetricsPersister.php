<?php

class FeatureMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $terminal = getFactory()->getObject('Request')->getTerminalStates();
         $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
         
         $columns = array();

         if ( class_exists('FunctionTraceRequirement') ) {
             $columns[] =
                 "	GREATEST(
                    COALESCE((SELECT MIN(r.EstimatedStartDate) 
                       FROM pm_ChangeRequest r, pm_Function f
                      WHERE r.Function = f.pm_FunctionId 
                        AND f.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')), 0),
                    COALESCE((SELECT MIN(r.EstimatedStartDate)
                       FROM pm_ChangeRequestTrace l, pm_ChangeRequest r, WikiPage c, WikiPage cparent, pm_FunctionTrace f, pm_Function fn
                      WHERE l.ObjectId = c.WikiPageId 
                        AND f.Feature = fn.pm_FunctionId 
                        AND fn.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')  
                        AND f.ObjectClass = '" . getFactory()->getObject('FunctionTraceRequirement')->getObjectClass() . "' 
                        AND cparent.WikiPageId = f.ObjectId
                        AND c.ParentPath LIKE CONCAT(cparent.ParentPath, '%')
                  	    AND l.ChangeRequest = r.pm_ChangeRequestId 
                        AND l.ObjectClass = '" . getFactory()->getObject('RequestTraceRequirement')->getObjectClass() . "'),0), 
                    COALESCE((SELECT MIN(ts.EstimatedStartDate) 
                       FROM pm_TaskTrace l, WikiPage c, WikiPage cparent, pm_FunctionTrace f, pm_Function fn, pm_Task ts 
                      WHERE l.ObjectId = c.WikiPageId 
                        AND f.Feature = fn.pm_FunctionId 
                        AND fn.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')  
                        AND f.ObjectClass = '" . getFactory()->getObject('FunctionTraceRequirement')->getObjectClass() . "' 
                        AND cparent.WikiPageId = f.ObjectId
                        AND c.ParentPath LIKE CONCAT(cparent.ParentPath, '%')
                        AND l.ObjectClass = '" . getFactory()->getObject('TaskTraceRequirement')->getObjectClass() . "'
                        AND l.Task = ts.pm_TaskId),0)
                   ) MetricStartDate ";
         }
         else {
             $columns[] = "	
                   COALESCE((SELECT MIN(r.EstimatedStartDate) 
                               FROM pm_ChangeRequest r, pm_Function f
                              WHERE r.Function = f.pm_FunctionId 
                                AND f.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')), 0) MetricStartDate ";
         }

         if ( class_exists('FunctionTraceRequirement') ) {
             $columns[] =
                 "	GREATEST(
                    COALESCE((SELECT MAX(IFNULL(r.FinishDate,IFNULL(r.EstimatedFinishDate, r.DeliveryDate))) 
                       FROM pm_ChangeRequest r, pm_Function f
                      WHERE r.Function = f.pm_FunctionId 
                        AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')), 0),
                    COALESCE((SELECT MAX(IFNULL(r.FinishDate,IFNULL(r.EstimatedFinishDate, r.DeliveryDate)))
                       FROM pm_ChangeRequestTrace l, pm_ChangeRequest r, WikiPage c, WikiPage cparent, pm_FunctionTrace f, pm_Function fn
                      WHERE l.ObjectId = c.WikiPageId 
                        AND f.Feature = fn.pm_FunctionId 
                        AND fn.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')  
                        AND f.ObjectClass = '" . getFactory()->getObject('FunctionTraceRequirement')->getObjectClass() . "' 
                        AND cparent.WikiPageId = f.ObjectId
                        AND c.ParentPath LIKE CONCAT(cparent.ParentPath, '%')
                  	    AND l.ChangeRequest = r.pm_ChangeRequestId 
                        AND l.ObjectClass = '" . getFactory()->getObject('RequestTraceRequirement')->getObjectClass() . "'),0), 
                    COALESCE((SELECT MAX(ts.EstimatedFinishDate) 
                       FROM pm_TaskTrace l, WikiPage c, WikiPage cparent, pm_FunctionTrace f, pm_Function fn, pm_Task ts 
                      WHERE l.ObjectId = c.WikiPageId 
                        AND f.Feature = fn.pm_FunctionId 
                        AND fn.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')  
                        AND f.ObjectClass = '" . getFactory()->getObject('FunctionTraceRequirement')->getObjectClass() . "' 
                        AND cparent.WikiPageId = f.ObjectId
                        AND c.ParentPath LIKE CONCAT(cparent.ParentPath, '%')
                        AND l.ObjectClass = '" . getFactory()->getObject('TaskTraceRequirement')->getObjectClass() . "'
                        AND l.Task = ts.pm_TaskId),0)
                   ) MetricDeliveryDate ";
         }
         else {
             $columns[] = "	
                   COALESCE((SELECT MAX(IFNULL(r.FinishDate,IFNULL(r.EstimatedFinishDate, r.DeliveryDate))) 
                               FROM pm_ChangeRequest r, pm_Function f
                              WHERE r.Function = f.pm_FunctionId 
                                AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')), 0) MetricDeliveryDate ";
         }


         $columns[] =
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) 
                   FROM pm_ChangeRequest r, pm_Function f
                  WHERE r.Function = f.pm_FunctionId 
                    AND f.ParentPath LIKE CONCAT({$alias}.ParentPath, '%') ) MetricEstimation ";
         
         $columns[] = 
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) 
                   FROM pm_ChangeRequest r, pm_Function f
                  WHERE r.Function = f.pm_FunctionId 
                    AND f.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')
                    AND r.State NOT IN ('".join("','", $terminal)."')) MetricEstimationLeft ";
         
         $columns[] = 
             "	(SELECT ROUND(".$strategy->getEstimationAggregate()."(IF(pr.Rating <= 0, 0, r.Estimation / pr.Rating)), 1) 
                   FROM pm_ChangeRequest r, pm_Project pr, pm_Function f
                  WHERE r.Function = f.pm_FunctionId 
                    AND f.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')
                    AND r.Project = pr.pm_ProjectId 
                    AND r.State NOT IN ('".join("','", $terminal)."')) MetricWorkload ";
             
         return $columns;
     }
}
