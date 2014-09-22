<?php

class SortCaptionClause extends SortClauseBase
{
 	function clause()
 	{
 		return " ".$this->setColumnAlias('Caption')." ASC ";
 	}
}
