<?php
include_once "RequestBusinessActionChangeStateBase.php";

class RequestBusinessActionResolveDuplicates extends RequestBusinessActionChangeStateBase
{
 	function getId() {
 		return '19adedb1-7426-44ea-8139-4a2d680dfeac';
 	}

    function getFilters($object_it) {
        return array (
            new RequestDuplicatesOfFilter($object_it->getId())
        );
    }

    function getStateFilters($object_it) {
        return array(
            new FilterAttributePredicate('IsTerminal', 'Y'),
        );
    }

 	function getDisplayName() {
 		return text(1878);
 	}
}