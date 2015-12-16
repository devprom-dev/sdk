<?php
include "ReleaseRecentRegistry.php";

class ReleaseRecent extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Version', new ReleaseRecentRegistry($this) );
	}
}