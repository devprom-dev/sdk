<?php

include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

class ProcessCheckpoints extends TaskCommand
{
 	function execute()
	{
		global $model_factory;
		
		$this->logStart();
		
		$checkpoint_factory = getCheckpointFactory();
		
		$checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );

		$checkpoint->executeDynamicOnly();

		$this->logFinish();
	}
}
