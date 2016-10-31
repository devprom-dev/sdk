<?php

namespace Devprom\CommonBundle\Service\Widget;

class ScriptService
{
	public function getJSPaths()
	{
	    if ( getSession()->getApplicationUrl() == '/' ) return array();
		return array_merge(
				array_filter( 
						getFactory()->getObject('Script')->getRegistry()->Query()->fieldToArray('ReferenceName'),
						function($value) {
								return $value != '';
						}
				),
				array (
						getSession()->getApplicationUrl().'scripts/javascript/'
				)				
		);
	}
	
	public function getJSBody()
	{
		return join(PHP_EOL, getFactory()->getObject('Script')->getRegistry()->Query()->fieldToArray('Caption'));
	}

	public function getCSSBody()
	{
		return join(PHP_EOL, getFactory()->getObject('StyleSheet')->getRegistry()->Query()->fieldToArray('Caption'));
	}
}