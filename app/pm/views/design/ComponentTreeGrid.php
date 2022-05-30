<?php

class ComponentTreeGrid extends ComponentList
{
    use PageTreeTrait;

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    str_replace('components/list', 'components/tree', $this->getTable()->getFiltersUrl())
            )
        );
    }

    function buildItemsCount($registry, $predicates)
    {
        return parent::buildItemsCount( $registry,
            array_merge(
                $predicates,
                array(
                    new ObjectRootFilter()
                )
            )
        );
    }
}