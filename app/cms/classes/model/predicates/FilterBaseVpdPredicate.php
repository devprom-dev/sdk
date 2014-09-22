<?php

class FilterBaseVpdPredicate extends FilterPredicate
{
 	function FilterBaseVpdPredicate()
 	{
 		parent::FilterPredicate('base');
 	}
 	
  	function getPredicate()
 	{
 		$object = $this->getObject();
 		
 		$vpd = $object->getVpdValue();
 		
 		if ( $vpd != '' )
 		{
 		    return " AND t.VPD = '".$vpd."'";
 		}
 		else
 		{
 		    return " ";
 		}
 	}
}