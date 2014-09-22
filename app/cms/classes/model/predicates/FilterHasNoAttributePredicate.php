<?php

include_once "FilterAttributePredicate.php";

class FilterHasNoAttributePredicate extends FilterAttributePredicate
{
 	function getQueryPredicate()
 	{
 	 	if ( $this->hasNullValue() )
 		{
 			return " AND (t.".$this->getAttribute()." NOT IN (".join($this->getIds(),',').") AND t.".$this->getAttribute()." IS NOT NULL )";
 		}
 		else
 		{
 			return " AND IFNULL(t.".$this->getAttribute().", '') NOT IN (".join($this->getIds(),',').") ";
 		}
 	}
}