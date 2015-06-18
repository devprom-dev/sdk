<?php

class SortReverseKeyClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->getObject()->getClassName()."Id DESC ";
 	}
}
