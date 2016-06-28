<?php

class FieldIssueTypeDictionary extends FieldDictionary
{
	private $type = null;
	private $issue = null;

	function __construct( $issue )
	{
		$this->issue = $issue;
		$this->type = getFactory()->getObject('pm_IssueType');
		$this->type->addFilter( new FilterBaseVpdPredicate() );

		parent::__construct($this->type);
		$this->setNullOption(false);
	}

	function getOptions()
	{
		if ( $this->issue->IsAttributeRequired('Type') ) {
			return parent::getOptions();
		}
		else {
			return array_merge(
				array (
					array(
						'value' => '',
						'caption' => getFactory()->getObject('Request')->getDisplayName()
					)
				),
				parent::getOptions()
			);
		}
	}
}