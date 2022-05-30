<?php
include_once "RequestBusinessActionChangeStateBase.php";

class RequestBusinessActionMoveImplemented extends RequestBusinessActionChangeStateBase
{
 	function getId() {
 		return '2d768afe-4d51-433c-9e83-c58c66f9e1d6';
 	}

    function getFilters($object_it) {
        return array (
            new RequestImplementationFilter($object_it->getId())
        );
    }

    function getStateFilters($object_it) {
        return array(
            new FilterAttributePredicate('ReferenceName',
                $this->getParameters() != '' ? $this->getParameters() : '-' ),
        );
    }

 	function getDisplayName() {
 		return text(3022);
 	}
}