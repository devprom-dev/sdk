<?php

include_once "ObjectRegistry.php";

class ObjectRegistrySQL extends ObjectRegistry
{
	protected $persisters = array();
	protected $filters = array();
	protected $groups = array();
	protected $sorts = array();
	protected $limit = '';
	protected $default_sort = '';

	public function __construct( Object $object = null, array $persisters = null, array $filters = null, array $sorts = null, array $groups = null )
	{
		parent::__construct( $object );
		
		if ( is_array($persisters) ) $this->setPersisters($persisters);
		
		if ( is_array($filters) ) $this->setFilters($filters);
		
		if ( is_array($groups) ) $this->setGroups($groups);
		
		if ( is_array($sorts) ) $this->setSorts($sorts);
	}
	
	public function setPersisters( $persisters )
	{
		$this->persisters = $persisters;
	}
	
	public function & getPersisters()
	{
		return $this->persisters;
	}
	
	public function setFilters( $filters )
	{
		$this->filters = $filters;
	}
	
	public function & getFilters()
	{
		return $this->filters;
	}
	
	public function setGroups( $groups )
	{
		$this->groups = $groups;
	}
	
	public function & getGroups()
	{
		return $this->groups;
	}
	
	public function setSorts( $sorts )
	{
		$this->sorts = $sorts;
	}
	
	public function addSort( $sort )
	{
		$this->sorts[] = $sort;
	}
	
	public function & getSorts()
	{
		return $this->sorts;
	}
	
	public function setLimit( $limit )
	{
		$this->limit = $limit;
	}
	
	public function getLimit()
	{
		return $this->limit;
	}
	
	public function setDefaultSort( $sort_clause )
	{
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
		
		$this->setSorts($sorts);

		if ( count($persisters) > 0 ) $this->setPersisters($persisters);
	}
	
	public function Query( $parms = array() )
	{
		$this->setParameters( $parms );
		
		return $this->getAll();
	}
	
	public function getAll()
	{
		$sql = 'SELECT '.$this->getSelectClause('t').' FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate();

		$group = $this->getGroupClause('t');

		if ( $group != '' ) $sql .= ' GROUP BY '.$group;
		
		$sort = $this->getSortClause('t');
		
		if ( $sort != '' ) $sql .= ' ORDER BY '.$sort;

		$sql .= $this->getLimitClause();

		return $this->createSQLIterator($sql);
	}
	
	public function Count( $parms = array() )
	{
		$this->setParameters( $parms );
		
		return $this->createSQLIterator(
				'SELECT COUNT(1) cnt FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate()
			)->get('cnt');
	}
	
	public function createSQLIterator( $sql_query ) 
	{
		global $model_factory;
		
		$class_name = get_class($this->getObject()); 

		$cached_iterator = $model_factory->getCachedIterator( $class_name, $sql_query );
	
		if ( is_object($cached_iterator) )
		{
			$cached_iterator->resetStop();

		    $model_factory->debug("Hit SQL cache: ".$sql_query);
			
			return $this->createIterator($cached_iterator->getRowset());
		}
		
		$this->checkSelectOnly($sql_query);
		
		$r2 = DAL::Instance()->Query($sql_query);

   		if ( is_resource($r2) )
   		{
   			$model_factory->info('Query: Ok');
    		
    		$iterator = $this->createIterator( $r2 );

    		$model_factory->cacheIterator( $class_name, $sql_query, $iterator );
   		}
   		else
   		{
   			$model_factory->info('Query: Failed');
   			
   			$iterator = $this->createIterator( array() );
   		}
		
		return $iterator;
	}
	
	// to be protected
	public function getFilterPredicate()
	{
		$predicate = '';
		
		foreach( $this->getFilters() as $filter )
		{
			$filter->setAlias('t');
			
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
	
	public function Store( OrderedIterator $object_it, array $data )
	{
		global $model_factory;
		
		$sql = "";
		
		$object = & $this->getObject();
		
		$pre_sql = "UPDATE ".$object->getEntityRefName()." SET RecordModified = ".
		   ($data['RecordModified'] != '' ? "'".DAL::Instance()->Escape($data['RecordModified'])."'" : "NOW()").", ".
		   "RecordVersion = RecordVersion + 1, ";

		foreach( $object->getAttributes() as $key => $attribute ) 
		{
			if ( $key == 'RecordModified' || $key == 'RecordCreated' ) continue;
			
			if ( in_array($object->getAttributeType($key), array('file', 'image')) ) continue; 
			
			if( !$object->IsAttributeStored($key) )
			{
			    $model_factory->info( 'Skip non-storable attribute: '.$key );
			    
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
			
			$sql .= '`'.$key.'` = '.$object->formatValueForDB($key, DAL::Instance()->Escape(addslashes($value))).',';
		}
		
		if ( $data['VPD'] != '' ) $sql .= "`VPD` = '".DAL::Instance()->Escape(addslashes($data['VPD']))."',";
		
		$model_factory->info( JsonWrapper::encode($parms) );
				
		if ( $sql != '' )
		{
			$sql = $pre_sql.$sql;
			
			if ( $data['RecordVersion'] == '0' || $data['RecordVersion'] != '' )
			{
				$data['RecordVersion'] = DAL::Instance()->Escape(addslashes($data['RecordVersion']));
			}
			else
			{
				$data['RecordVersion'] = $object_it->get('RecordVersion') != '' ? $object_it->get('RecordVersion') : 0;
			}
			
			$sql = substr($sql, 0, strlen($sql) - 1)." WHERE ".
				" RecordVersion = ".$data['RecordVersion'].
				" AND ".$object->getEntityRefName()."Id IN (".join(',', preg_split('/,/', $object_it->getId())).")";
	
			$this->checkUpdateOnly($sql);

			$r2 = DAL::Instance()->Query($sql);

			$affected_rows = DAL::Instance()->GetAffectedRows();

			if ( $affected_rows < 1 ) return $affected_rows;

		    $model_factory->resetCachedIterator($object);
		}
		else
		{
			$model_factory->error( 'SQL query to update attributes is empty: '.$pre_sql );
		}
		
		foreach ( $this->persisters as $persister )
		{
			$persister->modify( $object_it->getId(), $data );
		}		
		
		return $affected_rows;
	}
	
	protected function checkSelectOnly( $sql )
	{
	}
	
	protected function checkUpdateOnly( $sql )
	{
	}
}