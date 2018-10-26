<?php

class FunctionTreeGrid extends PMPageList
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

    function getMaxOnPage() {
        return 0;
    }

    function IsNeedNavigator() {
        return false;
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