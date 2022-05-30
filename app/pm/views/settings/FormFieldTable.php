<?php

class FormFieldTable extends DictionaryItemsTable
{
    function getNewActions()
    {
        $actions = array();

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() ) {
            $uid = strtolower('new-'.get_class($this->getObject()));
            $filterValues = $this->getFilterValues();
            if ( class_exists($filterValues['fieldentity']) ) {
                $actions[$uid] = array (
                    'name' => translate('Добавить'),
                    'uid' => $uid,
                    'url' => $method->getJSCall(array('Entity' => $filterValues['fieldentity']))
                );
            }
            else {
                $jsCall = $method->getJSCall(
                    array(
                        'Entity' => '%entity%'
                    )
                );
                $jsCall = str_replace("'", "\'", $jsCall);
                $projectIt = getSession()->getProjectIt();
                $actions[$uid] = array (
                    'name' => translate('Добавить'),
                    'uid' => $uid,
                    'url' => "javascript: workflowNewObject(
                            '/pm/{$projectIt->get('CodeName')}/widget/entity','', '', '', {}, function(entity, data) { 
                                eval('{$jsCall}'.replace('%entity%', entity)); });"
                );
            }
        }

        return $actions;
    }

    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                $this->buildEntityFilter()
            )
        );
    }

    protected function buildEntityFilter()
    {
        $filter = new FilterObjectMethod($this->getObject()->getAttributeObject('Entity'),
            translate('Сущность'), 'fieldentity');
        $filter->setHasNone(false);
        return $filter;
    }

    function getFilterPredicates($values)
    {
        return array_merge(
            parent::getFilterPredicates($values),
            array(
                new FilterAttributePredicate('Entity', $values['fieldentity'])
            )
        );
    }
}