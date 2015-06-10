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
 			foreach( $area['items'] as $group_uid => $group )
 			{
 				$index = 100;
 				array_walk($group['items'], function(&$value, $key) use(&$index) {
 					if ( $value['order'] == '' ) $value['order'] = $index++;
 				});
 				usort($group['items'], function( $left, $right ) {
 					if ( $left['order'] == $right['order'] ) return 0;
		 		    return $left['order'] > $right['order'] ? 1 : -1;
		 		});
 				$area['items'][$group_uid]['items'] = $group['items'];
 			}
 					 		
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