<?php

class BusinessActionModifiedEvent extends ObjectFactoryNotificator
{
	function modify( $prev_object_it, $object_it )
	{
        if ( !$object_it instanceof StatableIterator ) return;

        $this->applyRules(
            $object_it->object->getExact($object_it->getId()),
            $prev_object_it,
            array_diff_assoc($object_it->getData(), $prev_object_it->getData()),
            TRIGGER_ACTION_MODIFY
        );
	}

	function add( $object_it )
    {
        if ( $object_it->object instanceof WikiPageChange )
        {
            $page_it = $object_it->getRef('WikiPage');
            $this->applyRules($page_it, $page_it, array('Content'), TRIGGER_ACTION_ADD);
        }
        if ( $object_it->object instanceof Request ) {
            $this->applyRules($object_it, $object_it, $object_it->getData(), TRIGGER_ACTION_ADD);
        }
    }

	function delete( $object_it ) {;}

    protected function applyRules( $object_it, $prev_object_it, $attributes, $action )
    {
        $state_it = $object_it->getStateIt();
        if ( $state_it->getId() < 1 ) return;

        $action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('State', $state_it->getId()),
                new FilterVpdPredicate($object_it->get('VPD'))
            )
        );

        $actionObject = getFactory()->getObject('StateBusinessAction');
        while ( !$action_it->end() )
        {
            $rule_it = $action_it->getRef('ReferenceName', $actionObject);
            if ( is_object($rule_it) && $rule_it->checkType('BusinessActionShift') )
            {
                $prev_object_it->object->removeNotificator(get_class($this));
                Logger::getLogger('System')->info('Applying system action: '.$rule_it->getDisplayName());

                $rule_it->getRule()->applyContent( $prev_object_it, $attributes, $action );
            }
            $action_it->moveNext();
        }
    }
}