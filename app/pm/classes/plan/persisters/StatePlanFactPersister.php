<?php

class StatePlanFactPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array('Planned', 'Fact');
    }

    function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();

        if ( $methodologyIt->HasTasks() && $methodologyIt->TaskEstimationUsed() ) {
            $columns[] = " t.TasksPlanned Planned ";
        }
        else {
            $columns[] = " t.IssuesPlanned Planned ";
        }

 	    if ( $methodologyIt->IsReportsRequiredOnActivities() ) {
            if ( $methodologyIt->HasTasks() ) {
                $columns[] = " t.TasksFact Fact ";
            }
            else {
                $columns[] = " t.IssuesFact Fact ";
            }
        }
 	    else {
            $columns[] = " 0 Fact ";
        }

 		return $columns;
 	}
}
