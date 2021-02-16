<?php
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionSuspectFeatureDocs extends BusinessActionWorkflow
{
 	function getId() {
 		return '4f73f3d0-58fc-429e-be9d-c42cc05e61e6';
 	}
	
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(2701);
 	}

    function apply( $object_it )
    {
        if ( $object_it->get('Function') == '' ) return false;

        $requestTraceRegistry = getFactory()->getObject('pm_ChangeRequestTrace')->getRegistry();
        $registry = getFactory()->getObject('FunctionTrace')->getRegistry();
        $traceIt = $registry->Query(
            array(
                new FilterAttributePredicate('Feature', $object_it->get('Function'))
            )
        );
        while( !$traceIt->end() ) {
            if ( $traceIt->get('IsActual') == 'N' ) {
                $issuesIds = join(',', array_unique(array_merge(
                    \TextUtils::parseIds($traceIt->get('Issues')),
                    array(
                        $object_it->getId()
                    )
                )));
            }
            else {
                $issuesIds = $object_it->getId();
            }

            $requestTraceIt = $requestTraceRegistry->Query(
                array(
                    new FilterAttributePredicate('ChangeRequest', $issuesIds),
                    new FilterAttributePredicate('ObjectId', $traceIt->get('ObjectId')),
                    new FilterAttributePredicate('ObjectClass', $traceIt->get('ObjectClass'))
                )
            );
            if ( $requestTraceIt->count() < 1 ) {
                $registry->Store($traceIt, array(
                    'IsActual' => 'N',
                    'Issues' => $issuesIds
                ));
            }
            $traceIt->moveNext();
        }

        return true;
    }
}