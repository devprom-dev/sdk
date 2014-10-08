<?php

include_once "classes/licenses/events/RenewSAASLicenseEventHandler.php";
include_once "classes/ScriptIntercomBuilder.php";

class SaasAssistAdminPlugin extends PluginAdminBase
{
 	function getBuilders()
 	{
 	    return array (
 	            new RenewSAASLicenseEventHandler(),
 	    		new ScriptIntercomBuilder(getSession())
 	    );
 	}

    function getObjectAccess( $action, $group_it, &$object_it )
    {
        switch ( $object_it->object->getEntityRefName() )
        {
            case 'cms_PluginModule':
                
                if ( $object_it->getId() == 'update-upload' )
                {
                    return false;
                }
                
                break;
        }
    }
 	
 	function interceptMethodTableGetActions( & $table, & $actions )
 	{
 	    if ( is_a($table, 'UpdateTable') )
 	    {
 	    	if ( getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') == 'LicenseProcloud' ) return;
 	    	
 	    	$actions = array();
 	    }
 	}
 	
 	function interceptMethodFormGetActions( & $form, & $actions )
 	{
 	    if ( is_a($form, 'LicenseForm') )
 	    {
 	    	$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
 	    	
 	    	array_unshift($actions, 
 	    			array (
			 	    		'url' => 
			 	    			"javascript: window.location = 'https://devprom.ru/license?LicenseType=".$license_it->get('LicenseType').
			 	    			"&LicenseKey=".$license_it->get('LicenseKey').
			 	    			"&Value=".$license_it->get('LicenseValue').
			 	    			"&InstallationUID=".INSTALLATION_UID.
			 	    			"&Redirect=".urlencode(EnvironmentSettings::getServerUrl().$_SERVER['REQUEST_URI'])."';",
			 	    			
			 	    		'name' => text('saasassist36'),
			 	    		'class' => 'btn-success'
 	    			)
 	    	);
 	    }
 	}
 	 	
 	function interceptMethodListGetActions( & $table, & $actions )
 	{
 	 	if ( is_a($table, 'UpdateList') )
 	    {
 	    	if ( getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') == 'LicenseProcloud' ) return;
 	    	
 	    	foreach( $actions as $key => $action )
 	    	{
 	    		if ( $action['uid'] == 'download' )
 	    		{
 	    			unset($actions[$key-1]);
 	    			unset($actions[$key]);
 	    		}
 	    	}
 	    }

 	 	if ( is_a($table, 'BackupList') )
 	    {
 	    	if ( getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') == 'LicenseProcloud' ) return;
 	    	
 	    	foreach( $actions as $key => $action )
 	    	{
 	    		if ( $action['uid'] == 'download' ) unset($actions[$key]);
 	    	}
 	    }
 	}
} 