<?php
include "RequestTypeIterator.php";
include "RequestTypeRegistry.php";

class RequestType extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_IssueType', new RequestTypeRegistry($this) );
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
        if ( !$this->IsAttributeVisible('Option1') ) {
            $this->addAttributeGroup('Option1', 'system');
        }
 		$this->setAttributeDescription( 'RelatedColor', text(1852) );
 	}
 	
	function createIterator() {
	    return new RequestTypeIterator( $this );
	}

	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
			case 'ReferenceName':
				return uniqid('IssueType_');
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}

    function getEmptyValueName() {
        return translate('Пожелание');
    }

	function getPage() {
	    return getSession()->getApplicationUrl($this).'project/dicts/RequestType?';
	}
}
