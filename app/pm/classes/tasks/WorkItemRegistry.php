<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectUIDPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestDueDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestColorsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskDatesPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskTracePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskFactPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskColorsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include "persisters/WorkItemStatePersister.php";
include "persisters/WorkItemCommentPersister.php";

class WorkItemRegistry extends ObjectRegistrySQL
{
    private $descriptionIncluded = true;
    private $tracesIncluded = true;

    function getPersisters() {
        $result = array(
            new EntityProjectPersister(),
            new WorkItemStatePersister()
        );
        if ( $this->tracesIncluded ) {
            $result[] = new WorkItemCommentPersister();
            $result[] = new AttachmentsPersister();
        }
        return $result;
    }

    function getTaskPersisters() {
        $result = array (
            new TaskDatesPersister(),
            new TaskTagsPersister(),
            new TaskFactPersister(),
            new TaskColorsPersister()
        );
        if ( $this->tracesIncluded ) {
            $result[] = new TaskTracePersister();
        }
        return $result;
    }

    function getIssuePersisters() {
        $result = array (
            new RequestDueDatesPersister(),
            new RequestTagsPersister(),
            new RequestColorsPersister()
        );
        if ( $this->tracesIncluded ) {
            $result[] = new RequestTracePersister();
        }
        return $result;
    }

    public function setDescriptionIncluded( $flag = true ) {
        $this->descriptionIncluded = $flag;
    }

    public function setTracesIncluded( $flag = true ) {
        $this->tracesIncluded = $flag;
    }

 	function getQueryClause()
 	{
 	    $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();

 	    $request = getFactory()->getObject('Request');
		$task = getFactory()->getObject('Task');
		if ( !$this->getObject()->isVpdEnabled() ) {
            $task->disableVpd();
            $request->disableVpd();
        }

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
				   CONCAT(
				      IFNULL((SELECT CONCAT(p.Caption, ': ') FROM pm_TaskType p WHERE p.pm_TaskTypeId = t.TaskType), ''),
				      IFNULL(t.Caption, (SELECT r.Caption FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest))
				      ) Caption,
				   ".($this->descriptionIncluded ? "(SELECT r.Description FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) Description ": "'' Description").",
				   t.State,
				   t.StateObject,
				   t.TaskType,
				   (SELECT p.Caption FROM pm_TaskType p WHERE p.pm_TaskTypeId = t.TaskType) TypeName,
				   t.RecordCreated,
				   t.RecordModified,
				   t.StartDate,
				   t.FinishDate,
				   IFNULL(t.PlannedStartDate, (SELECT i.StartDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release)) PlannedStartDate,
				   t.Assignee,
				   t.ChangeRequest,
				   t.OrderNum,
				   t.LeftWork,
				   t.Planned,
				   t.Release,
				   t.Author,
				   IFNULL((SELECT r.Version FROM pm_Release r WHERE r.pm_ReleaseId = t.Release), (SELECT r.PlannedRelease FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest)) PlannedRelease,
				   t.VPD,
				   '' Type,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a WHERE a.Task = t.pm_TaskId) Spent,
				   ".($this->tracesIncluded ? ("
				   (SELECT CONCAT(CONCAT_WS(':','request',t.ChangeRequest,''), 
				                GROUP_CONCAT(DISTINCT CONCAT_WS(':',l.ObjectClass,CAST(l.ObjectId AS CHAR),l.Baseline)))
                      FROM pm_ChangeRequestTrace l
                     WHERE l.ChangeRequest = t.ChangeRequest
                       AND l.ObjectClass NOT IN ('Task')) IssueTraces") : "'' IssueTraces").",
				   ".join(',',$task_columns).",
				   CONCAT('T-', t.pm_TaskId) UID
			  FROM pm_Task t
			 WHERE 1 = 1 ".$task->getVpdPredicate('t').$this->getInnerFilterPredicate($task,$this->getTaskFilters())."
			   AND t.VPD IN (SELECT m.VPD FROM pm_Methodology m, pm_Project p 
			                  WHERE m.IsTasks = 'Y' AND m.Project = p.pm_ProjectId AND IFNULL(p.IsClosed,'N') = 'N')
			 UNION
			SELECT t.pm_ChangeRequestId, ".
                   ($methodologyIt->get('IsRequirements') == \ReqManagementModeRegistry::RDD
                        ? " IF(t.Type IS NULL, 'Issue', 'Increment') as ObjectClass "
                        : " 'Request' as ObjectClass ").
				   ",
				   t.Priority,
				   t.Caption,
				   ".($this->descriptionIncluded ? "t.Description ": "'' Description").",
				   t.State,
				   t.StateObject,
				   1000000 + IFNULL(t.Type, 0),
				   (SELECT p.Caption FROM pm_IssueType p WHERE p.pm_IssueTypeId = t.Type) TypeName,
				   t.RecordCreated,
				   t.RecordModified,
				   t.StartDate,
				   t.FinishDate,
				   IFNULL((SELECT i.StartDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Iteration), (SELECT i.StartDate FROM pm_Version i WHERE i.pm_VersionId = t.PlannedRelease)),
				   t.Owner,
				   t.pm_ChangeRequestId,
				   t.OrderNum,
				   t.EstimationLeft,
				   IF((SELECT m.RequestEstimationRequired FROM pm_Methodology m WHERE m.VPD = t.VPD LIMIT 1) = 'estimationhoursstrategy', t.Estimation, 0),
				   IFNULL(t.Iteration, (SELECT MIN(r.pm_ReleaseId) FROM pm_Release r WHERE r.Version = t.PlannedRelease)),
				   t.Author,
				   t.PlannedRelease,
				   t.VPD,
				   t.Type,
				   (SELECT GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) FROM pm_Activity a, pm_Task s WHERE a.Task = s.pm_TaskId AND s.ChangeRequest = t.pm_ChangeRequestId) Spent,
				   ".($this->tracesIncluded ? ("
				   (SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(':',l.ObjectClass,CAST(l.ObjectId AS CHAR),l.Baseline))
                      FROM pm_ChangeRequestTrace l
                     WHERE l.ChangeRequest = t.pm_ChangeRequestId
                       AND l.ObjectClass NOT IN ('Task', 'Milestone')) IssueTraces"):"'' IssueTraces").",
				   ".join(',',$issue_columns).",
				   t.UID
			  FROM pm_ChangeRequest t
			 WHERE 1 = 1 ".$request->getVpdPredicate('t').$this->getInnerFilterPredicate($request,$this->getIssueFilters())."
			   AND t.VPD IN (SELECT p.VPD FROM pm_Project p WHERE IFNULL(p.IsClosed,'N') = 'N')
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