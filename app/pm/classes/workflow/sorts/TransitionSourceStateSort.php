<?php

class TransitionSourceStateSort extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->getAlias().".SourceState ASC ";
 	}
}
