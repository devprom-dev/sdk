<?php

class ProjectCurrentPredicate extends FilterPredicate
{
 	function ProjectCurrentPredicate()
 	{
 		parent::FilterPredicate('current');
 	}
 	
 	function _predicate( $filter )
 	{
 		global $project_it;
 		return " AND pm_ProjectId = ".$project_it->getId();
 	}
}
