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
        $object = getFactory()->getObject('ChangeLogAggregated');
        $object->removeAttribute('EntityName');
 		return $object;
	}
    
    function getTable() 
 	{
 		return new ProjectLogTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getRecentChangedObjectIds( $filters )
 	{
         return $this->getObject()->getRegistry()->Query(
         		array (
                    new FilterModifiedSinceSecondsPredicate(5 * 60),
         				new FilterVpdPredicate(),
         				new SortChangeLogRecentClause()
         		)
	         )->idsToArray();
 	}
}
