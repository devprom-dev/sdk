<?php

class ProjectScrumPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('-');
	}
	
 	function _predicate( $filter ) {
 		return " AND (SELECT 1 FROM pm_Methodology m WHERE m.Project = t.pm_ProjectId AND m.UseScrums = 'Y')";  
 	}
}
