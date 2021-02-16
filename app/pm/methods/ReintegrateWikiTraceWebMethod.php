<?php
use Devprom\ProjectBundle\Service\Wiki\WikiMergeService;
use Devprom\ProjectBundle\Service\Wiki\WikiBreakTraceService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class ReintegrateWikiTraceWebMethod extends WebMethod
{
    private $fromIt = null;
    private $toIt = null;

	function __construct( $fromIt = null, $toIt = null )
	{
		parent::__construct();
		$this->fromIt = $fromIt;
		$this->toIt = $toIt;
		$this->setRedirectUrl('devpromOpts.UpdateUI');
	}
	
	function getCaption()
	{
		return sprintf(text(2602),
            $this->fromIt->get('DocumentVersion') != '' ? $this->fromIt->get('DocumentVersion') : $this->fromIt->get('DocumentName'),
            $this->toIt->get('DocumentVersion') != '' ? $this->toIt->get('DocumentVersion') : $this->toIt->get('DocumentName')
            );
	}

    function getMethodName() {
        return 'Method:'.get_class($this).':CopyAttributes';
    }

    function getJSCall( $parms = array() )
    {
        if ( is_object($this->toIt) ) {
            $parms['toDocument'] = $this->toIt->get('DocumentId') == '' ? $this->toIt->getId() : $this->toIt->get('DocumentId');
        }
        $pageId = 0;
        if ( is_object($this->fromIt) ) {
            $pageId = $this->fromIt->getId();
        }
        $method = $this->getMethodName();
        foreach( $parms as $parm => $value ) {
            $method .= ":" . $parm . '=' . $value;
        }
        return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=".$method
            ."&".http_build_query($parms)."', ".$pageId.", ".$this->getRedirectUrl().")";
    }


	function execute_request()
 	{
        $className = getFactory()->getClass($_REQUEST['className']);
        if ( !class_exists($className) ) throw new Exception('Invalid class');
        $object = getFactory()->getObject($className);

        $registry = new WikiPageRegistryContent($object);
        $targetIt = $registry->Query(
                array(
                    new ParentTransitiveFilter($_REQUEST['ids']),
                    new SortDocumentClause()
                )
            );
        if ( $targetIt->getId() == '' ) throw new Exception('Target object is undefined');

        if ( $_REQUEST['Branch'] != '' ) {
            $documentIt = $object->getRegistry()->Query(
                array(
                    new WikiPageBranchFilter($_REQUEST['Branch']),
                    new FilterTextExactPredicate('UID', $targetIt->getRef('DocumentId')->get('UID') )
                )
            );
        }
        else {
            $documentIt = $object->getExact($_REQUEST['toDocument']);
        }
        if ( $documentIt->getId() == '' ) throw new Exception('Target document is undefined');

        $mergeService = new WikiMergeService(getFactory());
        $breakService = new WikiBreakTraceService(getFactory());

        while( !$targetIt->end() )
        {
            $sourceIt = $object->getRegistry()->Query(
                array(
                    new WikiDocumentFilter($documentIt),
                    new FilterTextExactPredicate('UID', $targetIt->get('UID'))
                )
            );

            if ( $sourceIt->getId() == '' && $targetIt->get('ParentPage') != '' )
            {
                $sourceIt = $object->getRegistry()->Query(
                    array(
                        new WikiDocumentFilter($documentIt),
                        new FilterTextExactPredicate('UID', $targetIt->getRef('ParentPage')->get('UID'))
                    )
                );

                $newPageIt = $mergeService->mergePage($targetIt, $sourceIt, $_REQUEST['traceClass']);
                $mergeService->copyAttributes($targetIt, $newPageIt, $_REQUEST['CopyAttributes']);

                $targetIt->moveNext();
                continue;
            }

            $copyAttributes = $_REQUEST['CopyAttributes'];
            foreach( $copyAttributes as $attribute ) {
                $data[$attribute] = $targetIt->getHtmlDecoded($attribute);
            }
            $data['ReintegratedTargetPageId'] = $targetIt->getId();

            if ( $object->modify_parms($sourceIt->getId(), $data) > 0 ) {
                $text = sprintf(text(2942),
                    $targetIt->get('DocumentVersion') != '' ? $targetIt->get('DocumentVersion') : $targetIt->get('DocumentName'),
                    $sourceIt->get('DocumentVersion') != '' ? $sourceIt->get('DocumentVersion') : $sourceIt->get('DocumentName')
                );
                $this->updateChangeLog($targetIt, $text);
                $this->updateChangeLog($sourceIt, $text, 2);
            }

            $mergeService->copyAttributes($targetIt, $sourceIt, $_REQUEST['CopyAttributes']);
            $breakService->execute($sourceIt, $targetIt);

            $registry = getFactory()->getObject('WikiPageTrace')->getRegistry();
            $traceIt = $registry->Query(
                array(
                    new FilterAttributePredicate('SourcePage', $targetIt->getId()),
                    new FilterAttributePredicate('TargetPage', $sourceIt->getId())
                )
            );
            if ( $traceIt->getId() != '' ) {
                $registry->Store($traceIt, array(
                    'IsActual' => 'Y'
                ));
            }

            $targetIt->moveNext();
        }
	}

    function updateChangeLog( $objectIt, $text, $visibilityLevel = 1 )
    {
        $change_parms = array(
            'Caption' => $objectIt->getDisplayName(),
            'ObjectId' => $objectIt->getId(),
            'EntityName' => $objectIt->object->getDisplayName(),
            'ClassName' => strtolower(get_class($objectIt->object)),
            'ChangeKind' => 'modified',
            'Content' => $text,
            'VisibilityLevel' => $visibilityLevel,
            'SystemUser' => getSession()->getUserIt()->getId()
        );
        getFactory()->getObject('ObjectChangeLog')->add_parms( $change_parms );
    }
}