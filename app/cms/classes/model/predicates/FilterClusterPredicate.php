<?php

class FilterClusterPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND h.RecordModified >= '".getSession()->getLanguage()->getDbDate( $filter )."' ";
 	}
}
