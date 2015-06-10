<?php

class permissionsAdmin extends PluginAdminBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new PortfolioMyProjectsBuilder()
		);
	}
}