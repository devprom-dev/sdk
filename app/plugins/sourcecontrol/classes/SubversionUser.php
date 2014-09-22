<?php

include "SubversionUserIterator.php";

class SubversionUser extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_SubversionUser');
 	}
 	
 	function createIterator() 
 	{
 		return new SubversionUserIterator( $this );
 	}
}
