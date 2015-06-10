<?php

class permissionsCo extends PluginCoBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new PortfolioMyProjectsBuilder()
		);
	}
}