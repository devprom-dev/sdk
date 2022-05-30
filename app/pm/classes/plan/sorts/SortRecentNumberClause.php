<?php

class SortRecentNumberClause extends SortClauseBase
{
 	function clause()
 	{
 		return " CAST(Caption AS UNSIGNED) DESC ";
 	}
}
