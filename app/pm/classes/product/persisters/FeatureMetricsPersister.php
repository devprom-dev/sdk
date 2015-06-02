<?php

class FeatureMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $terminal = getFactory()->getObject('Request')->getTerminalStates();
         $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
         
         $columns = array();
         
         $columns[] = 
             "	(SELECT MIN(r.StartDate) " .
             "	   FROM pm_ChangeRequest r, pm_Function f" .
             "	  WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')) MetricStartDate ";

         $columns[] = 
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) ".
             "	   FROM pm_ChangeRequest r, pm_Function f" .
             "	  WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')) MetricEstimation ";
         
         $columns[] = 
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) ".
             "	   FROM pm_ChangeRequest r, pm_Function f" .
             "	  WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')".
             "	    AND r.State NOT IN ('".join("','", $terminal)."')) MetricEstimationLeft ";
         
         $columns[] = 
             "	(SELECT IF(pr.Rating <= 0, 0, ROUND(".$strategy->getEstimationAggregate()."(r.Estimation) / pr.Rating, 1)) ".
             "	   FROM pm_ChangeRequest r, pm_Project pr, pm_Function f" .
             "	  WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')".
             "		AND r.Project = pr.pm_ProjectId ".
             "	    AND r.State NOT IN ('".join("','", $terminal)."')) MetricWorkload ";
             
         $columns[] = 
             "	(SELECT IFNULL( ".
             "	  (SELECT MAX(r.DeliveryDate) " .
             "	     FROM pm_ChangeRequest r, pm_Function f" .
             "	    WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')), ".
             "    (SELECT FROM_DAYS(TO_DAYS(GREATEST(pr.FinishDate,NOW())) + IF(pr.Rating <= 0, 0, ROUND(".$strategy->getEstimationAggregate()."(r.Estimation) / pr.Rating, 1))) ".
             "	   FROM pm_ChangeRequest r, pm_Project pr, pm_Function f " .
             "	  WHERE r.Function = f.pm_FunctionId ".
         	 "		AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%')".
             "		AND r.Project = pr.pm_ProjectId ".
             "	    AND r.State NOT IN ('".join("','", $terminal)."')) ".
         	 "   )) MetricDeliveryDate ";
         
         return $columns;
     }
}
