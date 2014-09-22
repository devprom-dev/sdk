<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class WikiParentPathTrigger extends SystemTriggersBase
{
    function add( $object_it )
    { 
        if ( !is_a($object_it->object, 'WikiPage') ) return;
            
        $this->updateParentPath( $object_it );
    }
    
    function modify( $prev_object_it, $object_it ) 
	{
        if ( !is_a($object_it->object, 'WikiPage') ) return;

        if ( $object_it->get('ParentPage') == $prev_object_it->get('ParentPage') ) return;

        $this->updateParentPath( $object_it );
	}

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	}
	
	function updateParentPath( $object_it )
	{
	    global $model_factory;
	    
        $roots = $object_it->getTransitiveRootArray();
        
        $path_value = ','.join(',', array_reverse($roots)).',';

		$sql = "UPDATE WikiPage t SET t.ParentPath = '".$path_value."', DocumentId = ".array_pop($roots)." WHERE t.WikiPageId = ".$object_it->getId();

		DAL::Instance()->Query( $sql );
		
		$sql = 
			"UPDATE WikiPage t ".
			"   SET t.ParentPath = REPLACE(t.ParentPath, '".$object_it->get('ParentPath')."', '".$path_value."') ".
			" WHERE t.ParentPath LIKE '%,".$object_it->getId().",%' AND t.WikiPageId <> ".$object_it->getId();

		DAL::Instance()->Query( $sql );

		$sql = 
			"UPDATE WikiPage t SET t.DocumentId = REPLACE(SUBSTRING_INDEX(t.ParentPath, ',', 2),',','') ".
			" WHERE t.ParentPath LIKE '%,".$object_it->getId().",%' ";

		DAL::Instance()->Query( $sql );
	}
}
