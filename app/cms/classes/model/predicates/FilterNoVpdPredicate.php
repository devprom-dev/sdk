<?php

class FilterNoVpdPredicate extends FilterPredicate
{
 	function FilterNoVpdPredicate()
 	{
 		parent::FilterPredicate('base');
 	}
 	
 	function getPredicate( $filter = '' )
 	{
 		return " AND t.VPD IS NULL ";
 	}
}
