<?php

class RequestOwnerIsNotTasksAssigneeFilter extends FilterPredicate
{
	function __construct() {
		parent::__construct('-');
	}

	function _predicate( $filter ) {
 		return " AND NOT EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = ".$this->getAlias().".pm_ChangeRequestId AND s.Assignee = ".$this->getAlias().".Owner)";
 	}
}
