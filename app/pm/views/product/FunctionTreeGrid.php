<?php

class FunctionTreeGrid extends FunctionList
{
    function getTemplate() {
        return "core/PageTreeGrid.php";
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    str_replace('features/list', 'treegrid/feature', $this->getTable()->getFiltersUrl())
            )
        );
    }

    function buildItemsCount($registry, $predicates)
    {
        return parent::buildItemsCount( $registry,
            array_merge(
                $predicates,
                array(
                    new FeatureRootFilter()
                )
            )
        );
    }
}