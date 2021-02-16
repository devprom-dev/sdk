<?php

namespace Devprom\ProjectBundle\Service\Tooltip;
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";

class BrokenTraceExplainService
{
	private $object_it;
	
	public function __construct( $object_id )
	{
		getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
		
    	$this->object_it = getFactory()->getObject('WikiPage')->getExact($object_id);
	}
	
    public function getData()
    {
    	if ( $this->object_it->get('Suspected') < 1 ) return array();
    	
    	$traceIt = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('TargetPage', $this->object_it->getId()),
                new \FilterAttributePredicate('IsActual', 'N')
            )
    	);
    	if ( $traceIt->count() > 0 ) {
    	    return $this->getWikiTraceData($traceIt);
        }

        $traceIt = getFactory()->getObject('FunctionTrace')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('ObjectId', $this->object_it->getId()),
                new \FilterAttributePredicate('IsActual', 'N')
            )
        );
        if ( $traceIt->count() > 0 ) {
            return $this->getFeatureTraceData($traceIt);
        }

        return array();
   }

    function getWikiTraceData( $traceIt )
    {
        $uid = new \ObjectUID;
        $pages = array();
        $type = getFactory()->getObject('WikiType');

        while( !$traceIt->end() )
        {
            $class_name = $type->getExact($traceIt->get('SourcePageReferenceName'))->get('ClassName');
            if ( $class_name == '' ) {
                $traceIt->moveNext();
                continue;
            }

            $object_it = getFactory()->getObject($class_name)
                ->getExact($traceIt->get('SourcePage'));

            $change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
                array(
                    new \FilterAttributePredicate('WikiPage', $object_it->getId()),
                    new \SortRecentClause()
                )
            );

            $author = $change_it->getId() > 0
                ? $change_it->getRef('Author')->getDisplayName()
                : $object_it->getRef('Author')->getDisplayName();

            $recordcreated = $change_it->getId() > 0
                ? $change_it->getDateTimeFormat('RecordCreated')
                : $object_it->getDateTimeFormat('RecordCreated');

            $pages[] = array (
                'title' => $traceIt->getRef('UnsyncReasonType')->getDisplayName(),
                'ref' => $uid->getUidWithCaption($object_it),
                'author' => $author,
                'date' => $recordcreated
            );

            $traceIt->moveNext();
        }

        return array (
            'pages' => $pages,
            'description' => text(1734)
        );
    }

    function getFeatureTraceData( $traceIt )
    {
        $uid = new \ObjectUID;

        $pages[] = array (
            'title' => text(2702),
            'ref' => $uid->getUidWithCaption($traceIt->getRef('Feature')),
            'author' => '',
            'date' => $traceIt->getDateTimeFormat('RecordModified')
        );

        return array (
            'pages' => $pages,
            'description' => text(1734)
        );
    }
}