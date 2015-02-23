<?php

class SortChangeLogRecentClause extends SortClauseBase
{
 	function clause()
 	{
 		return " MAX(".$this->setColumnAlias('RecordCreated').") DESC ";
 	}
}
