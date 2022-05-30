<?php

class WikiPageBoard extends PMPageBoard
{
    function buildIterator()
    {
        $filters = $this->getTable()->getPredicateFilterValues();

        $contentNeeded = count(\TextUtils::parseFilterItems($filters['search'])) > 0
            || $this->getColumnVisibility('Content');
        if (!$contentNeeded) {
            $this->getObject()->setRegistry(new WikiPageRegistry());
        }

        return parent::buildIterator();
    }

    function getPredicates($filters)
    {
        return array_merge(
            parent::getPredicates($filters),
            array(
                new FilterAttributePredicate('IsDocument', '0,none')
            )
        );
    }

    function getBoardNamesPredicates() {
        return array(
            new FilterAttributePredicate('IsDocument', '0,none')
        );
    }
}
