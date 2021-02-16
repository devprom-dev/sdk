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
 	
 	function getEntityForm() {
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

 	function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array(
                'context_template' => ''
            )
        );
    }
}
