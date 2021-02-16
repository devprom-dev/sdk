<?php
include_once "FilterAttributePredicate.php";

class FilterHasNoAttributePredicate extends FilterAttributePredicate
{
    function getQueryPredicate() {
        return " IFNULL(t.".$this->getAttribute().", '') NOT IN (".join($this->getIds(),',').") ";
 	}
}