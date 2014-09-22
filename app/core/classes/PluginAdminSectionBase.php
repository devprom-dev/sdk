<?php

class PluginAdminBase extends PluginSectionBase
{
 	function getEntityAccess( $action, $user_it, & $object )
 	{
 	}

 	function getObjectAccess( $action, $user_it, & $object_it )
 	{
 	}
 	
 	function getModules()
 	{
		$modules = array (
 			'license' => 
 				array(
 					'includes' => array( 'ee/views/c_license_view.php' ),
 					'classname' => 'LicensePage' 
 					)
 			);
 		
 	}
}