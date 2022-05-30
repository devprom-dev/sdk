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
	
 	function getQueryClause(array $parms)
 	{
 	    $query = $this->_getQuery($parms);

 	    if ( $query == '' ) return parent::getQueryClause($parms);
 	    
 	    return "(".$query.")";
 	}
	
	private function _getQuery(array $parms)
 	{
		$queries = array();
		
		$skipped_entities = array();
		
 		if ( in_array('-', $this->getObject()->getVpds()) ) return ' SELECT t.* FROM ObjectChangeLog t WHERE 1 = 2 ';
		
		$base_predicate = $this->getFilterPredicate($this->extractPredicates($parms));

		$query_classes = array();
        $query_predicate = '';

		// simplify the query when the filter by ClassName is required
 		foreach( $this->extractPredicates($parms) as $predicate )
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
			$queries[] = " SELECT t.* FROM ObjectChangeLog t 
                            WHERE t.ClassName IN ('".join("','", $entity)."') ".$base_predicate.$predicate;
		}

		if ( count($query_classes) < 1 ) {
		    // use non-shared entities only if there is no filter by ClassName
    		if ( count($skipped_entities) < 1 ) return '';
    		
    		$queries[] = " SELECT t.* FROM ObjectChangeLog t ".
    				     "	WHERE t.ClassName NOT IN ('".join("','",$skipped_entities)."') ".$base_predicate.$query_predicate;
		}

		return join(" UNION ", $queries);
 	}

    public function QueryById( $ids )
    {
        $ids = \TextUtils::parseIds($ids);
        if ( count($ids) < 1 ) return $this->getObject()->getEmptyIterator();

        $filter = new FilterInPredicate($ids);
        $filter->setAlias('t');
        $filter->setObject( $this->getObject() );
        $queryClause = parent::getQueryClause(array());

        return $this->createSQLIterator(
            "SELECT {$this->getSelectClause($this->extractPersisters(array()), 't')} 
                       FROM {$queryClause} t WHERE 1 = 1 {$filter->getPredicate()}"
        );
    }

    public function Count( $parms = array() )
    {
        $sql = "SELECT {$this->getSelectClause($this->extractPersisters($parms),'t')} 
                  FROM {$this->getQueryClause($parms)} t 
                 WHERE 1 = 1 {$this->getFilterPredicate($this->extractPredicates($parms))}";

        $group = $this->getGroupClause('t');
        if ( $group != '' ) $sql .= ' GROUP BY '.$group;

        return $this->createSQLIterator(
            'SELECT COUNT(1) cnt FROM ('.$sql.') t '
        )->get('cnt');
    }
}