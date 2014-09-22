<?php

class SortParentPathClause extends SortClauseBase
{
 	function clause()
 	{
 		return " SUBSTRING_INDEX(t.ParentPath, ',', 2), ".
 		       " (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) ".
 		       "    FROM WikiPage u WHERE t.ParentPath LIKE CONCAT('%,',u.WikipageId,',%')) ";
 	}
}
