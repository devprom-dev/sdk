<?php

class IssueRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributeNullPredicate('Type')
            )
        );
    }
}