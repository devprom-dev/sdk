<?php

class ObjectRootFilter extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('-');
 	}
 	
 	function _predicate( $filter )
 	{
        $parentColumn = array_shift($this->getObject()->getAttributesByGroup('hierarchy-parent'));
 		return " AND t.{$parentColumn} IS NULL ";
 	}
}
