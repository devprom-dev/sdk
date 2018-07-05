<?php

class RequestFormLinked extends RequestForm
{
	private $source_it = null;

	public function __construct( $object )
	{
	    $ids = TextUtils::parseIds($_REQUEST['IssueLinked']);
	    if ( count($ids) < 1 ) $ids = array(0);

        $this->source_it = $object->getRegistry()->Query(
            array (
                new FilterInPredicate($ids)
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
        $object->addAttribute('IssueLinked', 'INTEGER', '', false, false);
	}
	
	function getSourceIt()
	{
        $result = array();
	    if ( $this->source_it->count() == 1 ) {
            $result[] = array($this->source_it, 'Description');
		}
		return array_merge(parent::getSourceIt(), $result);
	}

    function persist()
	{
		if ( $this->source_it->getId() == '' ) return parent::persist();
        $result = parent::persist();

        if ( $this->getAction() == 'add' ) {
            $link = getFactory()->getObject('pm_ChangeRequestLink');
            while( !$this->source_it->end() ) {
                $link->add_parms(
                    array(
                        'SourceRequest' => $this->getObjectIt()->getId(),
                        'TargetRequest' => $this->source_it->getId(),
                        'LinkType' => $_REQUEST['LinkType']
                    )
                );
                $this->source_it->moveNext();
            }
        }

        return $result;
	}
}