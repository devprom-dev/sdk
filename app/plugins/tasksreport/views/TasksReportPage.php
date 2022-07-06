<?php
include "TasksReportTable.php";
include "TasksReportPageSettingsBuilder.php";

class TasksReportPage extends PMPage
{
    function __construct() {
        getSession()->addBuilder(new TasksReportPageSettingsBuilder() );
        parent::__construct();
    }

    function getObject() {
        $object = new Task(new TasksReportRegistry());
        $object->addAttribute('FactPeriod', 'FLOAT', 'Затрачено', true);
        $object->addAttribute('StartDateOnly', 'DATE', 'Дата списания', true);
        $object->addAttribute('LastDate', 'DATE', 'Дата последней активности', true);
        $object->addAttribute('DayFact', 'FLOAT', 'Затраченное время', true);
        $object->addAttribute('regionCaption', 'VARCHAR', 'Регион', true);
        $object->addAttribute('regionId', 'INTEGER', 'Регион ИД', false);
        $object->addAttribute('FactRegion', 'FLOAT', 'Затраченно по региону', true);
        return $object;
    }

 	function getTable() {
 		return new TasksReportTable($this->getObject());
 	}

    function buildExportIterator( $object, $ids, $iteratorClassName, $queryParms )
    {
        $ids = array_filter($ids, function($value) {
            return $value != '';
        });
        if ( count($ids) < 1 ) return $object->getEmptyIterator();

        return $object->getRegistry()->Query(
            array_merge(
                array(
                    new TasksReportActivityPredicate($ids)
                ),
                $queryParms
            )
        );
    }
}
