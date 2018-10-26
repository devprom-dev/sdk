<?php

include_once "WorklfowMovementEventHandler.php";

class ApplyBusinessActionsEventHandler extends WorklfowMovementEventHandler
{
	function handle( $object_it )
	{
        if ( $object_it->get('StateObject') != '' ) {
            $transitionIt = getFactory()->getObject('pm_StateObject')
                ->getExact($object_it->get('StateObject'))->getRef('Transition');
            $this->applyTransitionActions($transitionIt, $object_it);
        }

		$state_it = $object_it->getStateIt();
		if ( $state_it->getId() != '' ) {
            $this->applyStateActions($state_it, $object_it);
        }
	}

	public function applyTransitionActions( $transition_it, $object_it )
    {
        if ( $transition_it->getId() == '' ) return;
        $action_it = getFactory()->getObject('TransitionAction')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('Transition', $transition_it->getId()),
                new FilterVpdPredicate($object_it->get('VPD'))
            )
        );
        $this->applyActions($action_it, $object_it);
    }

	public function applyStateActions( $state_it, $object_it )
    {
        if ( $state_it->getId() == '' ) return;
        $action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('State', $state_it->getId()),
                new FilterVpdPredicate($object_it->get('VPD'))
            )
        );
        $this->applyActions($action_it, $object_it, 'BusinessActionWorkflow');
    }

    protected function applyActions( $action_it, $object_it, $type = '' )
    {
        $action = getFactory()->getObject('StateBusinessAction');
        while ( !$action_it->end() )
        {
            $rule_it = $action_it->getRef('ReferenceName', $action);
            if ( !is_object($rule_it)  ) {
                $action_it->moveNext();
                continue;
            }
            if ( $type != '' && !$rule_it->checkType($type)) {
                $action_it->moveNext();
                continue;
            }
            try {
                Logger::getLogger('System')->info('Applying system action: '.$rule_it->getDisplayName());
                $rule_it->apply( $object_it, $this->getData() );
            }
            catch( Exception $e ) {
                Logger::getLogger('System')->error(
                    'Unable complete system action "'.$rule_it->getDisplayName().'"'.PHP_EOL.
                    $e->getMessage().PHP_EOL.
                    $e->getTraceAsString()
                );
            }
            $action_it->moveNext();
        }
    }
}