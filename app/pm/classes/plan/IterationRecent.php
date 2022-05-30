<?php
include_once "IterationDatesIterator.php";
include "IterationRecentRegistry.php";

class IterationRecent extends Metaobject
{
 	function __construct() {
		parent::__construct( 'pm_Release', new IterationRecentRegistry($this) );
        $this->setSortDefault( array(
            new SortAttributeClause('StartDate.D'),
            new SortReleaseIterationClause(),
            new SortAttributeClause('Caption')
        ));
	}

    function createIterator() {
        return new IterationIterator($this);
    }
}