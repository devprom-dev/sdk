<?php

class permissionsCo extends PluginCoBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		if ( !$this->getBasePlugin()->checkLicense() ) return array();
		return array(
				new PortfolioMyProjectsBuilder()
		);
	}
}