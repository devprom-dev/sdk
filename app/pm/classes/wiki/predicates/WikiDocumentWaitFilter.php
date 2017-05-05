<?php

class WikiDocumentWaitFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND IFNULL(t.DocumentId,".$filter.") = ".$filter;
 	}
}
