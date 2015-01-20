<?php

class accountCo extends PluginCoBase
{
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'command' =>
                array(
                        'includes' => array( 'account/views/AccountCommandController.php' ),
                        'classname' => 'AccountCommandController'
                ),
            'form' =>
                array(
                        'includes' => array( 'account/views/AccountFormController.php' ),
                        'classname' => 'AccountFormController'
                )
        );
    }
}