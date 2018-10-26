<?php

class accountAdmin extends PluginAdminBase
{
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'servicepayed' =>
                array(
                        'includes' => array( 'account/views/ServicePayedPage.php' ),
                        'classname' => 'ServicePayedPage'
                )
        );
    }

 	function getHeaderTabs()
 	{
		$tabs = array(
				'payment' =>  array ( 
					'name' => translate('�������'),
					'items' => array (
					    array(),
						array( 'module' => 'servicepayed', 'name' => '������' )
					)
				)
			);

		return $tabs;
 	}
}
