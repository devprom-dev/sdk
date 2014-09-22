<?php

class SortRecentNumberClause extends SortClauseBase
{
 	function clause()
 	{
 		return " CAST(ReleaseNumber AS UNSIGNED) DESC ";
 	}
}
