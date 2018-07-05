<?php

class StateObjectSortClause extends SortClauseBase
{
 	function clause()
 	{
 		return " (SELECT so.RecordModified FROM pm_StateObject so WHERE so.pm_StateObjectId = ".$this->getAlias().".StateObject) DESC ";
 	}
}
