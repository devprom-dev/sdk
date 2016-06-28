<?php

class BugRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('Type', 'bug')
            )
        );
    }
}