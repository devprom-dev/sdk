<?php

class accountClientCo extends PluginCoBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
				new AccountSiteJSBuilder(getSession()),
				new AccountSiteCssBuilder(getSession())
		);
	}
	
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'proxy' =>
                array(
                        'includes' => array( 'accountclient/views/AccountProxyController.php' ),
                        'classname' => 'AccountProxyController'
                )
        );
    }
    
  	function getHeaderMenus()
 	{
 		return $this->getBasePlugin()->getHeaderMenus();
 	}
}