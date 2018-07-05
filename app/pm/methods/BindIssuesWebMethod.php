<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWebMethod.php";

class BindIssuesWebMethod extends WebMethod
{
	function getCaption() {
		return text(2519);
	}

	function getMethodName() {
		return 'Method:'.get_class($this).':SourceIssue:LinkType';
	}

	function getObject() {
		return getFactory()->getObject('Request');
	}

    function execute_request() {
        $this->execute( $_REQUEST );
    }

    function execute( $parms )
    {
        $object_it =
            $parms['ids'] != ''
                ? $this->getObject()->getExact(TextUtils::parseIds($parms['ids']))
                : $this->getObject()->getEmptyIterator();
        if ( $object_it->getId() == '' ) throw new Exception('Objects should be passed');

        $linkTypeIt = getFactory()->getObject('RequestLinkType')->getExact($parms['LinkType']);
        if ( $linkTypeIt->getId() == '' ) throw new Exception('Link type should be passed');

        $sourceIt = getFactory()->getObject('Request')->getExact($parms['SourceIssue']);
        if ( $sourceIt->getId() == '' ) throw new Exception('Source issue should be passed');

        $link = getFactory()->getObject('pm_ChangeRequestLink');
        while( !$object_it->end() ) {
            $link->add_parms(
                array(
                    'SourceRequest' => $sourceIt->getId(),
                    'TargetRequest' => $object_it->getId(),
                    'LinkType' => $linkTypeIt->getId()
                )
            );
            $object_it->moveNext();
        }
    }
}
