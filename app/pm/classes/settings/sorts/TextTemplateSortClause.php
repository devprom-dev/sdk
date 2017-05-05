<?php

class TextTemplateSortClause extends SortAttributeClause
{
 	function __construct() {
 		parent::__construct('Caption');
 	}
 	
 	function clause()
 	{
		return " ".$this->getAlias().".ObjectClass ASC, ".$this->getAlias().".Caption ASC ";
 	}
}