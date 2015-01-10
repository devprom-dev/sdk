<?php

class TaskDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " IF(t.FinishDate IS NULL, 365, GREATEST(0, TO_DAYS(t.FinishDate) - TO_DAYS(NOW()))) DueDays ";
 		$columns[] = " IF(t.FinishDate IS NULL, 5, LEAST(5, GREATEST(-1, YEARWEEK(t.FinishDate) - YEARWEEK(NOW())))) + 2 DueWeeks ";
 		
 		return $columns;
 	}
}

