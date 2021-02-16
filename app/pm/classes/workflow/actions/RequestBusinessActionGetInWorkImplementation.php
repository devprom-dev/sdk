<?php
include_once "RequestBusinessActionChangeStateBase.php";

class RequestBusinessActionGetInWorkImplementation extends RequestBusinessActionChangeStateBase
{
 	function getId() {
 		return '1392172416';
 	}

    function getFilters($object_it) {
        return array (
            new RequestImplementationFilter($object_it->getId())
        );
    }

    function getStateFilters($object_it) {
        return array(
            new FilterAttributePredicate('IsTerminal', 'I'),
        );
    }

 	function getDisplayName() {
 		return text(1388);
 	}
}