<?php

class FieldAuthor extends FieldAutoCompleteObject
{
	function __construct()
	{
		parent::__construct(getFactory()->getObject('IssueAuthor'));
		$this->setAppendable();
		$this->setSearchEnabled(false);
	}
}