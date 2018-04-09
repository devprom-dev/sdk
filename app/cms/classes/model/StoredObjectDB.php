<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include "ObjectRegistrySQL.php";
include "ObjectRegistryArray.php";
include "ObjectRegistryMemory.php";
include "predicates/FilterPredicate.php";
include "predicates/FilterAdditionalObjectsPredicate.php";
include "predicates/FilterAttributePredicate.php";
include "predicates/FilterBaseVpdPredicate.php";
include "predicates/FilterClusterPredicate.php";
include "predicates/FilterInPredicate.php";
include "predicates/FilterNotInPredicate.php";
include "predicates/FilterModifiedAfterPredicate.php";
include "predicates/FilterModifiedSinceSecondsPredicate.php";
include "predicates/FilterModifiedBeforePredicate.php";
include "predicates/FilterNextSiblingsPredicate.php";
include "predicates/FilterNextKeyPredicate.php";
include "predicates/FilterPrevSiblingsPredicate.php";
include "predicates/FilterNoVpdPredicate.php";
include "predicates/FilterSubmittedAfterPredicate.php";
include "predicates/FilterSubmittedBeforePredicate.php";
include "predicates/FilterSubmittedDatePredicate.php";
include "predicates/FilterVpdPredicate.php";
include "predicates/FilterHasNoAttributePredicate.php";
include "predicates/FilterSearchAttributesPredicate.php";
include "predicates/FilterTextExactPredicate.php";
include "predicates/FilterDateAfterPredicate.php";
include "predicates/FilterDateBeforePredicate.php";
include "predicates/FilterAttributeNullPredicate.php";
include "predicates/FilterAttributeNotNullPredicate.php";
include "predicates/FilterDummyPredicate.php";
include "predicates/FilterEmptyPredicate.php";
include "predicates/ParentTransitiveFilter.php";
include "sorts/SortClauseBase.php";
include "sorts/SortCaptionClause.php";
include "sorts/SortOrderedClause.php";
include "sorts/SortRecentClause.php";
include "sorts/SortKeyClause.php";
include "sorts/SortReverseKeyClause.php";
include "sorts/SortRevOrderedClause.php";
include "sorts/SortVPDClause.php";
include "sorts/SortRecentModifiedClause.php";

class StoredObjectDB extends Object
{
 	var $fs_image, $fs_file;
	var $aggregates = array();
 	var $vpd_enabled;
 	var $disabled_notificators;
 	var $aggregate_objects;
 	var $vpd_context;
 	var $notification_enabled;
 	var $default_sorts = array();
 	
 	private $registry = null;
 	
 	private $persisters = array();
	
	//----------------------------------------------------------------------------------------------------------
	function StoredObjectDB( ObjectRegistrySQL $registry = null ) 
	{
		$this->setRegistry(is_object($registry) ? $registry : new ObjectRegistrySQL());
		
		$this->fs_image = new FileStoringFS( $this );	
		$this->fs_file = new FileStoringFS( $this );
		
		$this->resetFilters();
		$this->resetSortClause();
		$this->resetAggregates();
		$this->resetPersisters();
		$this->resetGroupClause();
		
		$this->vpd_enabled = true;
		$this->notification_enabled = true;
		$this->disabled_notificators = array();
		$this->vpd_context = '';
	}
	
	function getEntityRefName()
	{
		return $this->getClassName();
	}
	
	function getOrderStep()
	{
	    return 10;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getDefaultAttributeValue( $name ) 
	{
	    switch ( $name )
	    {
	        case 'OrderNum':
				if ( $this->entity->get('IsDictionary') == 'Y' || $this->IsAttributeRequired($name) ) {
					if ( $this->getVpdValue() == '' ) {
						return 0;
					}
					$it = $this->createSQLIterator(
						"SELECT MAX(t.OrderNum) OrderNum FROM ".$this->getEntityRefName()." t WHERE t.VPD = '".$this->getVpdValue()."'"
					);
					return $it->get( 'OrderNum' ) == '' ? 1 : $it->get( 'OrderNum' ) + $this->getOrderStep();
				}
				return 0;

	        default:
	            return parent::getDefaultAttributeValue( $name );
	    }
	}
	
	function isString( $attribute )
	{
		$type = $this->getAttributeType($attribute);
		
		return $type == '' || $type == 'text' || $type == 'datetime' || $type == 'date' || 
			$type == 'char' || $type == 'varchar';
	}

	//----------------------------------------------------------------------------------------------------------
	function getDataPredicate( $getter_kind ) 
	{
		return '1 = 1';
	}
	
	function checkSelectOnly( $sql )
	{
		/*
		$lowered = strtolower($sql);
		
		$result = strpos( $lowered, 'delete' ) > 0 ||
			strpos( $lowered, 'update' ) > 0 || strpos( $lowered, 'insert' ) > 0 ||
			strpos( $lowered, 'truncate' ) > 0 || strpos( $lowered, 'merge' ) > 0;
			
		if ( $result )
		{
			die();
		}
		*/
	}
	
	function checkInsertOnly( $sql )
	{
	}

	function checkUpdateOnly( $sql )
	{
	}

	function checkDeleteOnly( $sql )
	{
	}

  	//----------------------------------------------------------------------------------------------------------
	function getEmptyIterator() 
	{
	    return $this->createCachedIterator(array());
	}
	
	//----------------------------------------------------------------------------------------------------------
	function createCachedIterator( $rowset ) 
	{
		$iterator = $this->createIterator();

		$iterator->setRowset($rowset);
		
		return $iterator;
	}
		
	function setVpdContext( $context = null )
	{
		global $model_factory;
		
		if ( is_string($context) )
		{
			$this->vpd_context = $context;
			return; 
		}
		
		if ( is_a($context, 'OrderedIterator') )
		{
			$this->vpd_context = $context->get('VPD');
			return; 
		}
		
		if ( is_a($context, 'SotredObjectDB') )
		{
			$this->vpd_context = $context->getVpdValue();
			return; 
		}
		
		$this->vpd_context = '';
	}
	
	function getVpdContext()
	{
		return $this->vpd_context;
	}

	function getVpdValue()
	{
		$origin = getFactory()->getEntityOriginationService()->getSelfOrigin($this);
		
		if ( $origin == '' ) return '';
		
		return $this->vpd_context != '' ? $this->vpd_context : $origin;
	}
	
	function getVpds()
	{
        if ( !$this->IsVpdEnabled() ) return array();

    	if ( $this->vpd_context != '' )
    	{
    		$vpds = array( $this->vpd_context );
    	}
    	else
    	{
        	$vpds = getFactory()->getEntityOriginationService()->getAvailableOrigins($this);
    	}

        return count($vpds) > 0 ? $vpds : array();
	}
	
	function getVpdPredicate( $alias = 't' )
	{
	    $vpds = $this->getVpds();
	    
	    if ( count($vpds) < 1 ) return "";
	    
    	if ( $alias != '' ) $alias .= ".";
    	
    	$sql = " AND ".$alias."VPD IN ('".join($vpds, "','")."') ";
        
        return $sql;
	}

	//----------------------------------------------------------------------------------------------------------
	function getRecordCount( $alias = 't' ) 
	{
		global $model_factory;
		
		$sql = 'SELECT COUNT(1) cnt FROM '.$this->getRegistry()->getQueryClause().' '.$alias.' WHERE 1 = 1 ';
		
		$sql .= $this->getVpdPredicate($alias).' '.$this->getFilterPredicate();
		
		$this->checkSelectOnly($sql);

		$it = $this->createSQLIterator($sql);
		
		return $it->get('cnt');
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getCount() 
	{
		$sql = 'SELECT COUNT(1) Count FROM '.$this->getRegistry()->getQueryClause().' t WHERE 1 = 1 '.$this->getVpdPredicate().$this->getFilterPredicate();

		return $this->createSQLIterator($sql);
	}	
	
	//----------------------------------------------------------------------------------------------------------
	function getExact( $objectid ) 
	{
	    if ( $objectid == '' ) $objectid = -1;
		if ( !is_numeric($objectid) && !is_array($objectid) && $this->IsAttributeStored('Caption') )
		{
			$objectid .= ' ';
			
			$objectid = DAL::Instance()->Escape(
				htmlspecialchars($objectid, ENT_COMPAT | ENT_HTML401, APP_ENCODING));

			$search1 = '[^[:alnum:]]*'.str_replace(' ', '[^[:alnum:]]+', 
				addslashes(preg_quote($this->utf8towin(str_replace('-', ' ', trim($objectid))))) ).'[^[:alnum:]]*';
				
			$search2 = '[^[:alnum:]]*'.str_replace(' ', '[^[:alnum:]]+', 
				addslashes(urldecode(preg_quote(str_replace('-', ' ', trim($objectid))))) ).'[^[:alnum:]]*';
			
			$sql = " SELECT ".$this->getRegistry()->getSelectClause('t')." FROM ".$this->getRegistry()->getQueryClause().
				" t WHERE TRIM(t.".$this->getAttributeDbName('Caption').") REGEXP '^".$search1."$|^".$search2."$' ";

			$sql .= $this->getVpdPredicate().$this->getFilterPredicate();
			
			$group = $this->getGroupClause('t');

    		if ( $group != '' )
    		{
    			$sql .= ' GROUP BY '.$group;
    		}
			
		    $sort = $this->getSortClause('t');
		    
			if ( $sort != '' )
			{
				$sql .= ' ORDER BY '.$sort;
			}
			
			return $this->createSQLIterator($sql.' LIMIT 1');
		}
		elseif ( is_array($objectid) )
		{
            $objectid = array_filter($objectid, function($value) {
                return $value > 0;
            });
            if ( count($objectid) > 0 ) {
                return $this->getRegistry()->Query(
                    array(
                        new FilterInPredicate($objectid)
                    )
                );
            }
            else {
                return $this->getEmptyIterator();
            }
		}
		else {
            return $this->getRegistry()->Query(
                array(
                    new FilterInPredicate($objectid)
                )
            );
        }
	}

	public function setRegistry( $registry )
	{
		$this->registry = $registry;

		$this->registry->setObject($this);
	}
	
	public function getRegistry()
	{
		if ( !is_object($this->registry) ) return $this->registry;
		
		$registry = clone $this->registry;
		$registry->setObject($this);
		$registry->setFilters(array());
		$registry->setGroups(array());
		$registry->setSorts(array()); 
		$registry->setPersisters( $this->persisters );
		return $registry;
	}

    public function getRegistryBase()
    {
        if ( !is_object($this->registry) ) return $this->registry;

        $registry = clone $this->registry;
        $registry->setObject($this);
        $registry->setFilters(array());
        $registry->setGroups(array());
        $registry->setSorts(array());
        $registry->setPersisters(array_filter($this->persisters, function($persister) {
            return $persister->IsPersisterImportant();
        }));
        return $registry;
    }

	// to be removed
	public function getRegistryDefault()
	{
		$registry = clone $this->registry;
		
		$registry->setObject($this);
		
	    $filters = array();

		$vpds = $this->getVpds();

	    if ( count($vpds) > 0 ) $filters[] = new FilterVPDPredicate($vpds);

		$registry->setFilters(array_merge($registry->getFilters(), $filters));

	    $sorts = array();
	    
	    if ( count($sorts) < 1 ) $sorts = $this->default_sorts;

		$registry->setSorts(array_merge($registry->getSorts(), $sorts));

		$registry->setPersisters( $this->persisters );
		
		$registry->setLimit( $this->registry->getLimit() );
		
		$registry->setDefaultSort( $this->defaultsort );
		
		return $registry;
	}
	
	function createSQLIterator( $sql )
	{
		return $this->registry->createSQLIterator( $sql );
	}
	
	// to be removed
	function getAll() 
	{
		return $this->getRegistryDefault()->getAll();
	}

	//----------------------------------------------------------------------------------------------------------
	function getByRef( $reference_field, $reference_object) 
	{
		return $this->getByRefArray( array( $reference_field => $reference_object ) );
	}

	//----------------------------------------------------------------------------------------------------------
	function getByRef2( $reference_field, $reference_object, $reference_field2, $reference_object2) 
	{
		return $this->getByRefArray( 
			array( $reference_field => $reference_object,
				   $reference_field2 => $reference_object2 ) );
	}

	//----------------------------------------------------------------------------------------------------------
	function getByRefArrayWhere( $field_values, $limited_records = 0, $alias = 't') 
	{
		$reference_field = array_keys($field_values);
		$sql = '';
		
		for($i = 0; $i < count($reference_field); $i++) 
		{
			$field_name = $reference_field[$i];
			
			if ( strpos($field_name, "(") === false && !is_numeric($field_name) ) {
				$field_name = $alias.".".$field_name;
			}
			
			if( is_array($field_values[$reference_field[$i]]) ) 
			{
				if ( count($field_values[$reference_field[$i]]) < 1 )
				{
					$field_values[$reference_field[$i]] = array(0);
				}
				else
				{
					for ( $p = 0; $p < count($field_values[$reference_field[$i]]); $p++ )
					{
						$field_values[$reference_field[$i]][$p] =  
							$this->formatValueForDb( $reference_field[$i],
								DAL::Instance()->Escape($field_values[$reference_field[$i]][$p])
							);
					}
				}
				
				$sql .= $field_name.' IN ('.join(',', $field_values[$reference_field[$i]]).')';
			} 
			elseif( is_object($field_values[$reference_field[$i]]) && is_subclass_of($field_values[$reference_field[$i]], 'OrderedIterator') ) 
			{
				$array_values = $field_values[$reference_field[$i]]->idsToArray();
				
				if ( count($array_values) < 1 )
				{
					array_push($array_values, 0);
				}
				
				if( $this->isString($reference_field[$i]) ) 
				{
					$sql .= $field_name." IN ('".join("','", $array_values)."')";
				} 
				else 
				{
					$sql .= $field_name.' IN ('.join(',', $array_values).')';
				}
			} 
			else 
			{
				if(strtolower($field_values[$reference_field[$i]]) == 'null' or 
					$field_values[$reference_field[$i]] == '') 
				{
					$sql .= "IF(".$field_name." = '', NULL, ".$field_name.")".' IS NULL';
				}
				elseif(strtolower($field_values[$reference_field[$i]]) == 'not null')
				{ 
					$sql .= "IF(".$field_name." = '', NULL, ".$field_name.")".' IS NOT NULL';
				}
				else
				{
					$field_values[$reference_field[$i]] = DAL::Instance()->Escape($field_values[$reference_field[$i]]);
					
					if ( $this->getAttributeType($reference_field[$i]) == 'integer' && !is_numeric($field_values[$reference_field[$i]]) )
					{
						$regexp = str_replace('-', '[^[:alnum:]]+', 
							addslashes(preg_quote($this->utf8towin(trim($field_values[$reference_field[$i]])))) );
							
						$sql .= " TRIM(Caption) REGEXP '^".$regexp."$' ";
					}
					else
					{
						$field_values[$reference_field[$i]] = 
							$this->formatValueForDB( $reference_field[$i], addslashes($field_values[$reference_field[$i]]) );
						
						if( $this->isString($reference_field[$i]) )
						{
							$sql .= "BINARY ".$field_name." = ".$field_values[$reference_field[$i]];
						}
						else
						{
							$sql .= $field_name.' = '.$field_values[$reference_field[$i]];
						}
					}
				}
			}

			if( $i < count($reference_field) - 1 ) {
				$sql .= ' AND ';
			}
		}					   
		
		if ( count($reference_field) < 1 )
		{
			$sql .= ' 1 = 1 ';
		}
		
		if ( !in_array('VPD', array_keys($field_values)) )
		{
			$sql .= $this->getVpdPredicate($alias);
		}

		$sql .= $this->getFilterPredicate();

		return $sql;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getByRefArray( $field_values, $limited_records = 0, $offset_page = 0) 
	{
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('t').' FROM '.$this->getRegistry()->getQueryClause().' t WHERE ';
		$sql .= $this->getByRefArrayWhere( $field_values, $limited_records );
		
		$limited_records = DAL::Instance()->Escape($limited_records);
		$offset_page = DAL::Instance()->Escape($offset_page);

		$group = $this->getGroupClause('t');

		if ( $group != '' )
		{
			$sql .= ' GROUP BY '.$group;
		}
		
		$sort = $this->getSortClause('t');
		
		if ( $sort != '' )
		{
			$sql .= ' ORDER BY '.$sort;
		}
		else
		{
			if(isset($this->defaultsort)) $sql .= ' ORDER BY '.$this->defaultsort;
		}
		
		if($limited_records > 0) $sql .= ' LIMIT '.$limited_records.' OFFSET '.($offset_page * $limited_records);

		return $this->createSQLIterator($sql);
	}

	//----------------------------------------------------------------------------------------------------------
	function getByRefArrayCount( $field_values, $alias = 't' ) 
	{
		$sql = 'SELECT COUNT(1) FROM '.$this->getRegistry()->getQueryClause().' '.$alias.' WHERE ';

		$sql .= $this->getByRefArrayWhere( $field_values, 0, $alias );

		$this->checkSelectOnly($sql);

		$data = DAL::Instance()->QueryArray($sql);

   		return $data[0] == '' ? 0 : $data[0];
	}

	//----------------------------------------------------------------------------------------------------------
	function getByRefArrayLatest( $field_values)
	{
		$this->defaultsort = 'RecordModified DESC';
		return $this->getByRefArray( $field_values, 1 );
	}
	 
	//----------------------------------------------------------------------------------------------------------
	function getByRefArrayEarliest( $field_values)
	{
		$this->defaultsort = 'RecordModified ASC';
		return $this->getByRefArray( $field_values, 1 );
	}

	//----------------------------------------------------------------------------------------------------------
	function getBetween( $reference_field, $bound_a, $bound_b ) 
	{
		$bound_a = DAL::Instance()->Escape($bound_a);
		$bound_b = DAL::Instance()->Escape($bound_b);
		
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('t').' FROM '.$this->getRegistry()->getQueryClause().' t WHERE '.
			$reference_field.' BETWEEN '.$bound_a.' AND '.$bound_b;
			
		$sql .= $this->getVpdPredicate();
		
		if(isset($this->defaultsort)) $sql .= ' ORDER BY '.$this->defaultsort;

		return $this->createSQLIterator($sql);
	}

	//----------------------------------------------------------------------------------------------------------
	function getIds() 
	{
		$sql = 'SELECT '.$this->getEntityRefName().'Id FROM '.$this->getRegistry()->getQueryClause().' WHERE 1 = 1 ';
        
		$sql .= $this->getVpdPredicate();

		$predicate = $this->getDataPredicate('ids');
		if($predicate != '') $sql .= ' AND '.$predicate.' ';

		if(isset($this->defaultsort)) $sql .= ' ORDER BY '.$this->defaultsort;

		return $this->createSQLIterator($sql);
	}

	//----------------------------------------------------------------------------------------------------------
	function getIn( $reference_field, $object_it ) 
	{
		$in_values = array();
		$object_it->moveFirst();
		for($i = 0; $i < $object_it->count(); $i++) {
			array_push($in_values, $object_it->getId());
			$object_it->moveNext();
		}
		$object_it->moveFirst();
		
		return $this->getInArray( $reference_field, $in_values);
	}

	//----------------------------------------------------------------------------------------------------------
	function getInArray( $reference_field, $in_values ) 
	{
		if ( !is_array($in_values) || count($in_values) < 1 ) 
		{
			$in_values = array(0);
		}
		
		if ( $this->isString($reference_field) ) 
		{
			for($i = 0; $i < count($in_values); $i++) {
				$in_values[$i] = "'".DAL::Instance()->Escape($in_values[$i])."'";
			}
		}
		else
		{
			for($i = 0; $i < count($in_values); $i++) {
				$in_values[$i] = $in_values[$i] == '' ? 0 : DAL::Instance()->Escape($in_values[$i]);
			}
		}
		
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('t').' FROM '.$this->getRegistry()->getQueryClause().' t WHERE t.'.
			$reference_field.' IN ('.join(',',$in_values).') ';
			
		$sql .= $this->getVpdPredicate();
        
		if(isset($this->defaultsort)) $sql .= ' ORDER BY '.$this->defaultsort;

		return $this->createSQLIterator($sql);
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getLike( $text, $like_field = 'Caption' )
	{
		$text = DAL::Instance()->Escape( $text );
		
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('').' FROM '.$this->getRegistry()->getQueryClause().' WHERE '.$like_field." LIKE '%".$text."%' ";
        
		$sql .= $this->getVpdPredicate();
        
		$predicate = $this->getDataPredicate('like');
		if($predicate != '') $sql .= ' AND '.$predicate.' ';
		$sql = $sql."ORDER BY ".$like_field;

		return $this->createSQLIterator($sql);
	}

	// to be removed
	function getFirst( $limit = 1, $sorts = null )
	{
		$registry = $this->getRegistryDefault();
		
		$registry->addSort( new SortAttributeClause('RecordCreated') );
		
		$registry->setLimit( $limit );
		
		return $registry->getAll();
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getLatest( $limit = 10, $offset = 0 )
	{
		$registry = $this->getRegistryDefault();
		
		$registry->setDefaultSort('');
		
		$registry->setSorts( array(new SortRecentClause()) );
		
		$registry->setLimit( $limit );
		
		return $registry->getAll();
	}

	//----------------------------------------------------------------------------------------------------------
	function getEarliest( $limit = 1 )
	{
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('t').' FROM '.$this->getRegistry()->getQueryClause().' t WHERE 1 = 1 ';
		
		$sql .= $this->getVpdPredicate('t');
        
		$group = $this->getGroupClause('t');

		if ( $group != '' )
		{
			$sql .= ' GROUP BY '.$group;
		}
		
		$sql = $sql." ORDER BY t.RecordModified ASC LIMIT ".$limit;

		return $this->createSQLIterator($sql);
	}

	function getAggregated( $alias = 't', $sorts = array(), $persisters = array() )
	{
		$aggs = $this->getAggregateObjects();

		if ( count($persisters) < 1 ) $persisters = $this->getPersisters();
		
		$outer_columns = array();
		$inner_columns = array();
		$agg_attrs = array();
		$agg_columns = array();

		$usedPersisters = array();
		foreach ( $aggs as $aggregate )
		{
			foreach( $persisters as $persister ) {
				if ( !$persister instanceof ObjectPersister ) continue;
				if ( count(array_intersect(array($aggregate->getAggregatedAttribute(), $aggregate->getAttribute()), $persister->getAttributes())) < 1 ) continue;
				$usedPersisters[] = $persister;
			}

			// custom attributes are part of inner select already, just skip it
			if ( $this->getAttributeOrigin($aggregate->getAttribute()) != ORIGIN_CUSTOM ) {
				$column = trim($aggregate->getInnerColumn());
				if ( $column != '' ) array_push( $inner_columns, $column );
			}
			
			$column = trim($aggregate->getColumn());
			if ( $column != '' ) array_push( $outer_columns, $column );
			
			$column = trim($aggregate->getAggregatedInnerColumn());
			if ( $column != '' ) array_push( $agg_attrs, $column );
			
			$column = $aggregate->getAggregateColumn();
			if ( $column != '' ) array_push( $agg_columns, $column );
			
			$alias = $aggregate->getAlias();
		}

		$registry = $this->getRegistryDefault();
		$registry->setPersisters($usedPersisters);

		$inner_columns = array_unique(array_merge( array_unique($inner_columns), array_unique($agg_attrs) ));
		
		$select_clause = $registry->getSelectClause($alias, false);
		foreach( $inner_columns as $key => $column ) {
			$column = str_replace($alias.'.', '', $column);
			if ( strpos( $select_clause, ') '.$column.' ' ) > 0 || strpos( $select_clause, '`'.$column.'`' ) > 0 ) {
			    unset($inner_columns[$key]);
			} 
		}

		$sort_clause = $this->getSortClause($alias);
		
		$inner_select = ($select_clause != '' 
			? join(',', array_merge($inner_columns, array($select_clause))) 
			: join(',', $inner_columns));

		$predicate = $this->getFilterPredicate();
		if ( strpos($predicate, 'VPD') === false ) {
			$vpdFilter = $this->isVpdEnabled() && $this->getVpdValue() != '' ? " AND t.VPD IN ('".join("','",$this->getVpds())."') " : "";
		}

		$sql = " SELECT ".join($outer_columns, ',').", ".join($agg_columns, ',').
			   "   FROM (SELECT ".$inner_select.
			   "		   FROM ".$registry->getQueryClause()." t, (SELECT @row_num:=0) foo " .
			   "          WHERE 1 = 1 ".$predicate.$vpdFilter.
			   ($sort_clause != '' ? " ORDER BY ".$sort_clause : "").
			   "		) t ".
			   "  GROUP BY ".join($outer_columns, ',');

		return $this->createSQLIterator( $sql );
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getAggregatedHistory( $predicates = array(), $sorts = array() )
	{
		$agg_predicate = '';
		$object_predicate = '';

		$aggs = $this->aggregate_objects;
		$aggregate = $aggs[count($this->aggregate_objects) - 1];

        foreach( $predicates as $filter )
        {
            if ( $filter instanceof FilterModifiedAfterPredicate || $filter instanceof FilterModifiedBeforePredicate ) {
                $filter->setAlias('h');
                $agg_predicate .= $filter->getPredicate();
            }
        }
        foreach( $this->getFilters() as $filter )
        {
			if ( $filter instanceof FilterModifiedAfterPredicate || $filter instanceof FilterModifiedBeforePredicate ) continue;

            $filter->setAlias('t');
            $filter_sql = $filter->getPredicate();

            if ( strpos($filter_sql, $aggregate->getColumn().' ') !== false ) {
                $object_predicate .= str_replace( $aggregate->getColumn(), 'h.AttributeValue', $filter_sql );
            }
            $object_predicate .= $filter_sql;
        }

		if ( $object_predicate != '' )
		{
		    $sql = " SELECT UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(t.RecordCreated))) DayDate, " .
				   "		".$aggregate->getColumn().", " .
				   " 		".$aggregate->getAggregateColumn().
				   "   FROM (SELECT h.RecordCreated, h.RecordModified, h.AttributeValue ".$aggregate->getAttribute().
				   "           FROM ".$this->getEntityRefName()." t," .
				   " 	    		cms_EntityCluster h ".
				   "  		  WHERE 1 = 1 ".
									$this->getVpdPredicate('t').
									$agg_predicate.$object_predicate.
                   "    		AND h.VPD = t.VPD " .
				   "    		AND h.ObjectClass = '".get_class($this)."' " .
				   "    		AND h.ObjectAttribute = '".$aggregate->getAttribute()."'" .
				   "    		AND h.ObjectIds LIKE CONCAT('%,',t.".$this->getEntityRefName()."Id,',%') ".
				   " 		) t ".
				   "  GROUP BY 1, 2";
		}
		else
		{
            $sql = " SELECT UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(t.RecordCreated))) DayDate, " .
				   "		".$aggregate->getColumn().", " .
				   " 		SUM(t.TotalCount) ".$aggregate->getAggregateAlias().
				   "   FROM (SELECT h.RecordCreated, h.RecordModified, ".
				   "                h.TotalCount, h.AttributeValue ".$aggregate->getAttribute().
				   "           FROM cms_EntityCluster h ".
				   "  		  WHERE 1 = 1 ".$agg_predicate.
				   "    		AND h.ObjectClass = '".get_class($this)."' " .
				   "    		AND h.ObjectAttribute = '".$aggregate->getAttribute()."' ".
				   " 		) t ".
				   "  GROUP BY 1, 2";
		}
        if ( $this->registry->getLimit() > 0 ) {
            $sql .= " LIMIT ".$this->registry->getLimit();
        }
		return $this->createSQLIterator( $sql );
	}

	//----------------------------------------------------------------------------------------------------------
	function getHashTable( $attributes )
	{
		$all_it = $this->getAll();
		$iterator = new HashIterator( $this, $attributes, $all_it);
		
		return $iterator;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getChildren( $objectid )
	{
		$sql = 'SELECT '.$this->getRegistry()->getSelectClause('').' FROM '.$this->getRegistry()->getQueryClause().
			' WHERE Parent'.$this->getEntityRefName().'Id = '.DAL::Instance()->Escape($objectid);
			
		$sql .= $this->getVpdPredicate();

		if(isset($this->defaultsort)) $sql .= ' ORDER BY '.$this->defaultsort;

		return $this->createSQLIterator($sql);
	}

	//----------------------------------------------------------------------------------------------------------
	function getAttributeRDBMSDefinition( $attribute_name )
	{
	   $attribute_type = $this->getAttributeType($attribute_name);
	   $sql = '';

       if($attribute_type == 'image') {
        $sql .= " ".$this->fs_image->getDataDefinition($attribute_name);
        return $sql;
       }
       if($attribute_type == 'file') {
        $sql .= " ".$this->fs_file->getDataDefinition($attribute_name);
        return $sql;
       }
       if($attribute_type == 'price') {
        $sql .= " Price INTEGER, PriceCode varchar(32)";
        return $sql;
       }
       if($attribute_type == 'richtext') {
        $sql .= $attribute_name.' TEXT';
        return $sql;
       }
       if($attribute_type == 'varchar') {
        $sql .= $attribute_name.' VARCHAR(32)';
        return $sql;
       }
       if(is_subclass_of($attribute_type, 'StoredObjectDB')) {
        $sql .= $attribute_name.' INTEGER';
        return $sql;
       }
       else {
        $sql .= $attribute_name.' '.$this->getAttributeType();
		return $sql;
       }
	}
	
	//----------------------------------------------------------------------------------------------------------
	function Install()
	{
	    global $model_factory;
	    
		$sql = '  CREATE TABLE '.$this->getEntityRefName().
			     ' ('.$this->getEntityRefName().'Id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, VPD VARCHAR(32), RecordVersion INTEGER DEFAULT 0,';
		
		$keys = array_keys($this->getAttributes());
		for($i=0; $i < count($keys); $i++) 
		{
			$sql .= $this->getAttributeRDBMSDefinition($keys[$i]).',';
		}
		$sql = substr($sql, 0, strlen($sql) - 1).' ) ENGINE=MyISAM DEFAULT CHARSET='.APP_CHARSET.' ';

        DAL::Instance()->Query($sql);

		if(is_object($model_factory))
			if($model_factory->sql_log_enabled) {
				$sql = str_replace('\'', '\\\'', $sql);
				$sql_log = "INSERT INTO SystemLogSQL (SQLContent, RecordCreated) VALUES ('".$sql."', NOW())";
				$r3 = DAL::Instance()->Query($sql_log);
			}
	}
	
	//----------------------------------------------------------------------------------------------------------
	function UnInstall() 
	{
	    global $model_factory;
	    
		$sql = 'DROP TABLE '.$this->getEntityRefName();
   		
		$r2 = DAL::Instance()->Query($sql);

		if(is_object($model_factory))
			if($model_factory->sql_log_enabled) {
				$sql = str_replace('\'', '\\\'', $sql);
				$sql_log = "INSERT INTO SystemLogSQL (SQLContent, RecordCreated) VALUES ('".$sql."', NOW())";
				$r3 = DAL::Instance()->Query($sql_log);
			}
	}

	//----------------------------------------------------------------------------------------------------------
 	function add_parms( $parms )
	{
		global $model_factory;

       	// Формируем запрос для вставки записи в таблицу
		$sql = "INSERT INTO ".$this->getEntityRefName()." ( RecordCreated,RecordModified,VPD,";
		
		if ( $parms[$this->getEntityRefName().'Id'] > 0 )
		{
		    $sql .= $this->getEntityRefName().'Id,';
		}

		if ( $parms['RecordCreated'] == '' ) {
			$parms['RecordCreated'] = SystemDateTime::date();
		}
		if ( $parms['RecordModified'] == '' ) {
			$parms['RecordModified'] = SystemDateTime::date();
		}
        $values = " VALUES ( ".
        	($parms['RecordCreated'] != '' ? "'".$parms['RecordCreated']."'" : "NOW()").", ".
        	($parms['RecordModified'] != '' ? "'".$parms['RecordModified']."'" : "NOW()").", ";

		if ( $parms['VPD'] != '' )
		{
			$values = $values."'".DAL::Instance()->Escape(addslashes($parms['VPD']))."',"; 
		}
		else
		{
			$vpd_hash = 'NULL';
			
	        if ( is_object($model_factory) && $this->IsVPDEnabled() ) 
	        {
	        	$vpd_hash = $this->getVpdValue();
	        	
	        	if ( $vpd_hash == '' )
	        	{
	        		$vpd_hash = 'NULL';
	        	}
	        	else
	        	{
	        		$vpd_hash = "'".$vpd_hash."'";
	        	}
	        }
	        
			$values = $values.$vpd_hash.","; 
		}
		
		if ( $parms[$this->getEntityRefName().'Id'] > 0 )
		{
		    $values .= $parms[$this->getEntityRefName().'Id'].',';
		}

		foreach ( $this->persisters as $persister ) {
			$persister->map( $parms );
		}

		$imageattributes = array();
		$fileattributes = array();
		
		$keys = array_keys($this->getAttributes());
		for($i=0; $i < count($keys); $i++) 
		{
			if ( $keys[$i] == 'RecordModified' || $keys[$i] == 'RecordCreated' ) continue;
			
			if( !$this->IsAttributeStored($keys[$i]) ) continue;
			
			if($this->getAttributeType($keys[$i]) == 'image') {
				array_push($imageattributes, $keys[$i]);
				continue;
			}
			if($this->getAttributeType($keys[$i]) == 'file') {
				array_push($fileattributes, $keys[$i]);
				continue;
			}
			if($this->getAttributeType($keys[$i]) == 'price') {
				$sql .= $keys[$i].','.$keys[$i].'Code,';
				$values .= $this->formatValueForDB($keys[$i], $parms[$keys[$i]]).', \''.$parms[$keys[$i].'Code'].'\',';
				continue;
			}

			if($this->getAttributeType($keys[$i]) == 'char' && 
			   !array_key_exists($keys[$i], $parms) && $parms[$keys[$i].'OnForm'] != '') 
			{
				$parms[$keys[$i]] = 'N';
			}

			if ( $parms[$keys[$i]] == '' && $this->IsAttributeRequired($keys[$i]) )
			{
				$parms[$keys[$i]] = $this->getDefaultAttributeValue($keys[$i]);
			}

			if( isset($parms[$keys[$i]]) ) 
			{
				$sql .= "`".$keys[$i].'`,';
				
				$values .= $this->formatValueForDB($keys[$i], 
					DAL::Instance()->Escape(addslashes($parms[$keys[$i]]))).',';
			}
		}
		
		$values = substr($values, 0, strlen($values) - 1)." ) ";
		$sql = substr($sql, 0, strlen($sql) - 1)." ) ".$values;

		$this->checkInsertOnly($sql);

		$model_factory->info( JsonWrapper::encode($parms) );
		
		$r2 = DAL::Instance()->Query($sql);
		
		if ( $r2 === false )
		{
		    $model_factory->error('Query: Failed');
		}
		else 
		{
		    $model_factory->info('Query: Ok');
		}

		if ( $parms[$this->getEntityRefName().'Id'] > 0 )
		{
		    $id = $parms[$this->getEntityRefName().'Id'];
		}
		elseif( $r2 === true )
		{		    
    		$d = DAL::Instance()->QueryArray('SELECT LAST_INSERT_ID()');
    		$id = $d[0];
		}

		getFactory()->resetCachedIterator($this);
		
		foreach ( $this->persisters as $persister )
		{
			$persister->add( $id, $parms );
		}
		
        if ( count($imageattributes) > 0 || count($fileattributes) > 0  )
        {
			$new_object_it = $this->getExact($id);
			
			// загружаем изображения
			for($i=0; $i < count($imageattributes); $i++) {
				$this->fs_image->storeFile( $imageattributes[$i], $new_object_it, $parms );
			}
			// загружаем файлы
			for($i=0; $i < count($fileattributes); $i++) {
				$this->fs_file->storeFile( $fileattributes[$i], $new_object_it, $parms );
			}
		}

		if ( $this->getNotificationEnabled() )
		{
    		$new_object_it = $this->getExact($id);
    		
    		if ( $new_object_it->getId() > 0 )
    		{
    			getFactory()->getEventsManager()->notify_object_add($new_object_it, $parms);
    		}
		}

		getFactory()->resetCachedIterator($this);
		
		return $id;
	}
	
	protected function beforeDelete( $object_it )
	{
		foreach ( $this->persisters as $persister ) {
			$persister->beforeDelete( $object_it );
		}
	}

	//----------------------------------------------------------------------------------------------------------
	function delete( $id, $record_version = '' )
	{
		getFactory()->resetCachedIterator( $this );

		$object_it = $this->getExact($id);
		if ( $object_it->getId() == '' ) return 0;

		$rows = $this->deleteInternal( $object_it, $record_version );

		getFactory()->resetCachedIterator($this);
		if ( $this->getNotificationEnabled() ) {
			getFactory()->getEventsManager()->notify_object_delete($object_it);
		}

		return $rows;
	}

	protected function deleteInternal( $object_it, $record_version = '' )
	{
		$this->beforeDelete($object_it);

		// get file/image attributes
		$keys = array_keys($this->getAttributes());
		$file_attributes = array();
		$image_attributes = array();
		for ( $i=0; $i < count($keys); $i++ ) {
			if($this->getAttributeType($keys[$i]) == 'image') {
				$image_attributes[] = $keys[$i];
			}
			if($this->getAttributeType($keys[$i]) == 'file') {
				$file_attributes[] = $keys[$i];
			}
		}

		if ( $record_version != '' )
		{
			$parms['RecordVersion'] = DAL::Instance()->Escape(
				addslashes($record_version));
		}
		else
		{
			$parms['RecordVersion'] = $object_it->get('RecordVersion') != ''
				? $object_it->get('RecordVersion') : 0;
		}

		// удаляем запись
		$id = DAL::Instance()->Escape( $object_it->getId() );

		$sql = "DELETE FROM ".$this->getEntityRefName().
			" WHERE ".$this->getEntityRefName()."Id IN (".join(',',preg_split('/,/', $id)).")".
			"   AND RecordVersion = ".$parms['RecordVersion'];

		$this->checkDeleteOnly($sql);

		$r2 = DAL::Instance()->Query($sql);

		$affected_rows = DAL::Instance()->GetAffectedRows();
		if ( $affected_rows < 1 ) return $affected_rows;

		foreach ( $this->persisters as $persister ) {
			$persister->afterDelete( $object_it );
		}

		return $affected_rows;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function deleteAll()
	{
		getFactory()->getEventsManager()->notify_object_delete( $this->getEmptyIterator() );
		
		$sql = "DELETE FROM ".$this->getEntityRefName()." WHERE 1 = 1 ";
		
        $vpds = $this->getVpds();
        	
        if ( count($vpds) > 0 ) $sql .= " AND VPD IN ('".join("','",$vpds)."') ";
		
		$r2 = DAL::Instance()->Query($sql);

		getFactory()->resetCachedIterator($this);

		getFactory()->getEventsManager()->notify_object_delete( $this->getEmptyIterator() );
	}	

	//----------------------------------------------------------------------------------------------------------
	function modify_parms( $id, $parms )
	{
		if ( count($parms) < 1 ) throw new Exception('There are no attributes to be updated');

		if ( $id instanceof OrderedIterator ) {
			$prev_object_it = $id;
			$id = $prev_object_it->getId();
		}
		else {
			$prev_object_it = $this->getExact($id);
			if ( $prev_object_it->getId() == '' ) throw new Exception('There is no object "'.$id.'" of the entity "'.get_class($this).'"');
		}

		$imageattributes = array();
		$fileattributes = array();

		$keys = array_keys($this->getAttributes());

		for($i=0; $i < count($keys); $i++) 
		{
			if($this->getAttributeType($keys[$i]) == 'image') {
				array_push($imageattributes, $keys[$i]);
				continue;
			}
			
			if($this->getAttributeType($keys[$i]) == 'file') {
				array_push($fileattributes, $keys[$i]);
				continue;
			}
		}

		$affected_rows = $this->getRegistryDefault()->Store( $prev_object_it, $parms );
		
		if ( count($imageattributes) > 0 || count($fileattributes) > 0 )
		{
		    $now_object_it = $this->getExact($id);
		    
		    foreach( $imageattributes as $attribute )
		    {
		        $this->fs_image->storeFile( $attribute, $now_object_it );
		    }
		      
		    foreach( $fileattributes as $attribute )
		    {
		        $this->fs_file->storeFile( $attribute, $now_object_it );
		    }
		}
		
		if ( $this->getNotificationEnabled() )
		{
		    if ( !is_object($now_object_it) ) $now_object_it = $this->getExact($id);

		    getFactory()->getEventsManager()->notify_object_modify($prev_object_it, $now_object_it, $parms);
		}
		
		return $affected_rows;
	}

	//----------------------------------------------------------------------------------------------------------
	function modify( $object_id ) 
	{
		global $_REQUEST;
		return $this->modify_parms($object_id, $_REQUEST);
	}
	
	//----------------------------------------------------------------------------------------------------------
	function formatValueForDB( $name, $value )
	{
		if ( in_array($value, array('', 'null', 'NULL')) ) return 'NULL';
		
		switch ( $name )
		{
			case 'VPD':
				
				$attribute_type = 'varchar';
				
				break;
				
			default:
				$attribute_type = $this->getAttributeType($name);
				
				if ( $attribute_type == '' )
				{
					$attribute_type = strpos($name, "(") !== false ? 'varchar' : 'integer';
				}
		}
		
		switch( strtolower($attribute_type) )
		{
			case 'date':
			case 'datetime':
				if ( strtolower($value) == 'now()' ) return $value;
				if ( $value == 0 ) return 'NULL';
				if ( strpos($value, '0000-00-00') !== false ) return 'NULL';
				break;
				
			case 'float':
			case 'integer':
                $value = str_replace(',', '.', $value);
                if ( $value != '' && !is_numeric($value) ) $value = 0;
				return htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, APP_ENCODING);
		}
		
		return "'".htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML401, APP_ENCODING)."'";
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getUniqueAttributes()
	{
		if ( array_key_exists('Caption', $this->getAttributes()) )
			return array('Caption');
		
		$keys = array_keys($this->getAttributes());
		return array($keys[1]);
	}
	
	function disableVpd()
	{
		$this->vpd_enabled = false;
	}
	
	function enableVpd()
	{
		$this->vpd_enabled = true;
	}
	
	function removeNotificator( $base_class_name )
	{
		array_push($this->disabled_notificators, $base_class_name);
	}
	
	function getDisabledNotificators()
	{
		return $this->disabled_notificators;
	}
	
	function resetDisabledNotificators()
	{
		return $this->disabled_notificators = array();
	}
	
	function getNotificationEnabled()
	{
	    return $this->notification_enabled;
	}
	
 	function setNotificationEnabled( $enabled = true )
	{
	    $this->notification_enabled = $enabled;
	}
	
	function isVpdEnabled()
	{
		return $this->vpd_enabled;
	}

	function addFilter( $filter )
	{
		if ( !$filter->defined($filter->getValue()) ) return;
		
		$filters = $this->registry->getFilters();
		
		$filter->setObject( $this );

		$filters[] = $filter;

		$this->registry->setFilters($filters);
	}
	
	function resetFilters()
	{
		$empty = array();
		
		$this->registry->setFilters($empty);
	}
	
	function getFilters()
	{
		return $this->registry->getFilters();
	}
	
	function setFilters( $filters )
	{
		$this->registry->setFilters($filters);
	}
	
	function getFilterPredicate( $alias = 't' )
	{
		return $this->registry->getFilterPredicate($alias);
	}
	
	function setSortDefault( $clause )
	{
	    if ( !is_array($clause) ) $clause = array( $clause );
	    
	    $this->default_sorts = $clause;
	    
	    $sorts = array();
	    foreach ( $clause as $key => $item )
	    {
	        $clause[$key]->setObject( $this );
	        $clause[$key]->setAlias( 't' );

	        $clauseSql = trim($clause[$key]->clause());
	        if ( $clauseSql == '' ) continue;
	        $sorts[] = $clauseSql;
	    }
	    
	    $this->defaultsort = join(',', $sorts);
	}
	
	function getSortDefault()
	{
		return $this->default_sorts;
	}
	
	function addSort( $clause )
	{
		$clause->setObject( $this );
		
		$sorts = $this->registry->getSorts();
		
		$sorts[] = $clause;
		
		$this->registry->setSorts($sorts);
	}
	
	function getSortClause( $alias = 't', $sorts = null )
	{
		return $this->registry->getSortClause( $alias );
	}
	
	function resetSortClause()
	{
		$empty = array();
		
		$this->registry->setSorts($empty);
	}
	
	function getSort()
	{
		$sort = $this->getSortClause();
		if ( $sort != '' )
		{
			return " ORDER BY ".$sort;
		}
		
		return "";
	}
	
 	function addGroup( $clause )
	{
		$groups = $this->registry->getGroups();
		
		$clause->setObject( $this );
		
		$groups[] = $clause;
		 
		$this->registry->setGroups($groups);
	}
	
 	function getGroupClause( $alias = 't' )
	{
		return $this->registry->getGroupClause($alias);
	}
	
	function resetGroupClause()
	{
		$empty = array();
		
		$this->registry->setGroups($empty);
	}

	function addAggregate( $aggregate )
	{
		$aggregate->setObject( $this );
		array_push( $this->aggregate_objects, $aggregate );
	}
	
	function resetAggregates()
	{
		$this->aggregate_objects = array();
	}
	
	function getAggregateObjects()
	{
		return $this->aggregate_objects;
	}
	
	function addPersister( $persister )
	{
		if ( isset($this->persisters[$persister->getId()]) ) return;
		
		$persister->setObject($this);
		
		$this->persisters[$persister->getId()] = $persister;
	}
	
	function setPersisters( $persisters )
	{
		$this->persisters = $persisters;
		
		foreach( $this->persisters as $key => $persister )
		{
			$this->persisters[$key]->setObject($this);
		}
	}
	
	function getPersisters()
	{
		return $this->persisters;
	}

	function resetPersisters()
	{
		$this->persisters = array();
	}
	
	function setLimit( $limit )
	{
		$this->limit = $limit;
	}
	
	function getLimitClause()
	{
		if ( !is_numeric( $this->limit ) ) return;
		if ( $this->limit > 0 ) return ' LIMIT '.$this->limit;
	}

	public function __sleep()
	{
		throw new Exception('Unable serialize StoredObjectDB');
	}
}