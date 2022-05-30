<?php

class FilterIterationMethod extends FilterObjectMethod
{
    function __construct( $parmName = 'iteration' ) {
        parent::__construct(getFactory()->getObject('IterationRecent'), '', $parmName);
        $this->setLazyLoad(true);
    }

    function getValues()
    {
        $values = parent::getValues();
        return array_merge(
            array_slice($values, 0, 1),
            $this->getHasAll()
                ? array (
                        'notpassed' => text(2327),
                        'current' => translate('Текущая')
                    )
                : array(),
            array_slice($values, 1)
        );
    }

    function parseFilterValue($value, $context)
    {
        $value = preg_replace_callback('/notpassed/i', function() {
                return join(',',getFactory()->getObject('IterationActual')->getAll()->idsToArray());
            }, $value);

        $value = preg_replace_callback('/current/i', function() {
                return join(',',getFactory()->getObject('Iteration')->getRegistry()->QueryKeys(array(
                            new FilterVpdPredicate(),
                            new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT)
                        ))->idsToArray()
                    );
            }, $value);

        return $value;
    }
}