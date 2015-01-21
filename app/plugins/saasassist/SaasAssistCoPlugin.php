<?php

include_once "classes/ScriptIntercomBuilder.php";

class SaasAssistCoPlugin extends PluginCoBase
{
 	function getBuilders()
 	{
 	    return array (
 	    		new RenewSAASLicenseEventHandler(),
 	    		new ScriptIntercomBuilder(getSession())
 	    );
 	}
 	
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'initialize' =>
                array(
                        'includes' => array( 'saasassist/views/InitializeInstance.php' ),
                        'classname' => 'InitializeInstance'
                ),
            'create' =>
                array(
                        'includes' => array( 'saasassist/views/CreateInstance.php' ),
                        'classname' => 'CreateInstance'
                )
        );
    }

    function getCommand( $name )
    {
        switch ( $name )
        {
            case 'createinstance':
                return array(
                	'includes' => array( 'saasassist/commands/c_createinstance.php' )
                );
        }
    }
} 