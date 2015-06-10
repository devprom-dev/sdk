<?php

include "AdminChangeLogIterator.php";
include "persisters/AdminChangeLogPersister.php";
        
class AdminChangeLog extends Metaobject
{
	function __construct()
	{
		parent::__construct('ObjectChangeLog');
		
 		$this->addSort( new SortAttributeClause('RecordCreated.D') );
 		$this->addSort( new SortAttributeClause('OrderNum.D') );
		
		$this->addAttribute( 'ChangeDate', 'DATE', translate('Дата изменения'), false, false );
		
		$this->addPersister( new AdminChangeLogPersister() );
	}

	function resetFilters()
	{
	    global $model_factory;
	    
	    parent::resetFilters();

        $notificator = new AdminChangeLogNotificator();
	            
        $log = $model_factory->getObject('ObjectChangeLog');
        
		$this->addFilter( new ChangeLogObjectFilter( join(',',$notificator->getEntities()) ) );
	}
	
	function createIterator()
	{
		return new AdminChangeLogIterator( $this );
	}
}
