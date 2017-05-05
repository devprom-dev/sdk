<?php

class ChangeLogAccessFilter extends FilterPredicate
{
    static $skipClasses = null;

    function __construct() {
        parent::__construct('-');
    }
    
 	function _predicate( $filter )
 	{
 	    $noaccess = self::getClassesSkipped();
		if ( count($noaccess) > 0 ) {
			return " AND t.ClassName NOT IN ('".join($noaccess, "','")."') AND t.AccessClassName NOT IN ('".join($noaccess, "','")."') ";
		}
 	}

 	static function getClassesSkipped()
    {
        if ( is_array(self::$skipClasses) ) return self::$skipClasses;

        $logged_it = getFactory()->getObject('ChangeLogEntitySet')->getAll();
        while ( !$logged_it->end() )
        {
            $object = getFactory()->getObject($logged_it->get('ClassName'));

            if ( !getFactory()->getAccessPolicy()->can_read($object) ) {
                self::$skipClasses[] = strtolower(get_class($object));
            }

            $logged_it->moveNext();
        }
        return self::$skipClasses;
    }
}
