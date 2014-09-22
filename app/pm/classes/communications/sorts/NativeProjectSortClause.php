<?php

class NativeProjectSortClause extends SortAttributeClause
{
    protected $native_vpd;
    
 	function __construct( $native_vpd )
 	{
 	    $this->native_vpd = $native_vpd;
 	    
 		parent::__construct('VPD');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.VPD' : 'VPD';
 	      
		return " (CASE ".$ref_field." WHEN '".$this->native_vpd."' THEN 0 ELSE 1 END) ".$this->getSortType()." ";
 	}
}