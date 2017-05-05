<?php
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include "MilestoneList.php";

class MilestoneTable extends PMPageTable
{
	function getList()
	{
		return new MilestoneList( $this->object );
	}

	function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                $this->buildStartFilter(),
                new ViewFinishDateWebMethod()
            )
        );
    }

    function getFilterPredicates()
	{
	    $values = $this->getFilterValues();
	    
	    return array_merge(
	        parent::getFilterPredicates(),
            array(
	            new FilterDateAfterPredicate('MilestoneDate', $values['start']),
                new FilterDateBeforePredicate('MilestoneDate', $values['finish'])
	        )
        );
	}

    function buildStartFilter() {
        $filter = new ViewStartDateWebMethod();
        $filter->setDefault(getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j')))));
        return $filter;
    }
}