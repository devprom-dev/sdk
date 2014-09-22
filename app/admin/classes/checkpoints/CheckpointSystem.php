<?php

include "CheckpointRegistryBuilderSystem.php";

class CheckpointSystem extends CheckpointBase
{
	function __construct()
	{
		getSession()->addBuilder( new CheckpointRegistryBuilderSystem() );
		
		parent::__construct();
	}
}
