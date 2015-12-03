<?php

class BusinessActionModifiedEvent extends ObjectFactoryNotificator
{
	function modify( $prev_object_it, $object_it )
	{
        if ( !$object_it instanceof StatableIterator ) return;

        $this->applyRules(
            $prev_object_it,
            array_diff_assoc($object_it->getData(), $prev_object_it->getData())
        );
	}

	function add( $object_it )
    {
        if ( $object_it->object instanceof WikiPageChange )
        {
            $page_it = $object_it->getRef('WikiPage');
            $this->applyRules($page_it, array('Content'));
        }
    }

	function delete( $object_it ) {;}

    protected function applyRules( $object_it, $attributes )
    {
        $state_it = $object_it->getStateIt();
        if ( $state_it->getId() < 1 ) return;

        $action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('State', $state_it->getId()),
            )
        );
        $action = getFactory()->getObject('StateBusinessAction');
        while ( !$action_it->end() )
        {
            $rule_it = $action_it->getRef('ReferenceName', $action);
            if ( is_object($rule_it) && $rule_it->checkType('BusinessActionShift') ) {
                $rule_it->getRule()->apply( $object_it, $attributes );
            }
            $action_it->moveNext();
        }
    }
}