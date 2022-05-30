<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class OpenBrokenTraceWebMethod extends WebMethod
{
	public function execute_request()
	{
		if ( $_REQUEST['object'] < 1 ) throw new Exception('Object identifier is required');
		$page_it = getFactory()->getObject('WikiPage')->getExact($_REQUEST['object']);

		$trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('TargetPage', $page_it->getId()),
                new FilterAttributePredicate('IsActual', 'N')
            )
		);
		if ( $trace_it->getId() > 0 ) {
		    $redirectUrl = $this->processWikiTrace($page_it, $trace_it);
        }

		if ( $redirectUrl == '' ) {
            $trace_it = getFactory()->getObject('FunctionTrace')->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ObjectId', $page_it->getId()),
                    new FilterAttributePredicate('IsActual', 'N')
                )
            );
            if ( $trace_it->getId() > 0 ) {
                $redirectUrl = $this->processFeatureTrace($trace_it);
            }
        }

		if ( $redirectUrl == '' ) {
            throw new Exception('No broken trace was found');
        }

		echo $redirectUrl;
	}

	function processWikiTrace( $page_it, $trace_it )
    {
        if ( $trace_it->get('Type') == 'branch' )
        {
            $type_it = getFactory()->getObject('WikiType')->getExact($page_it->get('ReferenceName'));

            $broken = getFactory()->getObject($type_it->get('ClassName'));
            $broken_it = $broken->getExact($page_it->getId());
            $url = $broken_it->getUidUrl();

            if ( $trace_it->get('UnsyncReasonType') == 'text-changed' ) {
                $attributes = array_diff(
                    $broken->getAttributesByGroup('trace-branches'),
                    array (
                        'FeatureRequirements'
                    )
                );
                foreach( $attributes as $attribute ) {
                    if ( $broken->getAttributeClass($attribute) != get_class($broken) ) continue;
                    if ( !in_array($trace_it->get('SourcePage'), \TextUtils::parseIds($broken_it->get($attribute)))
                            && !in_array($trace_it->get('TargetPage'), \TextUtils::parseIds($broken_it->get($attribute))) ) continue;
                    $url .= '&hide=all&show=Content-'.$attribute;
                    break;
                }
                $url .= '&linkstate=nonactual';
            }
            else {
                $url .= '&compareto=document:'.$trace_it->get('SourceDocumentId');
            }

            return $url;
        }
        else
        {
            $type = getFactory()->getObject('WikiType');

            $source_type_it = $type->getExact($trace_it->get('SourcePageReferenceName'));
            $target_type_it = $type->getExact($trace_it->get('TargetPageReferenceName'));

            $target = getFactory()->getObject($target_type_it->get('ClassName'));
            $target_it = $target->getExact($trace_it->get('TargetPage'));

            $url = $target_it->getUidUrl();

            $attributes = array_diff(
                $target->getAttributesByGroup('source-attribute'),
                $target->getAttributesByGroup('trace-branches'),
                array (
                    'FeatureRequirements'
                )
            );
            foreach( $attributes as $attribute ) {
                if ( $target->getAttributeClass($attribute) == $source_type_it->get('ClassName') ) {
                    $url .= '&hide=all&show=Content-'.$attribute;
                    break;
                }
            }

            return $url.'&linkstate=nonactual';
        }
    }

    function processFeatureTrace( $trace_it ) {
	    $show = $trace_it->get('Issues') != '' ? 'FeatureIssues' : 'FeatureRequirements';
        return $trace_it->getObjectIt()->getUidUrl() . '&hide=all&show=Content-'.$show.'&linkstate=nonactual';
    }
}
