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
                )
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
                new FilterAttributePredicate('regionId', $values[REGION_REFNAME])
            )
        );
    }

    public function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);

        if ( !array_key_exists('submittedon', $values) ) {
            $values['submittedon'] = strftime('01.%m.%Y', strtotime(SystemDateTime::date('Y-m-d')));
        }
        if ( !array_key_exists('submittedbefore', $values) ) {
            $values['submittedbefore'] = strftime('%d.%m.%Y',
                strtotime('-1 day',
                    strtotime('+1 month',
                        strtotime(SystemDateTime::date('Y-m-1')))));
        }

        return $values;
    }
}