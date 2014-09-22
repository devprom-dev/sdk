<?php

class SortRecentClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('RecordCreated')." DESC ";
 	}
}
