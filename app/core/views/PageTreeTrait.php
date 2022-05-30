<?php

trait PageTreeTrait
{
    function getTemplate() {
        return "core/PageTreeGrid.php";
    }

    function buildItemsHash($registry, $predicates) {
        return \TextUtils::buildIds(
            $registry->QueryKeys(
                array_filter($predicates, function($predicate) {
                    return ! $predicate instanceof FilterInPredicate;
                })
            )->idsToArray()
        );
    }

    function buildGroup() {
        $this->getObject()->setAttributeOrderNum('Caption', 0); // Caption should be the first always
        return parent::buildGroup();
    }
}
