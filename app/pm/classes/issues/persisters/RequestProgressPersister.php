<?php

class RequestProgressPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array(
            'HoursProgress', 'TaskTypeProgress'
        );
    }

    function getSelectColumns( $alias )
 	{
 	    $result = array(
            "(SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(':',tt.Caption,tt.RelatedColor)) FROM pm_Task s, pm_TaskType tt " .
            "  WHERE s.StateObject IS NOT NULL AND s.FinishDate IS NULL ".
            "    AND s.TaskType = tt.pm_TaskTypeId AND s.ChangeRequest = ".$this->getPK($alias)." ) TaskTypeProgress "
        );

 	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	    if ( $methodology_it->TaskEstimationUsed() ) {
            $result[] =
                "(SELECT IF( SUM(s.Planned) = 0, 0, CEIL((GREATEST(0,1 - SUM(s.LeftWork)) / SUM(s.Planned)) * 100)) FROM pm_Task s " .
                "  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) HoursProgress ";
        }
 	    else {
            $result[] =
                "(SELECT IF( ".$alias.".Estimation, 0, CEIL((GREATEST(0,1 - SUM(s.LeftWork)) / ".$alias.".Estimation) * 100)) FROM pm_Task s " .
                "  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) HoursProgress ";
        }

 	    return $result;
 	}
}
