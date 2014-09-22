<?php

include 'ReportSpentTimeList.php';
include 'ReportSpentTimeTable.php';

class ReportSpentTimePage extends PMPage
{
	function getObject()
	{
		return getFactory()->getObject('SpentTime');
	}
	
	function getTable()
    {
        return new ReportSpentTimeTable($this->getObject());
    }

    function getForm()
    {
        return null;
    }
}
