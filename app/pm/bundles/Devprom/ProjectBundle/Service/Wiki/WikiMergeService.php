<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiMergeService
{
    private $factory = null;

    function __construct( $factory ) {
        $this->factory = $factory;
    }

    function mergePage( $pageIt, $parentIt, $traceClassName )
    {
        $data = array_merge(
            array_map( function($value) {
                return \TextUtils::decodeHtml($value);
            }, $pageIt->getData()),
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
        $newPageIt = $parentIt->object->getRegistry()->Create($data);

        $traceClass = getFactory()->getClass($traceClassName);
        if ( class_exists($traceClass) ) {
            getFactory()->getObject($traceClass)->getRegistry()->Merge(
                array(
                    'SourcePage' => $pageIt->getId(),
                    'TargetPage' => $newPageIt->getId(),
                    'Type' => 'branch'
                ),
                array(
                    'SourcePage', 'TargetPage'
                )
            );
        }

        $text = sprintf(text(2942),
            $pageIt->get('DocumentVersion') != '' ? $pageIt->get('DocumentVersion') : $pageIt->get('DocumentName'),
            $newPageIt->get('DocumentVersion') != '' ? $newPageIt->get('DocumentVersion') : $newPageIt->get('DocumentName')
        );
        $this->updateChangeLog($pageIt, $text);
        $this->updateChangeLog($newPageIt, $text);

        return $newPageIt;
    }

    function updateChangeLog( $objectIt, $text )
    {
        $change_parms = array(
            'Caption' => $objectIt->getDisplayName(),
            'ObjectId' => $objectIt->getId(),
            'EntityName' => $objectIt->object->getDisplayName(),
            'ClassName' => strtolower(get_class($objectIt->object)),
            'ChangeKind' => 'modified',
            'Content' => $text,
            'VisibilityLevel' => 1,
            'SystemUser' => getSession()->getUserIt()->getId()
        );
        getFactory()->getObject('ObjectChangeLog')->add_parms( $change_parms );
    }

    function copyAttributes( $fromPageIt, $toPageIt, $attributes )
    {
        $references = $this->getReferences($fromPageIt, $attributes);

        $ids_map = array(
            'WikiPage' => array(
                $fromPageIt->getId() => $toPageIt->getId()
            )
        );

        $xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
        foreach( $references as $object ) {
            $xml .= $object->serialize2Xml();
        }
        $xml .= '</entities>';

        $context = new \CloneContext();
        $context->setIdsMap( $ids_map );
        $context->setUseExistingReferences( true );
        $context->setRestoreFromTemplate(false);
        $context->setReuseProject(true);

        foreach( $references as $object ) {
            $object = getFactory()->getObject( get_class($object) );
            \CloneLogic::Run( $context, $object, $object->createXMLIterator($xml), getSession()->getProjectIt());
        }
    }

    function getReferences( $pageIt, $attributes )
    {
        $references = array();

        if ( in_array('Attachments', $attributes) ) {
            $attachment = getFactory()->getObject('WikiPageFile');
            $attachment->addFilter( new \FilterAttributePredicate('WikiPage', $pageIt->getId()) );
            $references[] = $attachment;
        }

        if ( in_array('Tasks', $attributes) ) {
            $task = getFactory()->getObject('pm_TaskTrace');
            $task->addFilter(new \FunctionTraceObjectPredicate($pageIt));
            $references[] = $task;
        }

        if ( count(array_intersect(array('Issues','Increments'), $attributes)) > 0 ) {
            $issue = getFactory()->getObject('pm_ChangeRequestTrace');
            $issue->addFilter(new \FunctionTraceObjectPredicate($pageIt));
            $references[] = $issue;
        }

        if ( in_array('Feature', $attributes) ) {
            $feature = getFactory()->getObject('pm_FunctionTrace');
            $feature->addFilter(new \FunctionTraceObjectPredicate($pageIt));
            $references[] = $feature;
        }

        if ( in_array('Tags', $attributes) ) {
            $tag = getFactory()->getObject('WikiTag');
            $tag->addFilter(new \FilterAttributePredicate('Wiki', $pageIt->getId()));
            $references[] = $tag;
        }

        if ( in_array('Watchers', $attributes) ) {
            $references[] = new \Watcher($pageIt);
        }

        if ( in_array('RecentComment', $attributes) ) {
            $comment = getFactory()->getObject('Comment');
            $comment->addFilter(new \CommentObjectFilter($pageIt));
            $references[] = $comment;
        }

        $verticalTraceAttributes = $pageIt->object->getAttributesByGroup('source-attribute');
        if ( count(array_intersect($verticalTraceAttributes, $attributes)) > 0 ) {
            $trace = getFactory()->getObject('WikiPageTrace');
            $trace->addFilter( new \FilterAttributePredicate('TargetPage', $pageIt->getId()) );
            $trace->addFilter( new \FilterAttributePredicate('Type', 'coverage') );
            $references[] = $trace;
        }

        $traceAttributes = $pageIt->object->getAttributesByGroup('trace');
        if ( count(array_intersect($traceAttributes, $attributes)) > 0 ) {
            $trace = getFactory()->getObject('WikiPageTrace');
            $trace->addFilter( new \FilterAttributePredicate('SourcePage', $pageIt->getId()) );
            $trace->addFilter( new \FilterAttributePredicate('Type', 'coverage') );
            $references[] = $trace;
        }

        return $references;
    }
}