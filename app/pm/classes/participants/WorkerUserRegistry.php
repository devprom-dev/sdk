<?php

class WorkerUserRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        $filters = parent::getFilters();
        $exactFilters = array_filter($filters, function($value) {
            return $value instanceof FilterInPredicate;
        });
        $workerPredicate = new UserWorkerPredicate();
        $workerPredicate->hasTasks(true);
        return array_merge(
            $filters,
            array (
                count($exactFilters) > 0 || getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant'))
                    ? $workerPredicate
                    : new FilterInPredicate(getSession()->getUserIt()->getId())
            )
        );
    }

    function getPersisters()
    {
        return array_merge(
            array (
                new UserParticipatesDetailsPersister()
            ),
            parent::getPersisters()
        );
    }
}