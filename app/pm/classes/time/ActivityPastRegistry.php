<?php

class ActivityPastRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new FilterDateBeforePredicate('ReportDate', strftime('%Y-%m-%d', strtotime('-1 day')))
            )
        );
    }
}