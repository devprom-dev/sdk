<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiBaselineService
{
    private $factory = null;
    private $session = null;

    function __construct( $factory, $session ) {
        $this->factory = $factory;
        $this->session = $session;
    }

    function storeBaseline( $objectIt, $caption, $stageId = '' )
    {
        $snapshot = $this->factory->getObject('Snapshot');
        $versioned = new \VersionedObject();
        $versioned_it = $versioned->getExact(get_class($objectIt->object));

        $versionId = $snapshot->add_parms( array (
            'Caption' => $caption,
            'ListName' => get_class($objectIt->object).':'.$objectIt->getId(),
            'Stage' => $stageId,
            'ObjectId' => $objectIt->getId(),
            'ObjectClass' => get_class($objectIt->object),
            'SystemUser' => $this->session->getUserIt()->getId(),
            'VPD' => $objectIt->get('VPD')
        ));

        return $snapshot->freeze(
            $versionId,
            $versioned_it->getId(),
            array($objectIt->getId()),
            $versioned_it->get('Attributes')
        );
    }

    function storeInitialBaseline( $objectIt ) {
        return $this->storeBaseline($objectIt,
            defined('DOCS_INITIAL_BASELINE_NAME') ? DOCS_INITIAL_BASELINE_NAME : text(2306)
            );
    }

    function getBaselineIt( $stringValue ) {
        $baselineIt = $this->factory->getObject('Snapshot')->getExact($stringValue);
        if ( $baselineIt->getId() == '' ) throw new \Exception('Wrong baseline is given: ' . $stringValue);
        return $baselineIt;
    }

    function getBaselineRegistry( $documentIt, $baseline )
    {
        if ( !$baseline instanceof \OrderedIterator ) {
            $baseline = $this->getBaselineIt($baseline);
        }
        $registry = new \WikiPageRegistryVersion($documentIt->object);
        $registry->setDocumentIt($documentIt);
        $registry->setSnapshotIt($baseline);
        return $registry;
    }

    function getComparableBaselineIt( $documentIt, $stringValue )
    {
        if( preg_match('/document[:]?(\d+)/', $stringValue, $matches) ) {
            $registry = new \WikiPageRegistryContent($documentIt->object);
            return $registry->Query(array(new \FilterInPredicate($matches[1])));
        }
        else if ( $stringValue > 0 ) {
            $snapshot = new \WikiPageComparableSnapshot($documentIt);
            return $snapshot->getExact($stringValue);
        }
        else {
            $documentIt->object->getEmptyIterator();
        }
    }

    function getComparableRegistry($documentIt, $compareToIt)
    {
        if ( !$compareToIt instanceof \OrderedIterator ) {
            $compareToIt = $this->getComparableBaselineIt($documentIt, $compareToIt);
        }
        if ( !$compareToIt instanceof \OrderedIterator ) {
            return $documentIt->object->getRegistry();
        }

        if( $compareToIt->object instanceof \WikiPageComparableSnapshot ) {
            $registry = new \WikiPageRegistryVersionStructure($documentIt->object);
            $registry->setDocumentIt($documentIt);
            $registry->setSnapshotIt($compareToIt);
            return $registry;
        }
        else {
            $registry = new \WikiPageRegistryBaseline($documentIt->object);
            $registry->setDocumentIt($documentIt);
            if ( in_array($compareToIt->get('Type'), array('branch','document')) ) {
                $registry->setBaselineIt($documentIt->object->getExact($compareToIt->get('ObjectId')));
            }
            else {
                $registry->setBaselineIt($compareToIt);
            }
            return $registry;
        }
    }

    function getComparedPageIt( $pageIt, $compareToIt )
    {
        $registry = new \WikiPageRegistryComparison($pageIt->object);
        $registry->setPageIt($pageIt);
        $registry->setBaselineIt($compareToIt);
        return $registry->Query();
    }
}