<?php

class FilterNoVpdPredicate extends FilterPredicate
{
 	function FilterNoVpdPredicate()
 	{
 		parent::FilterPredicate('base');
 	}
 	
 	function getPredicate()
 	{
 		return " AND t.VPD IS NULL ";
 	}
}
