<?php

class FeatureDatesPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         global $model_factory;
         
         $request = $model_factory->getObject('Request');
         
         $terminal = $request->getTerminalStates();

         $columns = array();
         
         $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
         
         $columns[] = 
             "	(SELECT MIN(r.StartDate) " .
             "	   FROM pm_ChangeRequest r" .
             "	  WHERE r.Function = ".$this->getPK($alias).") StartDate ";

         $columns[] = 
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) ".
             "	   FROM pm_ChangeRequest r" .
             "	  WHERE r.Function = ".$this->getPK($alias).") Estimation ";
         
         $columns[] = 
             "	(SELECT ".$strategy->getEstimationAggregate()."(r.Estimation) ".
             "	   FROM pm_ChangeRequest r" .
             "	  WHERE r.Function = ".$this->getPK($alias).
             "	    AND r.State NOT IN ('".join("','", $terminal)."')) EstimationLeft ";
         
         $columns[] = 
             "	(SELECT IF(pr.Rating <= 0, 0, ROUND(".$strategy->getEstimationAggregate()."(r.Estimation) / pr.Rating, 1)) ".
             "	   FROM pm_ChangeRequest r, pm_Project pr" .
             "	  WHERE r.Function = ".$this->getPK($alias).
             "		AND r.Project = pr.pm_ProjectId ".
             "	    AND r.State NOT IN ('".join("','", $terminal)."')) Workload ";
             
         $columns[] = 
             "	(SELECT IFNULL( ".
             "    (SELECT FROM_DAYS(TO_DAYS(NOW()) + IF(pr.Rating <= 0, 0, ROUND(".$strategy->getEstimationAggregate()."(r.Estimation) / pr.Rating, 1))) ".
             "	   FROM pm_ChangeRequest r, pm_Project pr " .
             "	  WHERE r.Function = ".$this->getPK($alias).
             "		AND r.Project = pr.pm_ProjectId ".
             "	    AND r.State NOT IN ('".join("','", $terminal)."')), ".
             "	  (SELECT MAX(r.FinishDate) " .
             "	     FROM pm_ChangeRequest r" .
             "	    WHERE r.Function = ".$this->getPK($alias).") ) ) DeliveryDate ";
         
         return $columns;
     }
}
