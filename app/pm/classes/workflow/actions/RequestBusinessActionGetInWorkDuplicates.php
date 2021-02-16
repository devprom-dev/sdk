<?php
include_once "RequestBusinessActionChangeStateBase.php";

class RequestBusinessActionGetInWorkDuplicates extends RequestBusinessActionChangeStateBase
{
 	function getId() {
 		return 'fa52bf30-6cb0-4bcf-92a4-d3ff4f365ee9';
 	}

 	function getFilters($object_it) {
        return array (
            new RequestDuplicatesOfFilter($object_it->getId())
        );
    }

    function getStateFilters( $object_it ) {
        return array(
            new FilterAttributePredicate('IsTerminal', 'I'),
        );
    }

 	function getDisplayName() {
 		return text(1879);
 	}
}