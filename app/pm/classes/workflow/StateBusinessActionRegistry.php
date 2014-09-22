<?php

include_once "StateBusinessBaseRegistry.php";

class StateBusinessActionRegistry extends StateBusinessBaseRegistry
{
	public function getBuilderInterfaceName()
	{
		return 'StateBusinessActionBuilder';
	}
}