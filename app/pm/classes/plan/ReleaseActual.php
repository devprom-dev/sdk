<?php
include "ReleaseActualRegistry.php";

class ReleaseActual extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Version', new ReleaseActualRegistry($this) );
	}
}