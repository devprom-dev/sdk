<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuSettingsBuilder extends FunctionalAreaMenuProjectBuilder
{
    const AREA_UID = 'stg';
    
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);
    	
 	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	     
 	    $part_it = getSession()->getParticipantIt();
 	    
		$module = getFactory()->getObject('Module');
		
		$items = array();

		$module_part_it = $module->getExact('participants');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_part_it) )
		{
    		$items[] = $module_part_it->buildMenuItem();
		}
		
		$module_it = $module->getExact('project-settings');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$items['common'] = $module_it->buildMenuItem();
		}

    	$module_it = $module->getExact('methodology');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
	    
		$module_it = $module->getExact('dicts-pmcustomattribute');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
		
    	$module_it = $module->getExact('rights');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}

        $module_it = $module->getExact('dicts-customresource');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
		
	    $menus['quick']['items'] = array_merge($items, $menus['quick']['items']);
 	    
 	    $items = array();
 	    
	    $object_it = getFactory()->getObject('Workflow')->getAll();
	    
	    while ( !$object_it->end() ) 
	    {
	    	$module_it = $module->getExact('workflow-'.strtolower($object_it->getId()));
	    	
    	 	$items[$object_it->getId()] = $module_it->buildMenuItem();
    	 	
    	 	$object_it->moveNext();
	    }

 	    $menus['workflow'] = array (
 	        'name' => translate('���������'),
            'uid' => 'workflow',
            'items' => $items
 	    );
 	    
 	    $items = array();
 	    
	    $object_it = getFactory()->getObject('Dictionary')->getAll();
	    
	    while ( !$object_it->end() ) 
	    {
	    	$module_uid = 'dicts-'.strtolower($object_it->getId());
	    	
	    	if ( in_array($module_uid, array('dicts-pmcustomattribute', 'dicts-customresource')) )
	    	{
	    		$object_it->moveNext();
	    		continue;
	    	}
	    	
	    	$module_it = $module->getExact($module_uid);
	    	
    	 	$items[$object_it->getId()] = $module_it->buildMenuItem();
    	 	
    	 	$object_it->moveNext();
	    }

 	    $menus['dicts'] = array (
 	        'name' => translate('�����������'),
            'uid' => 'dicts',
            'items' => $items
 	    );
 	    
 	    $items = array();

        $module_it = $module->getExact('kbtemplates');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
	    
        $module_it = $module->getExact('tags');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
		
        $module_it = $module->getExact('snapshots');

    	if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
	 	
        $module_it = $module->getExact('versions');

    	if ( $methodology_it->HasVersions() && getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$items[] = $module_it->buildMenuItem();
		}
		
 	    $menus['settings'] = array (
 	        'name' => translate('�������������'),
            'uid' => 'settings',
            'items' => $items
 	    );

 	    $set->setAreaMenus( FunctionalAreaMenuSettingsBuilder::AREA_UID, $menus );
    }
}