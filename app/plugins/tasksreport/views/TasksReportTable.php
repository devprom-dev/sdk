<?php
include "TasksReportList.php";

class TasksReportTable extends PMPageTable
{
    function getList($mode = '')
    {
        return new TasksReportList($this->getObject());
    }

    function getNewActions()
    {
        return array();
    }

    function getDeleteActions()
    {
        return array();
    }

    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new ViewSubmmitedAfterDateWebMethod(),
                new ViewSubmmitedBeforeDateWebMethod(),
                new FilterObjectMethod(
                    getFactory()->getObject('TaskType'), '', 'tasktype'
                ),
            	new FilterDateIntervalWebMethod(text(2334), 'spentafter'),
           	    new FilterDateIntervalWebMethod(text(2334), 'spentbefore')
            )
        );
    }

    function getFilterPredicates($values)
    {
        $parentPredicates = parent::getFilterPredicates($values);
        unset($parentPredicates[REGION_REFNAME]);

        return array_merge(
            $parentPredicates,
            array(
                new FilterSubmittedAfterPredicate($values['submittedon']),
                new FilterSubmittedBeforePredicate($values['submittedbefore']),
                new FilterAttributePredicate('TaskType', $values['tasktype']),
                new FilterAttributePredicate('regionId', $values[REGION_REFNAME]),
		        new SpentTimeReportDatePredicate($values['spentafter'], $values['spentbefore'])
            )
        );
    }

    public function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);

        if ( !array_key_exists('spentafter', $values) ) {
            $values['spentafter'] = strftime('01.%m.%Y', strtotime(SystemDateTime::date('Y-m-d')));
        }
        if ( !array_key_exists('spentbefore', $values) ) {
            $values['spentbefore'] = strftime('%d.%m.%Y',
                strtotime('-1 day',
                    strtotime('+1 month',
                        strtotime(SystemDateTime::date('Y-m-1')))));
        }

        return $values;
    }
}