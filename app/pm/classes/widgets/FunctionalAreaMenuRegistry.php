<?php

class FunctionalAreaMenuRegistry extends ObjectRegistrySQL
{
    protected $areas = array();
	
	function getAreaMenus( $uid )
 	{
 	    if ( !array_key_exists($uid, $this->areas) ) return array();
 	     
 		return $this->areas[$uid]['items'];
 	}

  	function setAreaMenus( $uid, $menus )
 	{
 	    $this->areas[$uid]['items'] = $menus;
 	}
 	
 	function createSQLIterator( $sql )
 	{
        foreach( getSession()->getBuilders('FunctionalAreaMenuBuilder') as $builder )
        {
            $builder->build( $this ); 
        }
 		
 	 	$data = array();
 		
 		foreach( $this->areas as $key => $area )
 		{
 			$data[] = array (
 				'entityId' => $key,
 				'Workspace' => $key,
 				'items' => $area['items']
 			);
 		}
 		
 		DAL::Instance()->Reconnect();
 		
        return $this->createIterator( $data );
 	}
}