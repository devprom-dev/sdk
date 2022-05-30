<?php
include "WhatsNewPageTable.php";

class WhatsNewPage extends PMPage
{
	function getObject() {
	    return getFactory()->getObject('ChangeLogWhatsNew');
	}
    
    function getTable() {
 		return new WhatsNewPageTable( $this->getObject() );
 	}
 	
 	function getEntityForm() {
 		return null;
 	}
 	
 	function getRecentChangedObjectIds( $filters )
 	{
         return $this->getObject()->getRegistry()->QueryKeys(
         		array (
                    new FilterCreatedSinceSecondsPredicate(5 * 60),
                    new FilterVpdPredicate(),
                    new SortKeyClause('DESC')
         		)
	         )->idsToArray();
 	}
}
