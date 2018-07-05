<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class PluginSectionBase
{
 	var $namespace;
 	
 	function __construct()
 	{
 	}

	public function __sleep()
	{
		return array('namespace');
	}

	public function __wakeup()
	{
	}

 	function setNamespace( $namespace )
 	{
 		$this->namespace = $namespace;
 	}
 	
 	function checkEnabled()
 	{
 	    return true;
 	}
 	
 	function getBasePlugin()
 	{
		if ( is_object($this->namespace) ) return $this->namespace;
 		return new PluginBase();
 	}
 	
 	function getNamespace()
 	{
 		return $this->getBasePlugin()->getNamespace();
 	}

 	function getModules()
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
	
	function getObjectActions( $object_it )
	{
		return array();
	}
	
 	function getHeaderTabs()
 	{
 		return array();
 	}
 	
 	function getPageInfoSections( $page )
 	{
 		return array();
 	}

 	function getQuickActions()
 	{
 		return array();
 	}

 	function getProfileActions()
 	{
 		return array();
 	}

 	function buildMenuItems( $owner, & $items, $parms )
 	{
 	}

 	function getHeaderMenus()
 	{
 		return array();
 	}
 	
  	function getNotificators()
 	{
 		return array();
 	}
 	
 	function interceptMethodTableGetFilters( & $table, & $filters )
 	{
 	}

 	function interceptMethodTableGetActions( & $table, & $actions )
 	{
 	}

    function interceptMethodFormExtendModel( & $form )
    {
    }

 	function interceptMethodFormCreateFieldObject( & $form, $attr )
 	{
 	}
 	
 	function interceptMethodFormGetActions( & $form, & $actions )
 	{
 	}

 	function interceptMethodFormDrawScripts( & $form )
 	{
 	}
 	
 	function interceptMethodListGetActions( & $table, & $actions )
 	{
 	}
 	
 	function interceptMethodListGetPredicates( & $list, & $predicates, $values )
 	{
 	}

 	function interceptMethodListDrawRefCell( & $list, & $entity_it, & $object_it, $attr )
 	{
 		return false;
 	}

 	function interceptMethodListDrawCell( & $list, & $object_it, $attr )
 	{
 		return false;
 	}
 	
 	function interceptMethodListSetupColumns( & $list )
 	{
 	}
}