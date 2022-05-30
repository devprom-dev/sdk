<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskFactPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskColorsPersister.php";

class WorkItemRegistryTaskBuilder extends WorkItemRegistryBuilder
{
    function build( WorkItemRegistry $registry, array $filters )
    {
        $task = getFactory()->getObject('Task');

        $task_columns = array(" '' Dummy ");
        foreach( array (
                     new TaskDatesPersister(),
                     new TaskTagsPersister(),
                     new TaskFactPersister(),
                     new TaskColorsPersister()
                 ) as $persister )
        {
            $persister->setObject($task);
            $task_columns = array_merge($task_columns, $persister->getSelectColumns('t'));
        }

        $registry->mergeSQL(
            " SELECT t.pm_TaskId,
				   'Task' ObjectClass,
				   t.Priority,
                   IFNULL(t.Caption, (SELECT r.Caption FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest)) Caption,
				   ".($registry->getDescriptionIncluded() ? "(SELECT r.Description FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) Description ": "'' Description").",
				   t.State,
				   t.StateObject,
				   (SELECT p.Caption FROM pm_TaskType p WHERE p.pm_TaskTypeId = t.TaskType) TaskType,
				   t.RecordCreated,
				   t.RecordModified,
				   t.RecordVersion,
				   t.StartDate,
				   t.FinishDate,
				   t.EstimatedStartDate,
				   t.EstimatedFinishDate,
				   t.Assignee,
				   t.ChangeRequest,
				   t.OrderNum,
				   t.LeftWork,
				   t.Planned,
				   t.Release,
				   t.Author,
				   IFNULL((SELECT r.Version FROM pm_Release r WHERE r.pm_ReleaseId = t.Release), 
				            (SELECT r.PlannedRelease FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest)) PlannedRelease,
				   t.VPD,
				   '' Type,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a WHERE a.Task = t.pm_TaskId) Spent,
				   ".join(',',$task_columns).",
				   CONCAT('T-', t.pm_TaskId) UID,
				   t.Caption CaptionNative
			  FROM pm_Task t
			 WHERE 1 = 1 ".$registry->getInnerFilterPredicate($task,$this->getSpecificFilters($filters))."
			   AND t.VPD IN (SELECT m.VPD FROM pm_Methodology m, pm_Project p 
			                  WHERE m.IsTasks = 'Y' AND m.Project = p.pm_ProjectId AND p.IsClosed = 'N')"
        );
    }

    function getSpecificFilters( $commonFilters )
    {
        $filters = array();
        foreach( $commonFilters as $filter ) {
            if ( $filter instanceof FilterAttributePredicate and $filter->getAttribute() == 'Assignee') {
                $filters[] = $filter;
            }
            if ( $filter instanceof FilterInPredicate ) {
                $filters[] = $filter;
            }
        }
        return $filters;
    }
}