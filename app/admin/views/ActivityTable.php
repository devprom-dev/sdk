<?php

include "ActivityList.php";

class ActivityTable extends PageTable
{
    function __construct()
    {
        global $model_factory;
        
        parent::__construct( $model_factory->getObject('AdminChangeLog') );
        
    	if ( $_REQUEST['modified'] == '' )
		{
		    $language = getLanguage();
		    
		    $_REQUEST['modified'] = $language->getPhpDate( 
		            strtotime('-1 month', strtotime(date('Y-m-j'))) );
		}
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
		
		$filters[] = $date; 
		
		return $filters;
	}
	
	function getNewActions()
	{
	    return array();
	}
	
	function IsNeedToDelete() { return false; }
	
 	function getDefaultRowsOnPage()
	{
		return 20;
	}
} 