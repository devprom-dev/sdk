<?php

class ProjectRoleInheritedRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new ProjectRoleInheritedFilter(),
                new FilterBaseVpdPredicate()
            )
        );
    }
}