<?php

class SortReverseKeyClause extends SortClauseBase
{
 	function clause()
 	{
 		$object = $this->getObject();
 		return " ".$object->getClassName()."Id DESC ";
 	}
}
