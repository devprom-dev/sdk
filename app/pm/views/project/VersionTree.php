<?php

class VersionTree extends VersionList
{
    function getTemplate() {
        return "core/PageTreeGrid.php";
    }

    function combineCaptionWithDescription() {
        return false;
    }

    function buildItemsHash($object, $predicates) {
        return \TextUtils::buildIds(
            $object->getRegistryBase()->Query(
                array_filter($predicates, function($predicate) {
                    return ! $predicate instanceof FilterInPredicate;
                })
            )->idsToArray()
        );
    }

    function getRenderParms()
    {
        $query = parse_url($this->getTable()->getFiltersUrl(), PHP_URL_QUERY);
        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    getSession()->getApplicationUrl($this->getObject()) . 'treegrid/stage?' . $query
            )
        );
    }
}