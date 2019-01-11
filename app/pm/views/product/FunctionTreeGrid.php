<?php

class FunctionTreeGrid extends FunctionList
{
    private $roots = array();

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

        $this->roots = $this->getObject()->getRegistryBase()->Query(
                array_merge(
                    $predicates,
                    array(
                        new FeatureHierarchyPersister()
                    )
                )
            )->fieldToArray('RootId');

        return parent::buildIterator();
    }

    function getPredicates( $filters )
    {
        return array_merge(
            PMPageList::getPredicates($filters),
            array(
                new FilterInPredicate($this->roots)
            )
        );
    }

    function getRenderParms()
    {
        $it = $this->getIteratorRef();
        $this->shiftNextPage($it, $this->getOffset());

        return array_merge(
            parent::getRenderParms(),
            array(
                'jsonUrl' =>
                    str_replace('features/list', 'treegrid/feature', $this->getTable()->getFiltersUrl())
                        .'&rows=all&roots='.\TextUtils::buildIds($it->idsToArray())
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