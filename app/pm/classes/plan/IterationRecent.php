<?php
include_once "IterationDatesIterator.php";
include "IterationRecentRegistry.php";

class IterationRecent extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Release', new IterationRecentRegistry($this) );
	}

    function createIterator() {
        return new IterationIterator($this);
    }

    function IsDictionary() {
        return true;
    }
}