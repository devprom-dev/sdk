<?php
include_once "IterationDatesIterator.php";
include "IterationActualRegistry.php";

class IterationActual extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Release', new IterationActualRegistry($this) );
	}

	function createIterator() {
        return new IterationIterator($this);
    }

    function getPage() {
        return getSession()->getApplicationUrl($this).'iterations?';
    }
}