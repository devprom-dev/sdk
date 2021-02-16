<?php
include "persisters/ChangeLogDetailsPersister.php";

class ChangeLogRegistry extends ObjectRegistrySQL
{
    public function getPersisters() {
        return array_merge(
            array (
                new ChangeLogDetailsPersister()
            ),
            parent::getPersisters()
        );
    }

	function getFilters()
	{
		return array_merge(
            parent::getFilters(),
            array (
                new ChangeLogAccessFilter()
            )
		);
	}
	
 	function getQueryClause()
 	{
 	    $query = $this->_getQuery();

 	    if ( $query == '' ) return parent::getQueryClause(); 
 	    
 	    return "(".$query.")";
 	}
	
	private function _getQuery()
 	{
		$queries = array();
		
		$skipped_entities = array();
		
 		if ( in_array('-', $this->getObject()->getVpds()) ) return ' SELECT t.* FROM ObjectChangeLog t WHERE 1 = 2 ';
		
		$base_predicate = $this->getObject()->getVpdPredicate('t');

		$query_classes = array();
        $query_predicate = '';

		// simplify the query when the filter by ClassName is required
 		foreach( $this->getFilters() as $predicate )
 		{
 			$predicate->setObject( $this->getObject() );
 			
 		    if ( is_a($predicate, 'ChangeLogObjectFilter') ) {
 		        $query_classes = array_diff(\TextUtils::parseItems($predicate->getValue()), array('any','all','hide'));

 		        array_walk($query_classes, function(&$value, $key) {
                    if ( class_exists(getFactory()->getClass($value)) ) {
                        $value = strtolower(get_class(getFactory()->getObject($value)));
                    }
                    else {
                        $value = '';
                    }
 		        });
 		    }
 		    else if ( $predicate instanceof ChangeLogItemFilter or $predicate instanceof ChangeLogItemDateFilter ) {
 		        return ""; 
 		    }
 		    else {
 		        $query_predicate .= $predicate->getPredicate();
 		    }
 		}
		
 		$include_classes = array();

 		$className = getFactory()->getClass('SharedObjectSet');
 		if ( class_exists($className) ) {
            $shareable_it = getFactory()->getObject($className)->getAll();
            while( !$shareable_it->end() ) {
                if ( count($query_classes) > 0 && !in_array($shareable_it->get('ClassName'), $query_classes) ) {
                    $shareable_it->moveNext();
                    continue;
                }

                $class_name = getFactory()->getClass($shareable_it->get('ClassName'));
                if ( !class_exists($class_name) )
                {
                    $shareable_it->moveNext();
                    continue;
                }

                $object = getFactory()->getObject($class_name);
                $entity = strtolower(get_class($object));

                $predicate = $object->getVpdPredicate('t');
                if ( $predicate == '' || $base_predicate == $predicate ) {
                    $shareable_it->moveNext();
                    continue;
                }

                $include_classes[$predicate.$query_predicate][] = $entity;
                $skipped_entities[] = $entity;
                $shareable_it->moveNext();
            }
        }

		foreach( $include_classes as $predicate => $entity ) {
			$queries[] = " SELECT t.* FROM ObjectChangeLog t WHERE t.ClassName IN ('".join("','", $entity)."') ".$predicate;
		}

		if ( count($query_classes) < 1 ) {
		    // use non-shared entities only if there is no filter by ClassName
    		if ( count($skipped_entities) < 1 ) return '';
    		
    		$queries[] = " SELECT t.* FROM ObjectChangeLog t ".
    				     "	WHERE t.ClassName NOT IN ('".join("','",$skipped_entities)."') ".$base_predicate.$query_predicate;
		}

		return join(" UNION ", $queries);
 	}
}