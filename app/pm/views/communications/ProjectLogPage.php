<?php

include "ProjectLogTable.php";
include "ProjectLogPageSettingsBuilder.php";

class ProjectLogPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        
        getSession()->addBuilder( new ProjectLogPageSettingsBuilder() );

		$this->addInfoSection( new FullScreenSection() );
    }
    
	function getObject()
	{
 		return getFactory()->getObject('ChangeLogAggregated');
	}
    
    function getTable() 
 	{
 		return new ProjectLogTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getRecentChangedObjectIds( $table )
 	{
 		 $from_date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 minutes', strtotime(SystemDateTime::date())));
 		
         $ids = $this->getObject()->getRegistry()->Query(
         		array (
         				new FilterModifiedAfterPredicate($from_date),
         				new FilterVpdPredicate(),
         				new SortRecentClause()
         		)
         )->idsToArray();
		 
		 return $ids;
 	}
}
