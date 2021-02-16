<?php

class FilterReleaseMethod extends FilterObjectMethod
{
    function __construct( $parmName = 'release' ) {
        parent::__construct(getFactory()->getObject('ReleaseRecent'), '', $parmName);
    }

    function getValues()
    {
        $values = parent::getValues();
        return array_merge(
            array_slice($values, 0, 1),
            array (
                'notpassed' => text(2327),
                'current' => translate('Текущий')
            ),
            array_slice($values, 1)
        );
    }

    function parseFilterValue($value)
    {
        $value = preg_replace_callback('/notpassed/i', function() {
                return join(',',getFactory()->getObject('ReleaseActual')->getAll()->idsToArray());
            }, $value);

        $value = preg_replace_callback('/current/i', function() {
                    return join(',',getFactory()->getObject('Release')->getRegistry()->Query(array(
                        new FilterVpdPredicate(),
                        new ReleaseTimelinePredicate('current')
                    ))->idsToArray()
                );
            }, $value);

        return $value;
    }
}