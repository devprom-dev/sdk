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
                new FilterObjectMethod(getFactory()->getObject('AutoActionEvent'), translate('Событие'), 'eventType'),
                new FilterTextWebMethod(text(2508), 'search')
            )
        );
    }

    function getFilterPredicates()
    {
        $values = $this->getFilterValues();
        return array_merge(
            parent::getFilterPredicates(),
            array(
                new FilterSearchAttributesPredicate($values['search'], array('Caption')),
                new FilterAttributePredicate('EventType', $values['eventType'])
            )
        );
    }
}
