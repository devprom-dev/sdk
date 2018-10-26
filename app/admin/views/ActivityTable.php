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
		$filters = array();

		$date = new FilterDateWebMethod();
		$date->setValueParm( 'modified' );
		$date->setCaption( translate('Изменено после') );
		$date->setDefault(
		    getSession()->getLanguage()->getPhpDate(strtotime('-4 weeks', strtotime(SystemDateTime::date('Y-m-j'))))
		);
		
		$filters[] = $date; 
		
		return $filters;
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