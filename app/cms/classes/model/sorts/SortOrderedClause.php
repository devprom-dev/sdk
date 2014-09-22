<?php

class SortOrderedClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('OrderNum')." ASC ";
 	}
}
