<?php

include_once "StateBusinessBaseRegistry.php";

class StateBusinessRuleRegistry extends StateBusinessBaseRegistry
{
	public function getBuilderInterfaceName()
	{
		return 'StateBusinessRuleBuilder';
	}
}