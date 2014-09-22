<?php

class ProjectExceptCurrentPredicate extends FilterPredicate
{
 	function ProjectExceptCurrentPredicate()
 	{
 		parent::FilterPredicate('except');
 	}
 	
 	function _predicate( $filter )
 	{
 		global $project_it;
 		return " AND pm_ProjectId <> ".$project_it->getId();
 	}
}
