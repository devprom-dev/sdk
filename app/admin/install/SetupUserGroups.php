<?php

class SetupUserGroups extends Installable 
{
    function check()
    {
        return true;
    }

	function skip()
	{
		if ( !class_exists('PortfolioMyProjectsBuilder', false) ) return true;
		
    	$group = new Metaobject('co_UserGroup');
    	return $group->getRecordCount() > 0;
	}
    
    function install()
    {
    	// build management group
    	$group = new Metaobject('co_UserGroup');
    	
    	$group_id = $group->add_parms(
    			array (
    					'Caption' => translate('Руководители')
    			)
		);
        
    	// permit access to allprojects module
    	$rights = new Metaobject('co_AccessRight');
    	
    	$rights->add_parms(
    			array (
    					'UserGroup' => $group_id,
    					'AccessType' => 'view',
    					'ReferenceName' => 'ee/allprojects',
    					'ReferenceType' => 'cms_PluginModule'
    			)
		);
    	
        return true;
    }
}
