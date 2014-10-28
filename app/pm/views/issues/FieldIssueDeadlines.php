<?php

include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDeadline.php";
include_once "FieldIssueTrace.php";

class FieldIssueDeadlines extends FieldIssueTrace
{
 	function __construct( $object_it )
 	{
 		parent::__construct($object_it, getFactory()->getObject('RequestTraceMilestone'));
 	}
 	
 	function getValidator()
 	{
 		return new ModelValidatorDeadline();
 	}
}