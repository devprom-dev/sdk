<?php

class WorkerUserRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        $filters = parent::getFilters();
        $exactFilters = array_filter($filters, function($value) {
            return $value instanceof FilterInPredicate;
        });
       return array_merge(
            $filters,
            array (
                count($exactFilters) > 0 || getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant'))
                    ? new UserWorkerPredicate()
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