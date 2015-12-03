<?php

class RequestFormDuplicate extends RequestForm
{
	private $source_it = null;

	public function __construct( $object )
	{
		parent::__construct($object);

		$this->source_it = $object->getRegistry()->Query(
			array (
				new FilterInPredicate($_REQUEST['Request'])
			)
		);
	}
	
	public function extendModel()
	{
		parent::extendModel();
		
		$object = $this->getObject();
		$object->addAttribute('LinkType', 'REF_RequestLinkTypeId', translate('Тип связи'), true, false, '', 1);
		$object->setAttributeVisible('Author', false);

		if ( $_REQUEST['Project'] == '' ) {
			$object->setAttributeVisible('Project', true);
			$object->setAttributeType('Project', 'REF_ProjectAccessibleId');
			$object->setAttributeOrderNum('Project', 2);
		}
	}
	
	function getFieldValue( $attribute )
	{
		switch( $attribute )
		{
			case 'LinkType':
				return getFactory()->getObject('RequestLinkType')->getByRef('ReferenceName', 'implemented')->getId();
				
			case 'Project':
				return $_REQUEST['Project'] > 0 ? $_REQUEST['Project'] : parent::getFieldValue($attribute);

			case 'Function':
			case 'Estimation':
			case 'PlannedRelease':
			case 'SubmittedVersion':
			case 'Description':
				return parent::getFieldValue( $attribute );

			case 'Author':
				return getSession()->getUserIt()->getId();

			default:
				return $this->source_it->get($attribute);
		}
	}

	function getSourceIt()
	{
		if ( $_REQUEST['Request'] != '' ) {
			return array($this->source_it, 'Description');
		}
		return parent::getSourceIt();
	}

	function process()
	{
		global $session;

		if ( $this->getAction() != 'add' ) return parent::process();
		if ( $this->source_it->getId() == '' ) return parent::process();

		$session = new PMSession( $this->source_it->getRef('Project') );
		$method = new DuplicateIssuesWebMethod($this->source_it);
		try {
			$method->execute_request();
			$this->redirectOnAdded($method->getResult());
		}
		catch( Exception $e ) {
			$this->setRequiredAttributesWarning();
			$this->setWarningMessage($e->getMessage());
			$this->edit('');
		}
	}
}