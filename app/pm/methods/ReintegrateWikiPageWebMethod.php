<?php
use Devprom\ProjectBundle\Service\Wiki\WikiMergeService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class ReintegrateWikiPageWebMethod extends WebMethod
{
    private $pageIt = null;

	function __construct( $pageIt = null )
	{
		parent::__construct();
		$this->pageIt = $pageIt;
	}
	
	function getCaption()
	{
		return sprintf( text(2602),
            $this->pageIt->get('DocumentVersion') != ''
                ? $this->pageIt->get('DocumentVersion')
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

        $data = array_merge(
            array_map( function($value) {
                return \TextUtils::decodeHtml($value);
            }, $this->pageIt->getData()),
            array(
                'StateObject' => '',
                'ParentPage' => $parentIt->getId(),
                'DocumentId' => $parentIt->get('DocumentId'),
                'DocumentVersion' => $parentIt->get('DocumentVersion'),
                'SortIndex' => '',
                'ParentPath' => ''
            )
        );
        unset($data['WikiPageId']);
        $pageIt = $parentIt->object->getRegistry()->Create($data);

        $service = new WikiMergeService(getFactory());
        $service->mergeTraces($this->pageIt, $pageIt);

        $traceClass = getFactory()->getClass($_REQUEST['traceClass']);
        if ( class_exists($traceClass) ) {
            getFactory()->getObject($traceClass)->getRegistry()->Merge(
                array(
                    'SourcePage' => $pageIt->getId(),
                    'TargetPage' => $this->pageIt->getId(),
                    'Type' => 'branch'
                ),
                array(
                    'SourcePage', 'TargetPage'
                )
            );
        }
	}
}