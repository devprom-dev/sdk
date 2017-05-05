<?php
include "predicates/WikiPageChangeTypeFilter.php";

class WikiPageChangeRegistry extends ObjectRegistrySQL
{
    public function getFilters()
    {
        $refName = $this->getObject()->getReferenceName();
        if ( $refName == '' ) return parent::getFilters();
        return array_merge(
            parent::getFilters(),
            array(
                new WikiPageChangeTypeFilter($refName)
            )
        );
    }
}