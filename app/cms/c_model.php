<?php

 include('c_command.php');

 //////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class MetaModel
 {
	function MetaModel() {}
	
	function Install()
	{
		$this->InstallClass('settings');
		$this->InstallClass('package');
		$this->InstallClass('entity');
		$this->InstallClass('attribute');
		$this->InstallClass('businessfunction');
	}
	
	function InstallClass( $classname )
	{
		require_once('c_'.strtolower($classname).'.php');
		$object = new $classname;
		$object->Install();
	}

	function UnInstallClass( $classname )
	{
		require_once('c_'.strtolower($classname).'.php');
		$object = new $classname;
		$object->UnInstall();
	}
 }
 

?>