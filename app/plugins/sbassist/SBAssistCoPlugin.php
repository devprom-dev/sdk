<?php

class SBAssistCoPlugin extends PluginCoBase
{
 	function getBuilders()
 	{
 	    return array (
            new RenewSAASLicenseEventHandler(),
            new ScriptCrispBuilder(getSession())
 	    );
 	}
 	
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'initialize' =>
                array(
                        'includes' => array( 'sbassist/views/InitializeInstance.php' ),
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
                	'includes' => array( 'sbassist/commands/c_createinstance.php' )
                );
        }
    }
} 