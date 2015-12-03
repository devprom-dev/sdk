<?php

class accountClientPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new AccountSiteJSBuilder(getSession()),
				new AccountSiteCssBuilder(getSession())
		);
	}
    
  	function getHeaderMenus()
 	{
 		return $this->getBasePlugin()->getHeaderMenus();
 	}
}