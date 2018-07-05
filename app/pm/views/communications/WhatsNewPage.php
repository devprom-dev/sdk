<?php
include "WhatsNewPageTable.php";

class WhatsNewPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
    }
    
	function getObject()
	{
 		return getFactory()->getObject('ChangeLogWhatsNew');
	}
    
    function getTable() 
 	{
 		return new WhatsNewPageTable( $this->getObject() );
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
