<?php
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

class TaskMetricsEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1)
	{
        if ( !$object_it->object instanceof Task ) return;

        if ( in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) {
            $service = new StoreMetricsService();
            $service->forceTaskMetrics(
                array(
                    $object_it->get('Assignee') != ''
                        ? new \FilterAttributePredicate('Assignee', $object_it->get('Assignee'))
                        : new \FilterInPredicate(array($object_it->getId())) ,
                    new \StatePredicate('notresolved')
                )
            );
        }

        if ( $object_it->get('Release') != '' ) {
            getFactory()->getObject('Iteration')
                ->getExact($object_it->get('Release'))->storeMetrics();
        }

        if ( $object_it->get('Assignee') != '' ) {
            $service = new StoreMetricsService();
            $service->forceUsersMetrics(
                array(
                    new \FilterInPredicate($object_it->get('Assignee'))
                )
            );
        }
	}
}