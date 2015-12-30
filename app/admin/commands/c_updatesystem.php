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
	    // executes installation scripts
	    $result = array();
	    if ( !InstallationFactory::getFactory()->install( $result ) ) {
	        $this->replyError(str_replace('%1', join(', ', $result), text(1053)));
	    }

	    // rerun checks on checkpoints
		getCheckpointFactory()->getCheckpoint('CheckpointSystem')->executeDynamicOnly();

		// rebuild cached list of plugins
		PluginsFactory::Instance()->buildPluginsList();

		$this->replyRedirect( '?' );
	}
}
