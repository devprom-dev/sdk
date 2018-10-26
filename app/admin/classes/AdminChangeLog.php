<?php

include "AdminChangeLogIterator.php";
include "persisters/AdminChangeLogPersister.php";
        
class AdminChangeLog extends Metaobject
{
	function __construct()
	{
		parent::__construct('ObjectChangeLog');

		$this->addAttribute( 'ChangeDate', 'DATE', translate('Дата'), false, false );
        $this->addPersister( new AdminChangeLogPersister() );

        $this->setSortDefault(
            array(
                new SortAttributeClause('RecordModified.D'),
                new SortAttributeClause('OrderNum.D')
            )
        );
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
