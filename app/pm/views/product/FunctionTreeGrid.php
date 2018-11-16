<?php

class FunctionTreeGrid extends FunctionList
{
    function getTemplate() {
        return "core/PageTreeGrid.php";
    }

    function getGroupFields() {
        return array();
    }

    function getGroup() {
        return '';
    }

    function buildIterator()
    {
        $filters = $this->getFilterValues();

        $predicates = array_merge(
            $this->getPredicates( $filters ),
            $this->getObject()->getFilters()
        );
        $predicates[] = new FilterVpdPredicate();

        $ids = $this->getIds();
        if ( count($ids) > 0 ) {
            $predicates[] = new FilterInPredicate($ids);
        }

        $roots = $this->getObject()->getRegistryBase()->Query(
            array_merge(
                $predicates,
                array(
                    new FeatureHierarchyPersister()
                )
            )
        )->fieldToArray('RootId');

        $this->getTable()->setFilterValue('roots', \TextUtils::buildIds($roots));

        return parent::buildIterator();
    }

    function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    str_replace('features/list', 'treegrid/feature', $this->getTable()->getFiltersUrl())
                        .'&parent='.\TextUtils::buildIds($this->getIteratorRef()->idsToArray())
            )
        );
    }

    function getColumnFields()
    {
        return array_merge(
            parent::getColumnFields(),
            array(
                'OrderNum'
            )
        );
    }
}