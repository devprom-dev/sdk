<?php
namespace Devprom\ProjectBundle\Service\Issue;

class MergeIssueLinksService extends MergeIssueService
{
    function run( $targetIssueIt, $duplicateIt )
    {
        $linkTypeIt = getFactory()->getObject('RequestLinkType')->getRegistry()->Query(
            array(
                'ReferenceName' => 'duplicates'
            )
        );
        if ( $linkTypeIt->getId() == '' ) throw new \Exception(text(2821));

        $registry = getFactory()->getObject('RequestLink')->getRegistry();
        while( !$duplicateIt->end() ) {
            $registry->Merge(
                array(
                    'SourceRequest' => $targetIssueIt->getId(),
                    'TargetRequest' => $duplicateIt->getId(),
                    'LinkType' => $linkTypeIt->getId()
                )
            );
            $duplicateIt->moveNext();
        }
    }
}