<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

abstract class RequestBusinessActionChangeStateBase extends BusinessActionWorkflow
{
    abstract function getFilters( $object_it );
    abstract function getStateFilters( $object_it );

	function apply( $object_it )
 	{
 	    global $session;

 	    $request_it = (new Request)->getRegistry()->Query(
 	        array_merge(
 	            $this->getFilters($object_it),
                array(
                    new FilterNotInPredicate($object_it->getId())
                )
            ));

 	    $selfProjectIt = getSession()->getProjectIt();

 	    try {
            while( !$request_it->end() ) {
                $session = new PMSession($request_it->getRef('Project'));

                $duplicate_it = (new Request)->getRegistry()->Query(
                        array(new FilterInPredicate($request_it->getId()))
                    )->getSpecifiedIt();

                $state_it = getFactory()->getObject($duplicate_it->object->getStateClassName())
                    ->getRegistry()->Query(
                        array_merge(
                            array(
                                new FilterVpdPredicate($duplicate_it->get('VPD')),
                                new SortOrderedClause()
                            ),
                            $this->getStateFilters($duplicate_it)
                        )
                    );

                if ( $state_it->getId() == '' ) {
                    \Logger::getLogger('System')->error('There is no state required');
                    $request_it->moveNext();
                    continue;
                }

                $service = new WorkflowService($duplicate_it->object);
                $service->moveToState($duplicate_it, $state_it->get('ReferenceName'));

                $request_it->moveNext();
            }
        }
        catch(\Exception $e) {
            \Logger::getLogger('System')->error($e->getMessage() . $e->getTraceAsString());
        }
        finally {
            $session = new PMSession($selfProjectIt);
        }

 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
}