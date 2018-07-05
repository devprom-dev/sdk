<?php
namespace Devprom\ProjectBundle\Service\Wiki;

class WikiMergeService
{
    private $factory = null;

    function __construct( $factory ) {
        $this->factory = $factory;
    }

    function mergeTraces( $fromPageIt, $toPageIt )
    {
        $traceIt = $this->factory->getObject('WikiPageTrace')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('SourcePage', $fromPageIt->getId())
            )
        );
        while( !$traceIt->end() ) {
            $traceIt->object->modify_parms( $traceIt->getId(),
                array(
                    'SourcePage' => $toPageIt->getId()
                )
            );
            $traceIt->moveNext();
        }

        $service = new WikiDeltaService(getFactory());

        foreach( array('pm_ChangeRequestTrace', 'pm_TaskTrace') as $traceClassName ) {
            $traceIt = $this->factory->getObject($traceClassName)->getRegistry()->Query(
                array(
                    new \FilterAttributePredicate('ObjectId', $fromPageIt->getId()),
                    new \FilterAttributePredicate('ObjectClass', array('Requirement', 'TestScenario', 'HelpPage'))
                )
            );
            while( !$traceIt->end() ) {
                $traceIt->object->modify_parms( $traceIt->getId(),
                    array(
                        'ObjectId' => $toPageIt->getId()
                    )
                );

                if ( $traceClassName == 'pm_ChangeRequestTrace' ) {
                    $requestIt = $traceIt->getRef('ChangeRequest');
                    $description = $requestIt->getHtmlDecoded('Description');

                    $replaceDelta = preg_match(REGEX_INCLUDE_PAGE, $description)
                        || preg_match(REGEX_INCLUDE_REVISION, $description);

                    if ( $replaceDelta ) {
                        $requestIt->object->modify_parms(
                            $requestIt->getId(),
                            array(
                                'Description' => $service->execute($toPageIt->copy())
                            )
                        );
                    }
                }

                $traceIt->moveNext();
            }
        }

        $traceIt = $this->factory->getObject('WikiPageFile')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('WikiPage', $fromPageIt->getId())
            )
        );
        while( !$traceIt->end() ) {
            $traceIt->object->modify_parms( $traceIt->getId(),
                array(
                    'WikiPage' => $toPageIt->getId()
                )
            );
            $traceIt->moveNext();
        }
    }
}