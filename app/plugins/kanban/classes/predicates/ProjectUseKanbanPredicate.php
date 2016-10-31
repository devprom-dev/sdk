<?php

class ProjectUseKanbanPredicate extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('-');
 	}

 	function _predicate( $filter ) {
 		return " AND EXISTS (SELECT 1 FROM pm_Methodology m ".
 		       "              WHERE m.Project = t.pm_ProjectId ".
 		       "                AND m.IsKanbanUsed = 'Y') ";
 	}
}

