<?php

class WikiDocumentFilter extends FilterPredicate
{
 	function _predicate( $filter ) {
 	    if ( $filter->getId() == '' ) return " AND 1 = 1 ";
 		return " AND t.DocumentId = ".$filter->getId();
 	}
}
