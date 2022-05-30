<?php
include "ProjectLogTable.php";
include "ProjectLogPageSettingsBuilder.php";

class ProjectLogPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        getSession()->addBuilder( new ProjectLogPageSettingsBuilder() );
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
 	
 	function getEntityForm()
 	{
 		return null;
 	}
 	
 	function getRecentChangedObjectIds( $filters )
 	{
 	    $registry = $this->getObject()->getRegistry();
        $registry->setSorts(array());
        return $registry->QueryKeys(
         		array (
                    new FilterCreatedSinceSecondsPredicate(5 * 60),
                    new FilterVpdPredicate(),
                    new SortKeyClause('DESC')
         		)
	         )->idsToArray();
 	}
}
