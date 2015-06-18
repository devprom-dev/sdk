<?php

class ModuleRegistry extends ObjectRegistrySQL
{
 	var $data = array();
	
 	function createSQLIterator( $sql )
 	{
 	    foreach( getSession()->getBuilders('ModuleBuilder') as $builder ) {
 	        $builder->build( $this );
 	    }
 	    
 	    $vpd_value = array_shift($this->getObject()->getVpds());
 	    foreach ( $this->getData() as $key => $row ) $this->data[$key]['VPD'] = $vpd_value;

 	    return $this->createIterator( $this->data );
 	}
 	
 	public function getData()
 	{
 		return $this->data;
 	}
 	
 	function addModule( $module_data )
 	{
 	    $parts = parse_url( $module_data['Url'] );
 	    
 	    if ( $parts['scheme'] == '' )
 	    {
 	        $module_data['Url'] = getSession()->getApplicationUrl().$module_data['Url'];
 	    }
 	    
 	    $this->data[] = $module_data;
 	}
}
