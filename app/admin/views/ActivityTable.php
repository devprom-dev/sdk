<?php

include "ActivityList.php";

class ActivityTable extends PageTable
{
    function __construct()
    {
        parent::__construct( getFactory()->getObject('AdminChangeLog') );
    }
	
	function getList()
	{
		return new ActivityList( $this->object );
	}

	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'ChangeDate.D';
		}
		
		if ( $sort_parm == 'sort2' )
		{
			return 'RecordModified.D';
		}
		
		return parent::getSortDefault( $sort_parm );
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
} 