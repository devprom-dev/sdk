<?php
use Devprom\ProjectBundle\Service\Issue\MergeIssueSingleService;
use Devprom\ProjectBundle\Service\Issue\MergeIssueLinksService;

class MergeIssuesWebMethod extends WebMethod
{
	function getCaption() {
		return text(2816);
	}

	function getMethodName() {
		return 'Method:'.get_class($this).':MergeType:MasterIssue';
	}

	function getObject() {
		return getFactory()->getObject('Request');
	}

    function execute_request() {
        $this->execute( $_REQUEST );
    }

    function execute( $parms )
    {
        $duplicateIt =
            $parms['ids'] != ''
                ? $this->getObject()->getExact(
                        array_diff(
                            TextUtils::parseIds($parms['ids']),
                            TextUtils::parseIds($parms['MasterIssue'])
                        )
                    )
                : $this->getObject()->getEmptyIterator();
        if ( $duplicateIt->getId() == '' ) throw new Exception('Objects are unknown');

        if ( !in_array($parms['MergeType'], array('1','2')) ) throw new Exception('Unknown strategy');

        $targetIt = $this->getObject()->getExact(TextUtils::parseIds($parms['MasterIssue']));
        if ( $targetIt->getId() == '' ) throw new Exception('Target is unknown');

        $strategy = $parms['MergeType'] == '1'
                        ? new MergeIssueSingleService()
                        : new MergeIssueLinksService();

        $strategy->run($targetIt, $duplicateIt);

        $this->setRedirectUrl($targetIt->getViewUrl());
    }
}
