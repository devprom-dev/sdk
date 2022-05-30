<?php

class RequestTaskTypePlannedPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
 	{
 	    $sqls = array();

		$taskTypeIt = getFactory()->getObject('TaskTypeUnified')->getAll();
		while( !$taskTypeIt->end() ) {
		    $taskTypeKey = $taskTypeIt->getId();
            $sqls = array_merge( $sqls, array(
                " ( SELECT IFNULL(SUM(a.Planned),0) 
                     FROM pm_Task a, pm_TaskType tp
                    WHERE a.ChangeRequest = {$this->getPK($alias)} 
                      AND a.TaskType = tp.pm_TaskTypeId
                      AND tp.ReferenceName = '{$taskTypeKey}' ) Planned".$taskTypeKey." "
            ));
            $taskTypeIt->moveNext();
        }

        return $sqls;
 	}
}
