<?php
include "AutoActionList.php";

class AutoActionTable extends SettingsTableBase
{
    function getList() {
        return new AutoActionList( $this->getObject() );
    }

    function getFilters() {
        return array_merge(
            parent::getFilters(),
            array(
                new FilterObjectMethod(getFactory()->getObject('AutoActionEvent'), translate('Событие'), 'eventType')
            )
        );
    }

    function getFilterPredicates( $values )
    {
        return array_merge(
            parent::getFilterPredicates( $values ),
            array(
                new FilterAttributePredicate('EventType', $values['eventType'])
            )
        );
    }

    function getSortFields()
    {
        return array_diff(
            parent::getSortFields(),
            $this->getObject()->getAttributesByGroup('actions'),
            $this->getObject()->getAttributesByGroup('task')
        );
    }
}
