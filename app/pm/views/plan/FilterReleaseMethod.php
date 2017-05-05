<?php

class FilterReleaseMethod extends FilterObjectMethod
{
    function __construct( $parmName = 'release' ) {
        $release = getFactory()->getObject('Release');
        $release->setSortDefault(new SortAttributeClause('StartDate.D'));
        parent::__construct($release, $release->getDisplayName(), $parmName);
    }

    function getValues()
    {
        $values = parent::getValues();
        return array_merge(
            array_slice($values, 0, 1),
            array (
                'notpassed' => text(2327)
            ),
            array_slice($values, 1)
        );
    }
}