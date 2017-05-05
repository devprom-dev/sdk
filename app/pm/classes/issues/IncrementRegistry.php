<?php

class IncrementRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributeNotNullPredicate('Type')
            )
        );
    }
}