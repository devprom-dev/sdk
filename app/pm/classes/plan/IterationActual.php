<?php
include "IterationActualRegistry.php";

class IterationActual extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Release', new IterationActualRegistry($this) );
	}
}