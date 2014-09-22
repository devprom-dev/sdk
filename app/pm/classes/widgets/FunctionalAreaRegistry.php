<?php

class FunctionalAreaRegistry extends ObjectRegistrySQL
{
    var $areas = array();

    var $order = 0;
    
    function addArea( $uid, $menus = array(), $icon = '', $order = 0 )
 	{
 	    $this->areas[$uid] = array ( 'icon' => $icon, 'order' => $order > 0 ? $order : $this->order );
 	    
 	    $this->order++;
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	 	$data = array();
 	 	
 	 	$category = new ModuleCategory();
 	 	
 	 	$category_it = $category->getAll();

 	    foreach( getSession()->getBuilders('FunctionalAreaBuilder') as $builder )
 	    {
 	        $builder->build( $this );
 	    }
 	 	
 		foreach( $this->areas as $key => $area )
 		{
 			$category_it->moveToId($key);
 			
 			$data[] = array (
 				'entityId' => $key,
 				'ReferenceName' => $key,
 				'Caption' => $category_it->getDisplayName(),
 			    'icon' => $area['icon'],
 			    'order' => $area['order']
 			);
 		}
 		
 		usort($data, function( $left, $right ) {
 		    return $left['order'] > $right['order'];
 		});
 		
        return $this->createIterator( $data );
 	}
}