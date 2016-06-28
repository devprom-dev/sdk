<?php
include "ProjectLogDetailsTable.php";

class ProjectLogDetailsPage extends PMPage
{
	function getObject() {
 		return getFactory()->getObject('ChangeLogAggregated');
	}
    
    function getTable() {
 		return new ProjectLogDetailsTable( $this->getObject() );
 	}
 	
 	function getForm() {
 		return null;
 	}

	function getRecentChangedObjectIds( $filters )
	{
 		 $from_date = SystemDateTime::convertToClientTime(
			 strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 minutes', strtotime(SystemDateTime::date())))
		 );
         return $this->getObject()->getRegistry()->Query(
				array (
						new FilterModifiedAfterPredicate($from_date),
						new FilterVpdPredicate(),
						new SortRecentClause()
				)
			 )->idsToArray();
 	}
}
