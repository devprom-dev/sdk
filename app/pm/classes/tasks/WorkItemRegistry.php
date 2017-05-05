<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectUIDPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestDueDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskFactPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include "persisters/WorkItemStatePersister.php";
include "persisters/WorkItemCommentPersister.php";

class WorkItemRegistry extends ObjectRegistrySQL
{
    function getPersisters() {
        return array(
            new EntityProjectPersister(),
            new WorkItemStatePersister(),
            new WorkItemCommentPersister(),
            new AttachmentsPersister()
        );
    }

    function getTaskPersisters() {
        return array (
            new ObjectUIDPersister(),
            new TaskDatesPersister(),
            new StateDurationPersister(),
            new TaskTracePersister(),
            new TaskTagsPersister(),
            new TaskFactPersister()
        );
    }

    function getIssuePersisters() {
        return array (
            new ObjectUIDPersister(),
            new RequestDueDatesPersister(),
            new StateDurationPersister(),
            new RequestTagsPersister(),
            new RequestTracePersister()
        );
    }

 	function getQueryClause()
 	{
 	    $request = getFactory()->getObject('Request');
		$task = getFactory()->getObject('Task');
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        $task_columns = array(" '' Dummy ");
        foreach( $this->getTaskPersisters() as $persister ) {
            $persister->setObject($task);
            $task_columns = array_merge($task_columns, $persister->getSelectColumns('t'));
        }
        $issue_columns = array(" '' Dummy ");
        foreach( $this->getIssuePersisters() as $persister ) {
            $persister->setObject($request);
            $issue_columns = array_merge($issue_columns, $persister->getSelectColumns('t'));
        }
        $issue_columns[] = 't.Fact';

		$sql = "
			SELECT t.pm_TaskId,
				   'Task' ObjectClass,
				   t.Priority,
				   t.Caption,
				   (SELECT r.Description FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) Description,
				   t.State,
				   (SELECT s.pm_StateId FROM pm_State s WHERE s.VPD = t.VPD AND s.ObjectClass = 'task' AND s.ReferenceName = t.State) StateMeta,
				   t.StateObject,
				   t.TaskType,
				   (SELECT p.Caption FROM pm_TaskType p WHERE p.pm_TaskTypeId = t.TaskType) TypeName,
				   t.RecordCreated,
				   t.RecordModified,
				   t.StartDate,
				   t.FinishDate,
				   t.Assignee,
				   t.ChangeRequest,
				   t.OrderNum,
				   t.LeftWork,
				   t.Planned,
				   t.Release,
				   t.Author,
				   (SELECT r.Version FROM pm_Release r WHERE r.pm_ReleaseId = t.Release) PlannedRelease,
				   t.VPD,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a WHERE a.Task = t.pm_TaskId) Spent,
				   (SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(':',l.ObjectClass,CAST(l.ObjectId AS CHAR),l.Baseline))
                      FROM pm_ChangeRequestTrace l
                     WHERE l.ChangeRequest = t.ChangeRequest
                       AND l.ObjectClass NOT IN ('Task')) IssueTraces,
				   ".join(',',$task_columns)."
			  FROM pm_Task t
			 WHERE 1 = 1 ".$task->getVpdPredicate('t').$this->getInnerFilterPredicate($task,$this->getTaskFilters()).($methodology_it->HasTasks() ? '' : ' AND 1 = 2 ')."
			 UNION
			SELECT t.pm_ChangeRequestId,
				   'Request' as ObjectClass,
				   t.Priority,
				   t.Caption,
				   t.Description,
				   t.State,
				   (SELECT s.pm_StateId FROM pm_State s WHERE s.VPD = t.VPD AND s.ObjectClass = 'request' AND s.ReferenceName = t.State) StateMeta,
				   t.StateObject,
				   1000000 + IFNULL(t.Type, 0),
				   (SELECT p.Caption FROM pm_IssueType p WHERE p.pm_IssueTypeId = t.Type) TypeName,
				   t.RecordCreated,
				   t.RecordModified,
				   t.StartDate,
				   t.FinishDate,
				   t.Owner,
				   NULL,
				   t.OrderNum,
				   t.EstimationLeft,
				   t.Estimation,
				   (SELECT MIN(r.pm_ReleaseId) FROM pm_Release r WHERE r.Version = t.PlannedRelease),
				   t.Author,
				   t.PlannedRelease,
				   t.VPD,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a, pm_Task s WHERE a.Task = s.pm_TaskId AND s.ChangeRequest = t.pm_ChangeRequestId) Spent,
				   (SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(':',l.ObjectClass,CAST(l.ObjectId AS CHAR),l.Baseline))
                      FROM pm_ChangeRequestTrace l
                     WHERE l.ChangeRequest = t.pm_ChangeRequestId
                       AND l.ObjectClass NOT IN ('Task', 'Milestone')) IssueTraces,
				   ".join(',',$issue_columns)."
			  FROM pm_ChangeRequest t
			 WHERE 1 = 1 ".$request->getVpdPredicate('t').$this->getInnerFilterPredicate($request,$this->getIssueFilters())."
		";

 	    return "(".$sql.")";
 	}

    protected function getInnerFilterPredicate( $object, $filters )
    {
        $predicate = '';
        foreach( $filters as $filter ) {
            $filter->setObject($object);
            $filter->setAlias('t');
            $predicate .= $filter->getPredicate();
        }
        return $predicate;
    }

    protected function getTaskFilters()
    {
        $filters = array();
        foreach( $this->getFilters() as $filter ) {
            if ( $filter instanceof FilterAttributePredicate and $filter->getAttribute() == 'Assignee') {
                $filters[] = $filter;
            }
            if ( $filter instanceof FilterInPredicate ) {
                $filters[] = $filter;
            }
        }
        return $filters;
    }

    protected function getIssueFilters()
    {
        $filters = array();
        foreach( $this->getFilters() as $filter ) {
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