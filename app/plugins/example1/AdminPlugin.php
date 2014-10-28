<?php

include "model/events/UserAddedEventHandler.php";

class example1Admin extends PluginAdminBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new UserAddedEventHandler()
		);
	}
}