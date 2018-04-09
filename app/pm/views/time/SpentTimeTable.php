<?php
include "SpentTimeList.php";
include "SpentTimeChart.php";

class SpentTimeTable extends PMPageTable
{
	function getList($mode = '')
    {
        switch ( $this->getReportBase() ) {
            case 'activitieschart':
                return new SpentTimeChart($this->getObject());
            default:
                return new SpentTimeList($this->getObject());
        }
	}

	function getNewActions() {
		return array();
	}
	
 	function getFilterPredicates()
 	{
 	    $values = $this->getFilterValues();
 	    return array(
            new FilterDateAfterPredicate('ReportDate', $values['start']),
            new FilterDateBeforePredicate('ReportDate', $values['finish']),
            new FilterAttributePredicate('Participant', $values['projectuser'])
        );
 	}

 	function getFilters()
    {
        return array(
            $this->buildStartFilter(),
            new ViewFinishDateWebMethod(),
            $this->buildParticipantFilter()
        );
    }

    function buildStartFilter() {
        $filter = new ViewStartDateWebMethod();
        $filter->setDefault(getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j')))));
        return $filter;
    }

    function buildParticipantFilter() {
	    $filter = new FilterObjectMethod(getFactory()->getObject('ProjectUser'));
        $filter->setDefaultValue(getSession()->getUserIt()->getId());
	    return $filter;
    }

 	function getFamilyModules($module)
    {
        return array(
            'activitiesreport'
        );
    }
}
