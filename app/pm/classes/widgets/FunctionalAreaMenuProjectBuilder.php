<?php

include_once "FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuProjectBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = array();
    	
    	$item = getFactory()->getObject('PMReport')->getExact('navigation-settings')->buildMenuItem();
    	
   		$menus['quick'] = array( 
	   		   'name' => '', 
	   		   'items' => 
   					array(
	   		   			'navigation-settings' => $item
   					),
	   		   'uid' => 'quick' 
   		);
    	
		return $menus;
    }
}