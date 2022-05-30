<?php
include_once "IterationDatesIterator.php";
include "ReleaseRecentRegistry.php";

class ReleaseRecent extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Version', new ReleaseRecentRegistry($this) );
        $this->setSortDefault( array(
            new SortAttributeClause('StartDate.D'),
            new SortAttributeClause('Caption'))
        );
	}

    function createIterator() {
        return new ReleaseIterator($this);
    }
}