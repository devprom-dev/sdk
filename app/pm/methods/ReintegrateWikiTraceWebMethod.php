<?php
use Devprom\ProjectBundle\Service\Wiki\WikiMergeService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class ReintegrateWikiTraceWebMethod extends WebMethod
{
    private $traceIt = null;

	function __construct( $traceIt = null )
	{
		parent::__construct();
		$this->traceIt = $traceIt;
	}
	
	function getCaption()
	{
	    $pageIt = $this->traceIt->getRef('SourcePage');
		return sprintf(text(2602), $pageIt->get('DocumentVersion') != '' ? $pageIt->get('DocumentVersion') : $pageIt->get('DocumentName') );
	}
	
	function getJSCall( $parms = array() )
	{
		return parent::getJSCall(
		    array_merge(
		        array(
			        'link' => $this->traceIt->getId()
		        ),
                $parms
            )
        );
	}
	
	function execute_request()
 	{
        $className = getFactory()->getClass($_REQUEST['className']);
        if ( !class_exists($className) ) throw new Exception('Invalid class');

        $ids = TextUtils::parseIds($_REQUEST['link']);
 	    if ( count($ids) < 1 ) throw new Exception('Object is undefined');

 	    $this->traceIt = getFactory()->getObject('WikiPageTrace')->getExact($ids);
 	    if ( $this->traceIt->getId() == '' ) throw new Exception('Object is required');

        $targetIt = $this->traceIt->getRef('TargetPage');
        $sourceIt = $this->traceIt->getRef('SourcePage');

 	    getFactory()->getObject($className)->modify_parms( $sourceIt->getId(),
            array(
                'Content' => $targetIt->getHtmlDecoded('Content'),
                'ReintegratedTraceId' => $this->traceIt->getId()
            )
        );

 	    $service = new WikiMergeService(getFactory());
 	    $service->mergeTraces($targetIt, $sourceIt);
	}
}