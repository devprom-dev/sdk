<?php
use Devprom\ProjectBundle\Service\Wiki\WikiMergeService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class ReintegrateWikiPageWebMethod extends WebMethod
{
    private $pageIt = null;
    private $parentIt = null;

	function __construct( $pageIt = null, $parentIt = null )
	{
		parent::__construct();
		$this->pageIt = $pageIt;
		$this->parentIt = $parentIt;
	}
	
	function getCaption()
	{
		return sprintf( text(2602),
            $this->parentIt->get('DocumentVersion') != ''
                ? $this->parentIt->get('DocumentVersion')
                : $this->pageIt->get('DocumentName')
        );
	}
	
	function getJSCall( $parms = array() )
	{
		return parent::getJSCall(
		    array_merge(
		        array(
			        'page' => $this->pageIt->getId()
		        ),
                $parms
            )
        );
	}
	
	function execute_request()
 	{
 	    $className = getFactory()->getClass($_REQUEST['className']);
 	    if ( !class_exists($className) ) throw new Exception('Invalid class');

 	    $ids = TextUtils::parseIds($_REQUEST['page']);
 	    if ( count($ids) < 1 ) throw new Exception('Object is undefined');

        $registry = new ObjectRegistrySQL(getFactory()->getObject($className));
 	    $this->pageIt = $registry->Query(
 	        array(
 	            new FilterInPredicate($ids)
            )
        );
 	    if ( $this->pageIt->getId() == '' ) throw new Exception('Object is required');

        $ids = TextUtils::parseIds($_REQUEST['parent']);
        if ( count($ids) < 1 ) throw new Exception('Parent is undefined');

 	    $parentIt = getFactory()->getObject($className)->getExact($ids);
        if ( $parentIt->getId() == '' ) throw new Exception('Parent is required');

        $service = new WikiMergeService(getFactory());
        $pageIt = $service->mergePage($this->pageIt, $parentIt, $_REQUEST['traceClass']);
        $service->mergeTraces($this->pageIt, $pageIt);
	}
}