<?php

include "ActivityList.php";

class ActivityTable extends PageTable
{
	function getList()
	{
		return new ActivityList( $this->getObject() );
	}

	function getSortDefault($sort_parm = 'sort')
    {
        return 'RecordModified.D';
    }

    function getFilters()
	{
		$filters = parent::getFilters();

		$date = new FilterDateWebMethod();
		$date->setValueParm( 'modified' );
		$date->setCaption( translate('Изменено после') );

		$filters[] = $date; 
		
		return $filters;
	}

	function getFilterPredicates($values)
    {
        return array_merge(
            parent::getFilterPredicates($values),
            array(
                new FilterModifiedAfterPredicate($values['modified'])
            )
        );
    }

    function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);
        if ( !array_key_exists('modified', $values) ) {
            $values['modified'] = getSession()->getLanguage()
                ->getPhpDate(strtotime('-4 weeks', strtotime(SystemDateTime::date('Y-m-j'))));
        }
        return $values;
    }

    function getNewActions()
	{
	    return array();
	}
	
	function IsNeedToDelete() { return false; }
	
 	function getDefaultRowsOnPage() {
		return 20;
	}

	function getCaption()
    {
        return text(2624);
    }
}