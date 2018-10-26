<?php
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";

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

		if ( $_REQUEST['Project'] == '' ) {
			$object->setAttributeVisible('Project', true);
			$object->setAttributeType('Project', 'REF_ProjectAccessibleActiveId');
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
			case 'Owner':
			case 'Customer':
            case 'OrderNum':
            case 'State':
				return parent::getFieldValue( $attribute );

			case 'Author':
				return getSession()->getUserIt()->getId();

            case 'Description':
                $uid = new ObjectUID();
                return '{{'.$uid->getObjectUid($this->source_it).'}}';

			default:
				return $this->source_it->get($attribute);
		}
	}

	function getSourceIt()
	{
        $result = array();
	    if ( $_REQUEST['Request'] != '' ) {
            $result[] = array($this->source_it, 'Description');
		}
		return array_merge(parent::getSourceIt(), $result);
	}

	function IsAttributeEditable($attribute)
    {
        switch( $attribute ) {
            case 'Project':
                return true;
            default:
                return parent::IsAttributeEditable($attribute);
        }
    }

    function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();
		if ( $this->source_it->getId() == '' ) return parent::process();

		$method = new DuplicateIssuesWebMethod($this->source_it);
		try {
			if ( $this->source_it->get('Project') != getSession()->getProjectIt()->getId() ) {
				if ( !$this->persist() ) return false;
				$method->linkIssues(
					array(
						'pm_ChangeRequest' => array (
							$this->source_it->getId() => $this->getObjectIt()->getId()
						)
					)
				);
				$this->redirectOnAdded($this->getObjectIt());
			}
			else {
				$method->execute_request();
				$this->redirectOnAdded($method->getResult());
			}
		}
		catch( Exception $e ) {
			$this->setRequiredAttributesWarning();
			$this->setWarningMessage($e->getMessage());
			$this->edit('');
		}
	}
}