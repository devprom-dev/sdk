<?php
include "BaselineRegistry.php";

class Baseline extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_Version', new BaselineRegistry($this));
	}
	
	function IsDeletedCascade( $object ) {
		return false;
	}
 	
	function IsUpdatedCascade( $object ) {
		return false;
	}
}