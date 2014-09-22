<?php

class ChangeLogAccessFilter extends FilterPredicate
{
    function __construct()
    {
        parent::__construct('-');
    }
    
 	function _predicate( $filter )
 	{
 	    global $model_factory;
 	    
 	 	$logged_it = $model_factory->getObject('ChangeLogEntitySet')->getAll();
 		
		$noaccess = array();

		while ( !$logged_it->end() )
		{
			$object = $model_factory->getObject($logged_it->get('ClassName'));

			$class_name = strtolower(get_class($logged_it->object));
			
			if ( $class_name != 'changelogentityset' && !getFactory()->getAccessPolicy()->can_read($object) ) $noaccess[] = $class_name;
			
			$logged_it->moveNext();
		}
		
		if ( count($noaccess) > 0 )
		{
			return " AND t.ClassName NOT IN ('".join($noaccess, "','")."') ";
		}
 	}
}
