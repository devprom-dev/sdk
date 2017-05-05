<?php

class integrationCO extends PluginCOBase
{
	public function getBuilders()
	{
		return array();
	}

 	function getCommand( $name )
	{
		switch ( $name )
		{
			case 'integrationtask':
				return array(
					'includes' => array( 'integration/commands/c_integrationtask.php' )
				);
            case 'fillproject':
                return array(
                    'includes' => array( 'integration/commands/c_fillproject.php' )
                );
		}
	}

    function getModules()
    {
        $modules = array(
            'fill' =>
                array(
                    'includes' => array( 'integration/views/FillProjectPage.php' ),
                    'classname' => 'FillProjectPage',
                    'title' => text('integration1'),
                    'AccessEntityReferenceName' => 'pm_Integration',
                    'AccessType' => 'read'
                )
        );
        return $modules;
    }
}