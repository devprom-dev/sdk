<?php

class WikiNotArchivedPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::FilterPredicate('N');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND IFNULL(t.IsArchived, 'N') = '".$filter."' ";
 	}
}
