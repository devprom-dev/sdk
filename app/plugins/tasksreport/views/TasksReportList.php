<?php

class TasksReportList extends PMPageList
{
    function buildItemsHash($registry, $predicates) {
        $ids = join(',',$registry->Query($predicates)->fieldToArray('Activities'));
        if ( $ids == '' ) $ids = '0';
        return \TextUtils::secureData(
            getFactory()->getObject('Activity')->getRegistryBase()->QueryKeys(
                array( new FilterInPredicate($ids) ),
                false
            )
        );
    }
}