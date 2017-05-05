<?php

class SortRecentModifiedClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('RecordModified')." DESC ";
 	}
}
