<?php

class StateOrderedSortClause extends SortClauseBase
{
 	function clause() {
 		return " CASE t.IsTerminal WHEN 'N' THEN 1 WHEN 'Y' THEN 3 ELSE 2 END ";
 	}
}
