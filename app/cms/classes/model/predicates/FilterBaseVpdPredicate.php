<?php

class FilterBaseVpdPredicate extends FilterPredicate
{
 	function FilterBaseVpdPredicate() {
 		parent::FilterPredicate('base');
 	}
 	
  	function getPredicate( $filter = '' )
 	{
 		$vpd = $this->getObject()->getVpdValue();

 		if ( $vpd != '' ) {
 		    return " AND ".$this->getAlias().".VPD = '".$vpd."'";
 		}
 		else {
 		    return " ";
 		}
 	}
}