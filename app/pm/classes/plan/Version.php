<?php

include "VersionIterator.php";
include "VersionRegistry.php";

class Version extends Metaobject
{
 	function __construct() 
 	{
 		parent::Metaobject('pm_Version', new VersionRegistry($this));
 		
 	    $this->setSortDefault( array (
 	            new SortAttributeClause('VersionNumber')
 	    ));
 	}
 	
 	function createIterator()
 	{
 		return new VersionIterator( $this );
 	}

	function IsDeletedCascade( $object )
	{
		return false;
	}
 	
	function IsUpdatedCascade( $object )
	{
		return false;
	}
		
 	function getDisplayName()
 	{
 	    return translate('Версия');
 	}
}
