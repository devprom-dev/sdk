<?php

class RequestFormLinked extends RequestForm
{
	private $source_it = null;

	public function __construct( $object )
	{
        $this->source_it = $object->getRegistry()->Query(
            array (
                new FilterInPredicate($_REQUEST['IssueLinked'])
            )
        );
		parent::__construct($object);
	}
	
	public function extendModel()
	{
		parent::extendModel();

        $object = $this->getObject();
        $object->addAttribute('LinkType', 'REF_RequestLinkTypeId', translate('Тип связи'), true, false, '', 1);
        $object->setAttributeRequired('LinkType', true);
        $object->setAttributeVisible('Author', false);
        $object->addAttribute('IssueLinked', 'INTEGER', '', false, false);
	}
	
	function getSourceIt()
	{
        $result = array();
	    if ( $_REQUEST['IssueLinked'] != '' ) {
            $result[] = array($this->source_it, 'Description');
		}
		return array_merge(parent::getSourceIt(), $result);
	}

    function persist()
	{
		if ( $this->source_it->getId() == '' ) return parent::persist();

        $result = parent::persist();

        if ( $this->getAction() == 'add' ) {
            getFactory()->getObject('pm_ChangeRequestLink')->add_parms(
                array(
                    'SourceRequest' => $this->getObjectIt()->getId(),
                    'TargetRequest' => $this->source_it->getId(),
                    'LinkType' => $_REQUEST['LinkType']
                )
            );
        }

        return $result;
	}
}