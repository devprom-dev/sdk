<?php
include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDeadline.php";
include_once "FieldIssueTrace.php";

class FieldIssueDeadlines extends FieldIssueTrace
{
 	function __construct( $object_it ) {
        $traceObject = getFactory()->getObject('RequestTraceMilestone');
        $traceObject->setAttributeType('ObjectId', 'REF_MilestoneActualId');
 		parent::__construct($object_it, $traceObject);
 	}
 	
 	function getValidator() {
 		return new ModelValidatorDeadline();
 	}
}