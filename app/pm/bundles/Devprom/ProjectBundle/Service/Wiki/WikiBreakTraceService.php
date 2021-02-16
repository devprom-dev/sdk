<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiBreakTraceService
{
    private $factory = null;

    function __construct( $factory ) {
        $this->factory = $factory;
    }

    function execute( $object_it, $ignoreTargetPageIt = null )
    {
        if ( $object_it->getId() < 1 ) throw new \Exception('Unknown Wiki is given');

        $contentRegistry = new \WikiPageRegistryContent($object_it->object);
        $registry = $this->factory->getObject('WikiPageTrace')->getRegistry();

        $trace_it = $registry->Query(
            array (
                new \FilterAttributePredicate('SourcePage', $object_it->getId() ),
                new \WikiTraceToBreakPredicate()
            )
        );

        while ( !$trace_it->end() )
        {
            if ( is_object($ignoreTargetPageIt) && $ignoreTargetPageIt->getId() == $trace_it->get('TargetPage') ) {
                $trace_it->moveNext();
                continue;
            }

            $content = $contentRegistry->Query(
                    array(
                        new \FilterInPredicate(array($trace_it->get('SourcePage'),$trace_it->get('TargetPage')))
                    )
                )->getRowset();

            if ( $content[0]['Content'] == $content[1]['Content'] ) {
                $trace_it->moveNext();
                continue;
            }

            $registry->Store( $trace_it, array(
                'IsActual' => 'N',
                'RecordModified' => $trace_it->get('RecordModified'),
                'UnsyncReasonType' => 'text-changed'
            ));

            $trace_it->moveNext();
        }
    }
}