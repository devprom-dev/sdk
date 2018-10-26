<?php
use Devprom\ProjectBundle\Service\Wiki\WikiMergeService;
use Devprom\ProjectBundle\Service\Wiki\WikiBreakTraceService;
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

        if ( $targetIt->get('ParentPage') == '' ) {
            $this->traceIt = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
                array(
                    new WikiTraceTargetDocumentPredicate($targetIt->get('DocumentId')),
                    new WikiTraceSourceDocumentPredicate($sourceIt->get('DocumentId'))
                )
            );
        }

        $mergeService = new WikiMergeService(getFactory());
        $breakService = new WikiBreakTraceService(getFactory());

        while( !$this->traceIt->end() )
        {
            $targetIt = $this->traceIt->getRef('TargetPage');
            $sourceIt = $this->traceIt->getRef('SourcePage');

            getFactory()->getObject($className)->modify_parms( $sourceIt->getId(),
                array(
                    'Content' => $targetIt->getHtmlDecoded('Content'),
                    'State' => $targetIt->get('State'),
                    'ReintegratedTraceId' => $this->traceIt->getId()
                )
            );

            $mergeService->mergeTraces($targetIt, $sourceIt);
            $breakService->execute($sourceIt, $this->traceIt);

            $this->traceIt->moveNext();
        }

        if ( $targetIt->get('ParentPage') != '' ) return; // structure items merge only for root

        $registry = new WikiPageRegistryContent(getFactory()->getObject('WikiPage'));

        // create new pages
        $pageIt = $registry->Query(
            array(
                new FilterAttributePredicate('DocumentId', $targetIt->get('DocumentId')),
                new PMWikiSourceFilter('none')
            )
        );
        while( !$pageIt->end() )
        {
            $traceIt = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('TargetPage', $pageIt->get('ParentPage')),
                    new WikiTraceSourceDocumentPredicate($sourceIt->get('DocumentId'))
                )
            );
            if ( $traceIt->getId() != '' ) {
                $newPageIt = $mergeService->mergePage($pageIt, $traceIt->getRef('SourcePage'), $_REQUEST['traceClass']);
                $mergeService->mergeTraces($pageIt, $newPageIt);
            }
            $pageIt->moveNext();
        }

        // remove deleted pages
        $sourceUids = $registry->Query(
                array(
                    new FilterAttributePredicate('DocumentId', $sourceIt->get('DocumentId'))
                )
            )->fieldToArray('UID');
        $targetUids = $registry->Query(
            array(
                new FilterAttributePredicate('DocumentId', $targetIt->get('DocumentId'))
            )
        )->fieldToArray('UID');

        $missedUids = array_diff($sourceUids, $targetUids);
        if ( count($missedUids) > 0 )
        {
            $pageIt = $registry->Query(
                array(
                    new FilterAttributePredicate('DocumentId', $sourceIt->get('DocumentId')),
                    new FilterAttributePredicate('UID', $missedUids)
                )
            );
            while( !$pageIt->end() ) {
                $registry->Delete($pageIt);
                $pageIt->moveNext();
            }
        }
	}
}