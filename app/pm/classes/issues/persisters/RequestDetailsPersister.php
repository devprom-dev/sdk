<?php

class RequestDetailsPersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
	}

 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = "(SELECT tp.Caption FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeName ";
        $columns[] = "(SELECT tp.ReferenceName FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeReferenceName ";

        $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodologyIt->HasTasks() ) {
            if ( $methodologyIt->TaskEstimationUsed() ) {
                $columns[] = " ( SELECT IFNULL(t.EstimationLeft, SUM(t.LeftWork)) FROM pm_Task t WHERE t.ChangeRequest = ".$this->getPK($alias)." ) LeftWork ";
                $columns[] = " ( SELECT IFNULL(t.EstimationLeft, SUM(t.LeftWork)) FROM pm_Task t WHERE t.ChangeRequest = ".$this->getPK($alias)." ) EstimationLeft ";
            }
            else {
                $columns[] = " t.EstimationLeft LeftWork ";
            }
        } else {
            $columns[] = " t.EstimationLeft LeftWork ";
        }

 		return $columns;
 	}

    function IsPersisterImportant() {
        return true;
    }
}
