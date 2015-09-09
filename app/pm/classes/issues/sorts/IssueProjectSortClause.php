<?php

class IssueProjectSortClause extends SortClauseBase
{
 	function clause()
 	{
 		return " (SELECT REPLACE(p.CodeName, '".getSession()->getProjectIt()->get('CodeName')."', '') FROM pm_Project p WHERE p.pm_ProjectId = t.Project) ASC ";
 	}
}
