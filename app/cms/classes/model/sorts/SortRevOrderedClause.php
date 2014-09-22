<?php

class SortRevOrderedClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('OrderNum')." DESC ";
 	}
}
