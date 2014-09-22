<?php

class SortVPDClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('VPD')." DESC ";
 	}
}
