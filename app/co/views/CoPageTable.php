<?php

class CoPageTable extends PageTable
{
 	function __construct() {
 		parent::__construct( getFactory()->getObject('entity') );
 	}

    function getNewActions() {
        return array();
    }
}
