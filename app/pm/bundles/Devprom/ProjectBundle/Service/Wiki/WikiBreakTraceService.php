<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiBreakTraceService
{
    private $factory = null;

    function __construct( $factory ) {
        $this->factory = $factory;
    }

    function execute( $object_it )
    {
        if ( $object_it->getId() < 1 ) throw new \Exception('Unknown Wiki is given');

        $registry = $this->factory->getObject('WikiPageTrace')->getRegistry();
        $trace_it = $registry->Query(
            array (
                new \FilterAttributePredicate('SourcePage', $object_it->getId() ),
                new \WikiTraceToBreakPredicate()
            )
        );
        while ( !$trace_it->end() )
        {
            $registry->Store( $trace_it, array(
                'IsActual' => 'N',
                'UnsyncReasonType' => 'text-changed'
            ));
            $trace_it->moveNext();
        }
    }
}