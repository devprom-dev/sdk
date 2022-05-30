<?php
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";

class RequestFormDuplicate extends RequestForm
{
	private $source_it = null;
	private $duplicateMethod = null;

	public function __construct( $object )
	{
        $this->source_it = $object->createCachedIterator(
            array(
                \JsonWrapper::decode(str_replace('\'', '"', $_REQUEST['Request']))
            )
        );
		parent::__construct($object);
	}
	
	public function extendModel()
	{
		parent::extendModel();
		
		$object = $this->getObject();
		$object->addAttribute('LinkType', 'REF_RequestLinkTypeId', translate('Тип связи'), true, false, '', 1);

		$this->duplicateMethod = new DuplicateIssuesWebMethod($this->source_it);
	}
	
	function getFieldValue( $attribute )
	{
		switch( $attribute )
		{
			case 'LinkType':
				return getFactory()->getObject('RequestLinkType')->getByRef('ReferenceName', 'implemented')->getId();
            case 'Type':
                if ( $_REQUEST['Project'] > 0 ) {
                    $typeIt = getFactory()->getObject('RequestType')
                        ->getByRef('ReferenceName', $this->source_it->getRef('Type')->get('ReferenceName'));
                    return $typeIt->getId();
                }
                return '';
            case 'Project':
                return parent::getFieldValue( $attribute );
			default:
                $defaults = $this->duplicateMethod->getAttributesDefaults($this->source_it);
                if ( array_key_exists($attribute, $defaults) ) {
                    return $defaults[$attribute];
                }
			    if ( in_array($attribute, $this->duplicateMethod->getAttributesToReset()) ) {
                    return parent::getFieldValue( $attribute );
                }
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

    function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();
		if ( $this->source_it->getId() == '' ) return parent::process();

		try {
			if ( $this->source_it->get('Project') != getSession()->getProjectIt()->getId() ) {
				if ( !$this->persist() ) return false;
                $this->duplicateMethod->linkIssues(
					array(
						'pm_ChangeRequest' => array (
							$this->source_it->getId() => $this->getObjectIt()->getId()
						)
					)
				);
				$this->redirectOnAdded($this->getObjectIt());
			}
			else {
                $this->duplicateMethod->execute_request();
				$this->redirectOnAdded($this->duplicateMethod->getResult());
			}
			return true;
		}
		catch( Exception $e ) {
			$this->setRequiredAttributesWarning();
			$this->setWarningMessage($e->getMessage());
			$this->edit('');
			return false;
		}
	}
}