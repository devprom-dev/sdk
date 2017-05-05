<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include ('PluginSectionBase.php');
include ('PluginAPISectionBase.php');
include ('PluginCoSectionBase.php');
include ('PluginPMSectionBase.php');
include ('PluginAdminSectionBase.php');

class PluginBase
{
	protected $license = '';

    function __construct() {
    }
    
 	function getNamespace()
 	{
 	}
 	
 	function getCaption()
 	{
 	}
 	
 	function getDescription()
 	{
 	}
 	
 	function getIndex()
 	{
 		return 100;
 	}
 	
 	function getFileName()
 	{
 	}
 	
 	function getSectionPlugins()
 	{
 	}
 	
 	function getAuthorizationFactories()
 	{
		return array();	
 	}
 	
 	function getClass( $class )
 	{
 	}

 	function getClasses()
 	{
 		return array();
 	}
 	
 	function getBuilders()
 	{
 	    return array();
 	}
 	
	function getObjectUrl( $object_it )
	{
	}

 	function IsUpdatedWithCore()
 	{
 		return true;
 	}

	function checkLicense() {
        if ( $this->license == '' ) $this->setLicense();
		return $this->license == 'Y';
	}

	function setLicense() {
        $license_it = getFactory()->getObject('LicenseState')->getAll();
        $this->license = $this->buildLicense($license_it) ? 'Y' : 'N';
    }

	function buildLicense( $license_it ) {
		return true;
	}

	function checkEnabled() {
		return true;
	}

	function getHeaderMenus() {
		return array();
	}

	public function __sleep() {
		return array('license');
	}

	public function __wakeup() {
	}
}