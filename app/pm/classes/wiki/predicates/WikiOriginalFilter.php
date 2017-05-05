<?php

class WikiOriginalFilter extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('dummy');
 	}
 	
 	function _predicate( $filter ) {
 		return " AND ".$this->getAlias().".Includes IS NULL ";
 	}
}
 