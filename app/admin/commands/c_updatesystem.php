<?php

include_once "MaintenanceCommand.php";
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';

class UpdateSystem extends MaintenanceCommand
{
	function validate()
	{
		return true;
	}

	function create()
	{
		global $plugins;
		
	    // executes installation scripts
	    $result = array();
	    
	    $installation_factory = InstallationFactory::getFactory();
	    
	    if ( !$installation_factory->install( $result ) )
	    {
	        $this->replyError(str_replace('%1', join(', ', $result), text(1053)));
	    }

	    // rerun checks on checkpoints
		$checkpoint_factory = getCheckpointFactory();
		
		$checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );

		$checkpoint->executeDynamicOnly();
		
		$plugins->buildPluginsList();
	    
		$this->replyRedirect( '?' );
	}
}
