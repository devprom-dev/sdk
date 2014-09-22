<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
include_once SERVER_ROOT_PATH.'pm/classes/wiki/predicates/WikiSameBranchFilter.php';

class WikiSectionNumberingTrigger extends SystemTriggersBase
{
    function add( $object_it )
    { 
        if ( !is_a($object_it->object, 'WikiPage') ) return;
            
        $this->update( $object_it );	
    }
    
    function delete( $object_it )
    {
        if ( !is_a($object_it->object, 'WikiPage') ) return;

        // when the page is deleted then all neigbours should be updated
        $this->update( $object_it );
    }
    
    function modify( $prev_object_it, $object_it ) 
	{
        if ( !is_a($object_it->object, 'WikiPage') ) return;

	    $b_skip = $object_it->get('OrderNum') == $prev_object_it->get('OrderNum')
	        && $object_it->get('ParentPage') == $prev_object_it->get('ParentPage');
	      
	    if ( $b_skip ) return;

        $this->update( $object_it );
	}

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	}
	
	function update( $object_it )
	{
        $this->updateSiblingsOrderNum( $object_it );
		$this->updateSortIndex( $object_it );
		$this->updateSectionNumber( $object_it );
	}
	
	function updateSortIndex( $object_it )
	{
		$parent_id = $object_it->get('ParentPage') != '' ? $object_it->get('ParentPage') : $object_it->getId();
		
		$sql = " CREATE TEMPORARY TABLE tmp_WikiPageSort (WikiPageId INTEGER, SortIndex VARCHAR(32767) ) ENGINE=MEMORY DEFAULT CHARSET=cp1251 AS ".
			   " SELECT t.WikiPageId, ".
			   "        (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) ".
 		       "    	   FROM WikiPage u WHERE t.ParentPath LIKE CONCAT('%,',u.WikiPageId,',%')) SortIndex ".
			   "   FROM WikiPage t ".
			   "  WHERE t.ParentPath LIKE '%,".$parent_id.",%' ";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
				
		DAL::Instance()->Query( $sql );
		
		$sql = " UPDATE WikiPage t SET t.SortIndex = (SELECT u.SortIndex FROM tmp_WikiPageSort u WHERE u.WikiPageId = t.WikiPageId) ".
			   "  WHERE t.ParentPath LIKE '%,".$parent_id.",%' ";

		DAL::Instance()->Query( $sql );
		
		DAL::Instance()->Query( "DROP TABLE tmp_WikiPageSort" );

        $className = get_class($object_it->object);

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
            " SELECT NOW(), NOW(), w.VPD, w.WikiPageId, '" . $className . "' ".
            "     FROM WikiPage w WHERE w.ParentPath LIKE '%,".$parent_id.",%' AND ParentPage <> ".$parent_id;

        DAL::Instance()->Query( $sql );

	}
	
	function updateSectionNumber( $object_it )
	{
		global $model_factory;
		
		if ( $object_it->get('ParentPage') != '' )
	    {
	    	$model_factory->resetCachedIterator($object_it->object);
	    	
			DAL::Instance()->Query( "SET @r=0 " );
			
	    	$sql = "UPDATE WikiPage t SET t.SectionNumber = CONCAT('".$object_it->getRef('ParentPage')->get('SectionNumber')."', '.', (@r:= (@r+1))) ".
	    		   " WHERE t.ParentPage = ".$object_it->get('ParentPage')." ORDER BY t.OrderNum";

    		DAL::Instance()->Query( $sql );
    		
    		$parent_id = $object_it->get('ParentPage');
	    }
	    else
	    {
	    	$sql = "UPDATE WikiPage t SET t.SectionNumber = '1' WHERE t.WikiPageId = ".$object_it->getId();

    		DAL::Instance()->Query( $sql );
    		
    		$parent_id = $object_it->getId();
	    }
	    
    	// get first children of my neighbours
   		$sql = "SELECT (SELECT c.WikiPageId FROM WikiPage c WHERE c.ParentPage = t.WikiPageId LIMIT 1) WikiPageId, ".
   			   "       t.WikiPageId ParentPage ".
   			   "  FROM WikiPage t WHERE t.ParentPage = ".$parent_id;
    		
    	$children_it = $object_it->object->getRegistry()->createSQLIterator($sql);
			
		while( !$children_it->end() )
		{
			if ( $children_it->getId() > 0 ) $this->updateSectionNumber( $children_it );
				
			$children_it->moveNext();
		}
	}

    private function updateSiblingsOrderNum($object_it)
    {
        global $model_factory;

        $className = get_class($object_it->object);
        $object = $model_factory->getObject($className);

        $object->addSort( new SortOrderedClause() );

        $object->addFilter( new WikiSameBranchFilter($object_it));
        $object->addFilter( new FilterNextSiblingsPredicate($object_it) );

        $seq_it = $object->getAll();

        if ( $seq_it->count() < 1 ) return;

        $sql = "SET @r=".$object_it->get('OrderNum');

        DAL::Instance()->Query( $sql );

        $sql = "UPDATE WikiPage w SET w.OrderNum = @r:= (@r+10), w.RecordModified = NOW() WHERE w.WikiPageId IN (".join(",", $seq_it->idsToArray()).") ORDER BY w.OrderNum ASC";

        DAL::Instance()->Query( $sql );

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
            " SELECT NOW(), NOW(), w.VPD, w.WikiPageId, '" . $className . "' ".
            "     FROM WikiPage w WHERE w.WikiPageId IN (".join(",", $seq_it->idsToArray()).") ";

        DAL::Instance()->Query( $sql );
}
}
