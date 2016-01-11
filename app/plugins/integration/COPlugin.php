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
		}
	}
}