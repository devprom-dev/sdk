<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once "ObjectRegistry.php";

class ObjectRegistrySQL extends ObjectRegistry
{
	protected $persisters = array();
	protected $filters = array();
	protected $groups = array();
	protected $sorts = array();
	protected $limit = '';
    protected $offset = 0;
	protected $default_sort = '';

	public function __construct( $object = null, array $persisters = null, array $filters = null, array $sorts = null, array $groups = null )
	{
		parent::__construct( $object );
		
		if ( is_array($persisters) ) $this->setPersisters($persisters);
		if ( is_array($filters) ) $this->setFilters($filters);
		if ( is_array($groups) ) $this->setGroups($groups);
		if ( is_array($sorts) ) $this->setSorts($sorts);
	}
	
	public function setPersisters( $persisters ) {
		$this->persisters = $persisters;
	}
	
	public function getPersisters()	{
		return $this->persisters;
	}

	public function useImportantPersistersOnly() {
	    foreach( $this->persisters as $key => $persister ) {
	        if ( !$persister->IsPersisterImportant() ) {
	            unset($this->persisters[$key]);
            }
        }
	    return $this;
    }
	
	public function setFilters( $filters ) {
		$this->filters = $filters;
	}
	
	public function getFilters() {
		return $this->filters;
	}
	
	public function setGroups( $groups ) {
		$this->groups = $groups;
	}
	
	public function getGroups()	{
		return $this->groups;
	}
	
	public function setSorts( $sorts ) {
		$this->sorts = $sorts;
	}

	public function addSort( $sort ) {
		$this->sorts[] = $sort;
	}
	
	public function getSorts() {
		return $this->sorts;
	}
	
	public function setLimit( $limit ) {
		$this->limit = $limit;
	}
	
	public function getLimit() {
		return $this->limit;
	}

	public function setOffset( $value ) {
        $this->offset = $value;
    }

    public function getOffset() {
        return $this->offset;
    }
	
	public function setDefaultSort( $sort_clause ) {
		$this->default_sort = $sort_clause;
	}
	
	protected function setParameters( $parms )
	{
		$filters = array();
		$sorts = array();
		$persisters = array();
		
		foreach ( $parms as $parameter )
		{
			if ( is_a($parameter, 'FilterPredicate') ) $filters[] = $parameter; 
			if ( is_a($parameter, 'SortClauseBase') ) $sorts[] = $parameter;
			if ( is_a($parameter, 'ObjectSQLPersister') ) $persisters[] = $parameter;
		}
		
		$this->setFilters($filters);
		
		if ( count($sorts) > 0 ) {
			$this->setSorts($sorts);
		}
		else {
			$this->setSorts($this->getObject()->getSortDefault());
		}

		if ( count($persisters) > 0 ) {
			$this->setPersisters(array_merge($this->getPersisters(), $persisters));
		}
	}

    public function QueryById( $ids )
    {
        $ids = \TextUtils::parseItems($ids);
        if ( count($ids) < 1 ) return $this->getObject()->getEmptyIterator();

        if ( $this->getObject() instanceof MetaobjectCacheable ) {
            return $this->getObject()->getExact($ids);
        }

        return $this->Query( array(
           new FilterInPredicate($ids)
        ));
    }

	public function Query( $parms = array() )
	{
	    if ( !is_array($parms) ) throw new Exception("parms should be array");
		$this->setParameters( $parms );
		return $this->getAll();
	}
	
	public function getAll()
	{
        foreach( $this->getFilters() as $predicate ) {
            if ( $predicate instanceof FilterEmptyPredicate ) return $this->getObject()->getEmptyIterator();
        }

		$sql = 'SELECT '.$this->getSelectClause('t').' FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate();

		$group = $this->getGroupClause('t');

		if ( $group != '' ) $sql .= ' GROUP BY '.$group;
		
		$sort = $this->getSortClause('t');
		
		if ( $sort != '' ) $sql .= ' ORDER BY '.$sort;

		$sql .= $this->getLimitClause();
        $sql .= $this->getOffsetClause();

		return $this->createSQLIterator($sql);
	}
	
	public function Count( $parms = array() )
	{
		$this->setParameters( $parms );
        foreach( $this->getFilters() as $predicate ) {
            if ( $predicate instanceof FilterEmptyPredicate ) return 0;
        }
		return $this->createSQLIterator(
				'SELECT COUNT(1) cnt FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate()
			)->get('cnt');
	}

    public function CountBy( $attribute, $parms = array() )
    {
        if ( !preg_match('/[A-Za-z0-1_]/', $attribute) ) {
            return $this->getObject()->getEmptyIterator();
        }
        $this->setParameters( $parms );
        foreach( $this->getFilters() as $predicate ) {
            if ( $predicate instanceof FilterEmptyPredicate ) return 0;
        }
        return $this->createSQLIterator(
            'SELECT t.'.$attribute.', COUNT(1) cnt FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate()." GROUP BY ".$attribute
            );
    }

	public function createSQLIterator( $sql_query )
	{
		$class_name = $this->getObject()->getEntityRefName();
		$cached_iterator = getFactory()->getCachedIterator( $class_name, $sql_query );
	
		if ( is_object($cached_iterator) )
		{
			$cached_iterator->resetStop();
			return $this->createIterator($cached_iterator->getRowset());
		}
		
		$this->checkSelectOnly($sql_query);
		
		$r2 = DAL::Instance()->Query($sql_query);

   		if ( $r2 !== false ) {
    		$iterator = $this->createIterator( $r2 );
            getFactory()->cacheIterator( $class_name, $sql_query, $iterator );
   		}
   		else {
   			$iterator = $this->createIterator( array() );
   		}
		
		return $iterator;
	}
	
	// to be protected
	public function getFilterPredicate( $alias = 't' )
	{
		$predicate = '';

		foreach( $this->getFilters() as $filter ) {
			$filter->setAlias($alias);
			$filter->setObject( $this->getObject() );
			$predicate .= $filter->getPredicate();
		}
		return $predicate;
	}
	
	// to be protected
	public function getQueryClause()
	{
	    return $this->getObject()->getEntityRefName();
	}
	
	// to be protected
	public function getSelectClause( $alias, $select_all = true )
	{
		if( $select_all )
		{
			$select_columns = array( $alias != '' ? $alias.".*" : "*" );
		}
		else
		{
			$select_columns = array();
		}

		foreach( $this->getPersisters() as $persister )
		{
			$persister->setObject( $this->getObject() );
			
			if ( !is_a( $persister, 'ObjectSQLPersister') ) continue;
			
			$persister->setObject( $this->getObject() );
			
			$select_columns = array_merge( $select_columns, $persister->getSelectColumns( $alias )); 
		}
		
		return join(' , ', $select_columns);
	}

	// to be protected
	public function getGroupClause( $alias = 't' )
	{
		$items = array();
		
		foreach ( $this->getGroups() as $group )
		{
			$group->setObject( $this->getObject() );
			
			$group->setAlias( $alias );
			
			$clause = $group->clause();
			
			if ( $clause != '' ) $items[] = $clause; 
		}
		
		return join($items, ', ');
	}
	
	// to be protected
	public function getSortClause( $alias = 't' )
	{
		$items = array();
		
		foreach ( $this->getSorts() as $sort )
		{
			$sort->setObject( $this->getObject() );
			
			$sort->setAlias( $alias );
			
			$clause = $sort->clause();
			
			if ( $clause != '' ) $items[] = $clause; 
		}
		
		return count($items) > 0 ? join($items, ', ') : $this->default_sort;
	}
	
	// to be protected
	public function getLimitClause()
	{
		if ( !is_numeric( $this->getLimit() ) ) return;
		if ( $this->getLimit() > 0 ) return ' LIMIT '.$this->getLimit();
	}

	public function getOffsetClause()
    {
        if ( !is_numeric( $this->getOffset() ) ) return;
        if ( $this->getOffset() > 0 ) return ' OFFSET '.$this->getOffset();
    }
	
	public function Store( OrderedIterator $object_it, array $data )
	{
		$sql = "";
		
		$object = $this->getObject();
        $data[$object->getIdAttribute()] = $object_it->getId();

		if ( $data['RecordModified'] == '' ) {
			$data['RecordModified'] = SystemDateTime::date();
		}

		$pre_sql = "UPDATE ".$object->getEntityRefName()." SET RecordModified = ".
		   ($data['RecordModified'] != '' ? "'".DAL::Instance()->Escape($data['RecordModified'])."'" : "NOW()").", ";

		foreach( $object->getAttributes() as $key => $attribute ) 
		{
			if ( $key == 'RecordModified' || $key == 'RecordCreated' ) continue;
			
			if ( in_array($object->getAttributeType($key), array('file', 'image')) ) continue; 
			
			if( !$object->IsAttributeStored($key) ) {
			    getFactory()->info( 'Skip non-storable attribute: '.$key );
			    continue;
			}

			if ( $object->getAttributeType($key) == 'char' && 
			   !array_key_exists($key, $data) && $data[$key.'OnForm'] != '') 
			{
				$data[$key] = 'N';
			}
			
			if( !array_key_exists($key, $data) && $object->isAttributeRequired($key) && $object_it->get($key) == '') 
			{
				$value = $object->getDefaultAttributeValue($key); 
			} 
			else 
			{
				$value = $data[$key];
			}

			if( !array_key_exists($key, $data) ) continue; 
			
			$sql .= '`'.$key.'` = '.$object->formatValueForDB($key, $value).',';
		}

		if ( $data['VPD'] != '' ) $sql .= "`VPD` = '".DAL::Instance()->Escape($data['VPD'])."',";

		getFactory()->info( JsonWrapper::encode($data) );

		if ( $sql != '' )
		{
			if ( $data['WasRecordVersion'] != '' )
			{
				$pre_sql .= "RecordVersion = RecordVersion + 1, ";
				$data['RecordVersion'] = DAL::Instance()->Escape($data['WasRecordVersion']);
			}
			
			$sql = $pre_sql.$sql;
			
			$sql = substr($sql, 0, strlen($sql) - 1)." WHERE ".
				$object->getEntityRefName()."Id IN (".join(',', preg_split('/,/', $object_it->getId())).")";
	
			if ( $data['RecordVersion'] != '' ) $sql .= " AND RecordVersion = ".$data['RecordVersion'];
			
			$this->checkUpdateOnly($sql);

			$r2 = DAL::Instance()->Query($sql);

			if ( $data['RecordVersion'] != '' && DAL::Instance()->GetAffectedRows() < 1 ) {
			    throw new \Exception(text(612));
            }

		    getFactory()->resetCachedIterator($object);
		}
		else
		{
			getFactory()->debug( 'SQL query to update attributes is empty: '.$pre_sql );
		}

		foreach ( $this->persisters as $persister ) {
			$persister->modify( $object_it->getId(), $data );
		}

		getFactory()->resetCachedIterator($object);
		$resultIt = $object->getExact($object_it->getId());

        if ( $object->getNotificationEnabled() ) {
            getFactory()->getEventsManager()->notify_object_modify($object_it->copy(), $resultIt, $data);
        }

        return $resultIt;
	}

    public function Create( array $data )
    {
        $objectId = $this->getObject()->add_parms($data);
        if ( $objectId < 1 ) return $this->getObject()->getEmptyIterator();
        return $this->Query(
            array (
                new FilterInPredicate($objectId)
            )
        );
    }

    public function Merge( array $data, array $alternativeKey = array() )
    {
        $parms = array();
        if ( count($alternativeKey) < 1 ) $alternativeKey = array_keys($data);

        foreach( $alternativeKey as $attribute ) {
            $parms[] = new FilterAttributePredicate($attribute, $data[$attribute]);
        }
        if ( $this->getObject()->getVpdValue() != '' ) {
            $parms[] = new FilterBaseVpdPredicate();
        }

        $object_it = $this->Query($parms);
        if ( $object_it->getId() != '' ) {
            return $this->Store($object_it, $data);
        }
        return $this->Create($data);
    }

    public function Delete( OrderedIterator $object_it )
    {
        $this->getObject()->delete($object_it->getId());
    }

	protected function checkSelectOnly( $sql )
	{
	}
	
	protected function checkUpdateOnly( $sql )
	{
	}

	public function __sleep()
	{
		$attributes = parent::__sleep();
		unset($this->persisters);
		$this->persisters = array();
		unset($this->filters);
		$this->filters = array();
		unset($this->groups);
		$this->groups = array();
		unset($this->sorts);
		$this->sorts = array();
		return array_merge($attributes, array());
	}
	
	public function __destruct()
	{
		parent::__destruct();
		unset($this->persisters);
		$this->persisters = array();
		unset($this->filters);
		$this->filters = array();
		unset($this->groups);
		$this->groups = array();
		unset($this->sorts);
		$this->sorts = array();
	}
}