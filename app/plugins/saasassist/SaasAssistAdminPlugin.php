<?php

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
                
                if ( $object_it->getId() == 'update-upload' && getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') != 'LicenseProcloud' )
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
 	    if ( $form instanceof InstallLicenseTypeForm || $form instanceof LicenseForm )
 	    {
 	    	$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
 	    	
 	    	if ( class_exists('AccountSiteJSBuilder') )
 	    	{
 	    		$script = AccountSiteJSBuilder::getScriptToBuy();
 	    	}
 	    	else
 	    	{
 	    		$script = "window.location = 'https://devprom.ru/license?LicenseType=".$license_it->get('LicenseType').
			 	    			"&LicenseKey=".$license_it->get('LicenseKey').
			 	    			"&Value=".$license_it->get('LicenseValue').
			 	    			"&InstallationUID=".INSTALLATION_UID.
			 	    			"&Redirect=".urlencode(EnvironmentSettings::getServerUrl().$_SERVER['REQUEST_URI'])."'";
 	    	} 	    	
 	    	
 	    	$actions = array ( 
 	    			array (
			 	    		'url' => getSession()->getUserIt()->getId() > 0 ? "javascript: ".$script.";" : "javascript: window.location = '/login';",
			 	    		'name' => text('saasassist36'),
			 	    		'class' => 'btn-success'
 	    			),
 	    			array (
 	    					'url' => "javascript: $('#action".$form->getId()."').val(3);",
 	    					'name' => translate('Ввести ключ'),
 	    					'type' => 'submit'
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