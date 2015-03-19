<?php

include_once "classes/ScriptIntercomBuilder.php";

class DOBAssistCoPlugin extends PluginCoBase
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