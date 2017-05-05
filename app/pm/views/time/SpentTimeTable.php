<?php
include "SpentTimeList.php";

class SpentTimeTable extends PMPageTable
{
	function getList() {
		return new SpentTimeList($this->getObject());
	}

	function getNewActions() {
		return array();
	}
	
 	function getFilterPredicates()
 	{
 	    $values = $this->getFilterValues();

 	    $predicates = array(
            new FilterDateAfterPredicate('ReportDate', $values['start']),
            new FilterDateBeforePredicate('ReportDate', $values['finish'])
        );
 	    if ( getSession()->getParticipantIt()->IsLead() ) {
            $predicates[] = new FilterAttributePredicate('Participant', $values['projectuser']);
        }
        else {
            $predicates[] = new FilterAttributePredicate('Participant', getSession()->getUserIt()->getId());
        }

 	    return $predicates;
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
