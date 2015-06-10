<?php

class RequestFormDuplicate extends RequestForm
{
	private $source_it = null;

	public function __construct( $object )
	{
		parent::__construct($object);
		$this->source_it = $object->getExact($_REQUEST['Request']);
	}
	
	public function extendModel()
	{
		parent::extendModel();
		
		$object = $this->getObject();
		$object->addAttribute('LinkType', 'REF_RequestLinkTypeId', translate('Тип связи'), true, false, '', 1);
		$object->setAttributeVisible('Project', true);
		$object->setAttributeType('Project', 'REF_ProjectAccessibleId');
		$object->setAttributeOrderNum('Project', 2);
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
				return parent::getFieldValue( $attribute );
				
			default:
				return $this->source_it->get($attribute);
		}
	}

	function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();
		 
		$method = new DuplicateIssuesWebMethod($this->source_it);
		try {
			$method->execute_request();
		}
		catch( Exception $e ) {
			$this->setWarningMessage($e->getMessage());
			$this->edit('');
		}
	}
}