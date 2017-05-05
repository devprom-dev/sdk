<?php
include_once "IterationDatesIterator.php";
include "ReleaseRecentRegistry.php";

class ReleaseRecent extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Version', new ReleaseRecentRegistry($this) );
	}

    function createIterator() {
        return new IterationDatesIterator($this);
    }
}