<?php
include_once "BusinessActionWorkflow.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";

class RequestBusinessActionMoveToProject extends BusinessActionWorkflow
{
 	function getId() {
 		return '58fdf144-e3b3-41a2-a5f3-5b026fe2b3b6';
 	}
	
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(866);
 	}

    function apply( $object_it )
    {
        if ( $this->getParameters() == '' ) return false;

        $projectIt = getFactory()->getObject('Project')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('CodeName', $this->getParameters())
            )
        );
        if ( $projectIt->getId() == '' ) return false;

        getFactory()->modifyEntity($object_it, array(
            'Project' => $projectIt->getId()
        ));

        return true;
    }
}