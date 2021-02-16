<?php
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
                $this->getCycleStateFilter(),
                $this->buildStartFilter(),
                new ViewFinishDateWebMethod()
            )
        );
    }

    function getFilterPredicates( $values )
	{
	    return array_merge(
	        parent::getFilterPredicates( $values ),
            array(
	            new FilterDateAfterPredicate('MilestoneDate', $values['start']),
                new FilterDateBeforePredicate('MilestoneDate', $values['finish']),
                new MilestoneTimelinePredicate($values['state'])
	        )
        );
	}

    function buildStartFilter() {
        return new FilterDateWebMethod(translate('Начало'), 'start');
    }

    function getCycleStateFilter()
    {
        $filter = new FilterObjectMethod( new CycleState(), '', 'state' );
        $filter->setHasNone(false);
        $filter->setDefaultValue('not-passed');
        $filter->setType( 'singlevalue' );
        $filter->setIdFieldName( 'ReferenceName' );
        return $filter;
    }

    public function buildFilterValuesByDefault( & $filters )
    {
        $values = parent::buildFilterValuesByDefault( $filters );
        if ( $values['start'] == '' ) {
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j'))));
        }
        return $values;
    }
}