<?php

class FilterIterationMethod extends FilterObjectMethod
{
    function __construct( $parmName = 'iteration' ) {
        $release = getFactory()->getObject('Iteration');
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