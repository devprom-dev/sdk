<?php

class accountCo extends PluginCoBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array();
	}
	
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