<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiDeltaService
{
    private $factory = null;

    function __construct( $factory ) {
        $this->factory = $factory;
    }

    function execute( $source_it )
    {
        if ( $source_it->getId() == '' ) return '';
        if ( $source_it->get('Content') == "" && $source_it->get('TotalCount') < 1 ) return '';

        $registry = $this->factory->getObject('RequestTraceRequirement')->getRegistry();
        $traceIt = $registry->Query(
            array (
                new \RequestTraceObjectPredicate($source_it),
                new \FilterAttributePredicate('Type', REQUEST_TRACE_PRODUCT),
                new \SortAttributeClause('Revision.D')
            )
        );
        if ( $traceIt->get('Revision') != '' ) {
            return '{{R-'.$source_it->getId().':'.$traceIt->get('Revision').'-}}';
        }
        else {
            $changeIt = $this->factory->getObject('WikiPageChange')->getRegistry()->Query(
                array(
                    new \FilterAttributePredicate('WikiPage', $source_it->getId()),
                    new \WikiPageChangeYounger($source_it),
                    new \SortAttributeClause('RecordCreated.A')
                )
            );
            if ( $changeIt->getId() != '' ) {
                return '{{R-'.$source_it->getId().':'.$changeIt->getId().'-}}';
            }
            else {
                return '{{R-'.$source_it->getId().'}}';
            }
        }
    }
}