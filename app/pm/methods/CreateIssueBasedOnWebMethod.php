<?php
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class CreateIssueBasedOnWebMethod extends ObjectCreateNewWebMethod
{
	function __construct( $object )
	{
		parent::__construct($object);
	}

    function getCaption() {
        return text(2518);
    }

	function getNewObjectUrl()
	{
		$url = $this->getObject()->getPageName();
		return $url;
	}
	
	function url( $issueIds = array() ) {
		$parms = array (
            'IssueLinked' => join(',',$issueIds)
		);
		return parent::getJSCall($parms);
	}

	function hasAccess() {
        return parent::hasAccess() && (!getSession()->IsRDD() || $this->getObject() instanceof Issue);
    }
}