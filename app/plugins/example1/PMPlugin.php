<?php

include "model/events/TaskChangedEventHandler.php";
include "model/events/TaskAddedEventHandler.php";

class example1PM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new TaskChangedEventHandler(),
				new TaskAddedEventHandler()
		);
	}
}