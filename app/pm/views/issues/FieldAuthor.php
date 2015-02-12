<?php

include SERVER_ROOT_PATH."pm/classes/issues/validators/ModelValidatorIssueAuthor.php";

class FieldAuthor extends FieldAutoCompleteObject
{
	function __construct()
	{
		parent::__construct(getFactory()->getObject('IssueAuthor'));
		$this->setAppendable();
	}
	
	function getValidator()
	{
		return new ModelValidatorIssueAuthor();
	}
}