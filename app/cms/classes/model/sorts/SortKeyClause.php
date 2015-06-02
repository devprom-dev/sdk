<?php

class SortKeyClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->getObject()->getClassName()."Id ASC ";
 	}
}
