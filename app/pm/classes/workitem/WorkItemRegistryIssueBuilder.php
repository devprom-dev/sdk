<?php
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestDueDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestColorsPersister.php";
include "persisters/WorkItemStubFactPersister.php";

class WorkItemRegistryIssueBuilder extends WorkItemRegistryBuilder
{
    function build( WorkItemRegistry $registry, array $filters )
    {
        $request = getFactory()->getObject('Request');

        $issue_columns = array(" '' Dummy ");
        foreach( array (
                     new RequestDueDatesPersister(),
                     new RequestTagsPersister(),
                     new WorkItemStubFactPersister(),
                     new RequestColorsPersister()
                 ) as $persister )
        {
            $persister->setObject($request);
            $issue_columns = array_merge($issue_columns, $persister->getSelectColumns('t'));
        }

        $registry->mergeSQL(
            " SELECT t.pm_ChangeRequestId, 
			       IF(LEFT(t.UID, 1) = 'U', 'Issue', 'Request') as ObjectClass,
				   t.Priority,
				   t.Caption,
				   ".($registry->getDescriptionIncluded() ? "t.Description ": "'' Description").",
				   t.State,
				   t.StateObject,
				   IFNULL((SELECT p.Caption FROM pm_IssueType p WHERE p.pm_IssueTypeId = t.Type), 'issue') TaskType,
				   t.RecordCreated,
				   t.RecordModified,
				   t.RecordVersion,
				   t.StartDate,
				   t.FinishDate,
				   t.EstimatedStartDate,
				   t.EstimatedFinishDate,
				   t.Owner,
				   t.pm_ChangeRequestId,
				   t.OrderNum,
				   t.EstimationLeft,
				   t.Estimation,
				   t.Iteration,
				   t.Author,
				   t.PlannedRelease,
				   t.VPD,
				   t.Type,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a, pm_Task s
				     WHERE a.Task = s.pm_TaskId AND s.ChangeRequest = t.pm_ChangeRequestId) Spent,
				   ".join(',',$issue_columns).",
				   t.UID,
				   t.Caption CaptionNative
			  FROM pm_ChangeRequest t
			 WHERE 1 = 1 ".$registry->getInnerFilterPredicate($request,$this->getSpecificFilters($filters))."
			   AND t.VPD IN (SELECT p.VPD FROM pm_Project p WHERE p.IsClosed = 'N')"
        );
    }

    function getSpecificFilters( $commonFilters )
    {
        $filters = array();
        foreach( $commonFilters as $filter ) {
            if ( $filter instanceof FilterAttributePredicate and $filter->getAttribute() == 'Assignee') {
                $filters[] = new FilterAttributePredicate('Owner', $filter->getValue());
            }
            if ( $filter instanceof FilterInPredicate ) {
                $filters[] = $filter;
            }
        }
        return $filters;
    }
}