<?php

class ProjectCurrentPredicate extends FilterPredicate
{
 	function ProjectCurrentPredicate() {
 		parent::__construct('current');
 	}
 	
 	function _predicate( $filter ) {
 		return " AND pm_ProjectId = ".getSession()->getProjectIt()->getId();
 	}
}
