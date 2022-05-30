<?php
include_once "IterationDatesIterator.php";
include "ReleaseActualRegistry.php";

class ReleaseActual extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Version', new ReleaseActualRegistry($this) );
	}

    function createIterator() {
        return new ReleaseIterator($this);
    }

    function getPage() {
        return getSession()->getApplicationUrl($this).'iterations?';
    }
}