<?php

class IterationRecentRegistry extends IterationRegistry
{
    function getFilters() {
        return array_merge (
            parent::getFilters(),
            array (
                new FilterAttributePredicate('IsClosed', 'N')
            )
        );
    }
}