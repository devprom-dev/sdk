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
}
