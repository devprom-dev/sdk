<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectAffectedDatePersister.php";
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectUIDPersister.php";

class PageList extends ListTable
{
 	private $table = null;
 	private $view = null;
 	private $list_mode = '';
 	private $group_it = null;
 	private $references_it = array();
 	private $system_attributes = array();
 	private $uid_service = null;
 	private $iterator_data = null;
    private $itemsCount = null;

 	function PageList( $object )
 	{
 		parent::__construct( $object );

 		$this->system_attributes = $this->buildSystemAttributes();
 		
		$plugins = getFactory()->getPluginsManager();
			    
		$this->plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
 	}
 	
  	function __destruct()
 	{
 		$this->view = null;
 		$this->table = null;
 	}
 	
 	function buildSystemAttributes()
 	{
	    $system_attributes = $this->getObject()->getAttributesByGroup('system');

	    if ( in_array('State', $system_attributes) ) unset( $system_attributes[array_search('State', $system_attributes)] );
 		
	    return $system_attributes;
 	}

	function extendModel()
	{
		$object = $this->getObject();

		$this->uid_service = new ObjectUID('', $object);

		if ( !in_array($object->getAttributeType('UID'), array('','integer')) ) {
			$object->setAttributeOrderNum('UID', 0);
		}
		else if ( $this->uid_service->hasUidObject( $object ) ) {
			$object->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
			$object->addPersister(new ObjectUIDPersister());
		}
	}
 	
 	function getSystemAttributes()
 	{
 		return $this->system_attributes;
 	}
 	
 	function getUidService()
 	{
 		return $this->uid_service;
 	}
 	
 	function setInfiniteMode()
 	{
 	    $this->list_mode = 'infinite';
    }

 	function getIterator() 
	{
        if ( is_null($this->iterator_data) ) {
            $iterator = $this->buildIterator();
            $this->iterator_data = $iterator->getRowset();
        }
        else {
            $iterator = $this->getObject()->createCachedIterator($this->iterator_data);
        }
		return $iterator;
	}

	function buildIterator()
    {
        $object = $this->getObject();

        $filters = $this->getFilterValues();
        $predicates = array_merge(
            $this->getPredicates( $filters ),
            $object->getFilters()
        );

        $plugins = getFactory()->getPluginsManager();
        $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getTable()->getSection()) : array();
        foreach( $plugins_interceptors as $plugin ) {
            $plugin->interceptMethodListGetPredicates( $this, $predicates, $filters );
        }

        $ids = $this->getIds();
        if ( count($ids) > 0 ) {
            $predicates[] = new FilterInPredicate($ids);
        }
        $predicates[] = new FilterVpdPredicate();

        $sorts = array();
        foreach( $this->getSorts() as $sort ) {
            if ( $sort instanceof SortAttributeClause ) {
                $sorts[] = $sort->getAttributeName();
            }
        }

        $persisters = $this->getPersisters($object, $sorts);
        $sorts = $this->getSorts();

        $registry = $object->getRegistry();
        $registry->setPersisters(array());
        $registry->setSorts(array());

        $this->itemsCount = $registry->Count($predicates);

        if ( $this->itemsCount > 0 ) {
            $limit = $this->getMaxOnPage();
            if ( is_numeric($limit) && $limit > 0 ) {
                $registry->setLimit($limit > 1 ? $limit : 99999);
                $offset = $this->getOffset();
                if ( is_numeric($offset) && $offset > 0 ) {
                    $registry->setOffset($offset);
                }
            }
        }

        return $registry->Query(
            array_merge(
                $predicates, $sorts, $persisters
            )
        );
    }

    function shiftNextPage( $iterator, $offset ) {
        if ( is_null($this->itemsCount) ) {
            $iterator->moveToPos($offset);
        }
    }

	function getItemsCount( $iterator ) {
        return is_null($this->itemsCount) ? parent::getItemsCount($iterator) : $this->itemsCount;
    }

	function getTotalIt( $attributes )
    {
        $values = array();
        $rowset = $this->getIteratorRef()->getRowset();
        foreach( $attributes as $attribute ) {
            $value = array_sum(
                array_map(function($row) use($attribute) {
                    return $row[$attribute];
                }, $rowset)
            );
            if ( $value != '' ) $values[$attribute] = $value;
        }
        if ( count($values) < 1 ) {
            return $this->getObject()->getEmptyIterator();
        }
        else {
            return $this->getObject()->createCachedIterator(array($values));
        }
    }

	protected function getPersisters( $object, $sorts )
    {
        $group = $this->getGroup();
        $persisters = $object->getPersisters();
        foreach( $persisters as $key => $persister )
        {
            if ( $persister->IsPersisterImportant() ) continue;
            $attributes = $persister->getAttributes();
            if ( count($attributes) < 1 ) continue;

            $visible = false;
            foreach( $attributes as $attribute ) {
                if ( $object->getAttributeType($attribute) == '' ) continue;
                if ( $this->IsAttributeInQuery($attribute) ) {
                    $visible = true;
                    break;
                }
                if ( $attribute == $group ) {
                    $visible = true;
                    break;
                }
                if ( in_array($attribute,$sorts) ) {
                    $visible = true;
                    break;
                }
            }
            if ( !$visible ) {
                unset($persisters[$key]);
            }
        }
        $persisters[] = new ObjectAffectedDatePersister();
        return $persisters;
    }

	protected function IsAttributeInQuery( $attribute ) {
		return $this->getColumnVisibility($attribute);
	}

    function getIds()
    {
		$ids = $_REQUEST[strtolower(get_class($this->getObject()))];
		if ( $ids == '' ) {
			$ids = $_REQUEST[$this->getObject()->getIdAttribute()];
		}
        return array_filter(preg_split('/[,\-]/', $ids), function( $value ) {
            return $value != 'all' && is_numeric($value) && $value >= 0;
        });
    }

	function getGroupFilterValue() {
		return '';
	}

	function getGroupIt()
	{
		if ( is_object($this->group_it) ) {
            $this->group_it->moveFirst();
            return $this->group_it;
        }
        return $this->group_it = $this->buildGroupIt();
	}

	function buildGroupIt() {
        if ( $this->getGroup() == '' ) {
            return $this->getObject()->getEmptyIterator();
        }
        if ( !$this->getObject()->IsReference($this->getGroup()) ) {
            return $this->getObject()->getEmptyIterator();
        }

        $group_object = $this->getObject()->getAttributeObject($this->getGroup());
        if ( !is_object($group_object) ) {
            return $this->getObject()->getEmptyIterator();
        }

        $ids = array_filter($this->getIteratorRef()->fieldToArray($this->getGroup()), function($value) {
            return $value != '';
        });
        if ( count($ids) < 1 ) {
            return $this->getObject()->getEmptyIterator();
        }

        $registry = $group_object->getRegistryDefault();
        return $registry->Query(
            array_merge(
                array(
                    new FilterInPredicate(join(',',$ids))
                ),
                $this->getGroupQuery()
            )
        );
    }

    function getGroupQuery() {
        return array();
    }

	function getGroupOrder() {
		$values = $this->getFilterValues();
		$sort_settings = preg_split('/\./', $values['sort']);
		if ( $sort_settings[0] == $this->getGroup() ) {
			return $sort_settings[1] == "" ? "A" : $sort_settings[1];
		}
		return "A";
	}

	function getGroupActions( $ref_it ) {
		$actions = array();
		$method = new ObjectModifyWebMethod($ref_it);
		if ( $method->hasAccess() ) {
			$actions[] = array (
			    'uid' => 'row-modify',
				'name' => translate('Изменить'),
				'url' => $method->getJSCall()
			);
		}
		return $actions;
	}

	function getReferenceIt( $attribute  )
	{
		if ( is_object($this->references_it[$attribute]) ) return $this->references_it[$attribute];
		 
		$object = $this->getObject()->getAttributeObject($attribute);

		if ( !is_object($object) || count($this->iterator_data) < 1 ) {
				return $this->references_it[$attribute] = $this->getObject()->getEmptyIterator();
		}
		
		$data = array_filter($this->iterator_data, function($value) use ($attribute) {
				return $value[$attribute] != '';
		});
		if ( count($data) < 1 ) return $this->references_it[$attribute] = $this->getObject()->getEmptyIterator();

		$ids = array();
		
		foreach( $data as $key => $row ) $ids[] = $row[$attribute];

		$sorts = array();
		if ( $object instanceof MetaobjectStatable ) {
			$sorts[] = new SortAttributeClause('State');
		}
		$sorts[] = new SortReverseKeyClause();

		$registry = $object->getRegistry();
		$registry->setPersisters(array_filter($registry->getPersisters(), function($persister) {
			return is_a($persister, 'StateDetailsPersister')
				|| is_a($persister, 'TestExecutionResultPersister')
				|| is_a($persister, 'TestCaseExecutionResultPersister')
				|| is_a($persister, 'WikiPageDetailsPersister')
                || is_a($persister, 'EntityProjectPersister');
		}));

		return $this->references_it[$attribute] = $object->createCachedIterator(
				$registry->Query(
						array_merge( array(
								new FilterInPredicate(join(',',array_unique($ids))),
						), $sorts)
				)->getRowset()
		);
	}
	
	function getFilteredReferenceIt( $attr, $value )
	{
		$ref_it = $this->getReferenceIt($attr);

		if ( $ref_it->count() < 1 ) return $ref_it;
		 
		$ref_key = $ref_it->getIdAttribute();

		$ref_ids = preg_split('/,/', $value);

		return $ref_it->object->createCachedIterator(
				array_values(array_filter($ref_it->getRowset(), function($value) use ($ref_key, $ref_ids)
				{
						return in_array($value[$ref_key], $ref_ids);
				}))
		);
	}
	
	function getSorts()
	{
		$sorts = array();

		$sort_parms = array( '_group', 'sort', 'sort2', 'sort3', 'sort4' );
		$values = $this->getFilterValues();
		$values['_group'] = $this->getGroup().".".$this->getGroupOrder();

		foreach( $sort_parms as $sort_parm ) {
        	$sort_field = $values[$sort_parm];
		    $sort_attribute = array_shift(preg_split('/\./', $sort_field));

        	if ( $sort_parm == '_group' && $this->getObject()->hasAttribute($sort_attribute) ) {
				$sorts[] = $this->getTable()->getSortAttributeClause( $sort_field );
        	}
        	else {
			    $clause = $this->getTable()->getSortAttributeClause( $sort_field );
			    if ( is_object($clause) ) $sorts[] = $clause;
        	}
		}

		return $sorts;
	}
	
	function Statable( $object = null )
	{
		if ( is_object($object) )
		{
			return is_a($object, 'MetaobjectStatable') && $object->getStateClassName() != '';
		}
		else
		{
			return is_a($this->object, 'MetaobjectStatable') && $object->getStateClassName() != '';
		}
	}
	
	function getPredicates( $filters )
	{
		return is_object($this->getTable()) ? $this->getTable()->getFilterPredicates( $filters ) : array();
	}
	
 	function getStateFilterName()
 	{
 		return 'state';
 	}
 	
	function getFilterValues()
	{
		return $this->getTable()->getFilterValues();
	}
	
	function getFiltersName()
	{
		return $this->getTable()->getFiltersName();
	}

	function getUrl() 
	{
		global $_SERVER;

		$parts = preg_split('/\&/', $_SERVER['QUERY_STRING']);
		
		foreach ( array_keys($parts) as $key )
		{ 
			if ( strpos($parts[$key], 'project=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'offset') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'namespace=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'module=') !== false )
			{
				unset($parts[$key]);
			}
		}
		
		return '?'.join($parts, '&');
	}

	function setTable( $table )
	{
		$this->table = $table;
	}
	
	function getTable()
	{
		return $this->table;
	}

	function drawMenu( $caption, $actions, $style )
	{
		if ( count($actions) > 0 )
		{
			$popup = new PopupMenu();
			$popup->draw('list_row_popup', $caption, $actions);
		}
	}

	function IsNeedToDisplayLinks()
	{
		return false;
	}
	
	function IsNeedNavigator()
	{
		return $this->getPages() > 1;
	}
	
	function IsNeedHeader()
	{
		return true;
	}
	
	function getColumnVisibility( $attr )
	{
		$filter_values = $this->getFilterValues();

		if ( is_numeric($attr) )
		{
			$attr = (string) $attr; 
		}
		
		$hidden = preg_split('/-/', $filter_values['hide'] );
		
		if ( in_array( $attr, $hidden ) ) return false;

		$visible = preg_split('/-/', $filter_values['show'] );
		
		if ( in_array( $attr, $visible ) )
		{
			if ( $attr == 'Password' ) return false;
			
			return $this->getObject()->getAttributeType($attr) != 'password';
		}
		
		if ( $filter_values['hide'] == 'all' ) return false;

		return parent::getColumnVisibility( $attr );
	}

	function getColumnWidth( $attr )
	{
		switch ( $attr )
		{
			case 'UID':
				return '1%';
				
			case 'RecordCreated':
			case 'RecordModified':
			    return '110';
				
	        default:
				return parent::getColumnWidth( $attr );
		}
	}
	
	function getColumnAlignment( $attr ) 
	{
		switch( $attr )
		{
			case 'UID':
				return 'left';
			
			default:
				return parent::getColumnAlignment( $attr );
		}
	}
	
	function getRowClassName( $object_it )
	{
	    return '';
	}
	
	function getHeaderAttributes( $attr )
	{
		$object = $this->getObject();

		$values = $this->getFilterValues();
		
		$parts = preg_split('/\./', $values['sort']);
		
		$sort = $parts[0];
		
		$sort_type = $parts[1];

		if ( !in_array($object->getAttributeDbType($attr), array('', 'COLOR', 'IMAGE')) )
		{
            $classname = "";
			if ( $sort == $attr )
			{
				$classname = $sort_type == 'D' ? "up" : "down";
            }
            $sort_parm = $sort_type == 'D' ? $attr : $attr.'.D';

            return array (
                'class' => $classname,
                'script' => "javascript: filterLocation.setup('group=none',1);filterLocation.setup('sort=".$sort_parm."',1);",
                'name' => $this->getColumnName($attr)
            );
		}
		
		if ( $object->getAttributeDbType($attr) == 'COLOR' )
		{
			return array (
				'class' => "",
				'script' => "#",
				'name' => '<i class="icon-tint"></i>'
			); 
		}
		
		return array (
			'class' => "",
			'script' => "#",
			'name' => $this->getColumnName($attr)
		); 
	}
	
	function getGroupBackground($group_field, $object_it) 
	{
		return '#f8f8f8';
	}

	function drawHeader( $attribute, $title )
	{
		$actions = $this->getHeaderActions($attribute);
		if ( count($actions) < 1 ) {
			parent::drawHeader( $attribute, $title );
		}
		else {
			echo '<div id="context-menu-'.$attribute.'">';
			echo $this->view->render('core/TextMenu.php', array (
				'title' => $title,
				'items' => $actions
			));
			echo '</div>';
		}
	}

	function drawGroupRow($group_field, $object_it, $columns)
	{
		echo '<td colspan="'.$columns.'">';
			$this->drawGroup($group_field, $object_it);
		echo '</td>';
	}
	
	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'State':
                $this->drawCell( $object_it, $group_field );
				break;
				
			default:
				if ( $object_it->get($group_field) == '' )
				{
					echo translate($object_it->object->getAttributeUserName($group_field)).': '.text(2030);
				}
				else
				{
					if ( $object_it->object->IsReference($group_field) )
					{
						$items = array();
						$ref_it = $this->getGroupIt();

						foreach( preg_split('/,/', $object_it->get($group_field)) as $group_id ) {
							$ref_it->moveToId($group_id);
               				$items[] = $this->uid_service->hasUid($ref_it)
               						? $this->uid_service->getUidWithCaption($ref_it) 
               						: $this->uid_service->getUidTitle($ref_it);
						}

                        if ( $ref_it->object instanceof DeadlineSwimlane )
                        {
                            echo $ref_it->getDisplayName();
                            return;
                        }

						$group_title = join($items, ', ');
                        $entityName = $this->getGroupEntityName($group_field, $object_it, $ref_it);
                        if ( $entityName != '' ) {
                            $group_title = $entityName.': '.$group_title;
                        }

						if ( count($items) == 1 && $ref_it->object->getPage() != '?' )
						{
							$actions = get_class($ref_it->object) == get_class($object_it->object)
								? $this->getItemActions('', $ref_it)
								: $this->getGroupActions($ref_it);

							if ( count($actions) > 0 ) {
								echo $this->getTable()->getView()->render('core/RowGroupMenu.php', array (
									'title' => $group_title,
									'items' => $actions,
									'id' => $object_it->getId()
								));
								return;
							}
						}
						echo $group_title;
					}
					else
					{
                        if ( in_array($object_it->object->getAttributeType($group_field), array('date','datetime')) ) {
                            echo getSession()->getLanguage()->getDateFormattedShort($object_it->get($group_field));
                        }
                        else {
                            $this->drawCell( $object_it, $group_field );
                        }
					}
				}
		}
	}

	function getGroupEntityName( $groupField, $object_it, $referenceIt )
    {
        if ( $referenceIt->object instanceof Priority ) return "";
        return translate($object_it->object->getAttributeUserName($groupField));
    }
	
	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'State':
				echo $object_it->getStateIt()->getDisplayName();
				break;

			default:
				foreach( $this->plugins_interceptors as $plugin )
				{
					if ( $plugin->interceptMethodListDrawRefCell( $this, $entity_it, $object_it, $attr ) ) return;
				}
				
				switch ( $entity_it->object->getEntityRefName() )
				{
				    case 'pm_Attachment':
				        
				        $files = array();
				        
				        while( !$entity_it->end() )
				        {
				            $files[] = array (
				                    'type' => $entity_it->IsImage('File') ? 'image' : 'file',
				                    'url' => $entity_it->getFileUrl(),
				                    'name' => $entity_it->getFileName('File'),
				                    'size' => $entity_it->getFileSizeKb('File')
				            );  
				            
				            $entity_it->moveNext();
				        }

				        echo $this->getTable()->getView()->render('core/Attachments.php',
                            array(
                                'files' => $files,
                                'random' => $entity_it->getId()
                            )
                        );
				        
				        break;

				    default:
						$separator = $this->uid_service->hasUid($entity_it) ? ' ' : '<br/>';
                		$ids = $entity_it->idsToArray();

                		$widget_it = $this->getTable()->getReferencesListWidget($entity_it->object);
                		if ( $widget_it->getId() != '' && count($ids) > 1 )
                		{
                            $url = $widget_it->getUrl('filter=skip&'.strtolower(get_class($entity_it->object)).'='.join(',',$ids));
                			$text = count($ids) > 10
                					? str_replace('%1', count($ids) - 10, text(2028))
                					: text(2034);

							$item_it = $entity_it->object->createCachedIterator(array_slice($entity_it->getRowset(),0,10));
                			echo join($separator,$this->getRefNames($item_it, $object_it, $attr));
                			echo ' <a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
                		}
                		else
                		{
                			echo join($separator,$this->getRefNames($entity_it, $object_it, $attr));
                		}
				}
		}
	}

	function drawNumberColumn($object_it, $index)
    {
        $color = $this->getGripColor( $object_it );
        echo '<div class="lst-num-grp">';
            echo '<div class="lst-num">';
                parent::drawNumberColumn($object_it, $index);
            echo '</div>';
            if ( $color != '' ) {
                echo '<div class="lst-grp"><span class="label" style="background-color:'.$color.';">&nbsp;</span></div>';
            }
        echo '</div>';
    }

    function getGripColor( $object_it ) {
        return '';
    }

    protected function getRefNames($entity_it, $object_it, $attr )
	{
		$items = array();
        $baselines_data = $object_it->get($attr.'Baselines');

        if ( $baselines_data == '' )
        {
            $uid_used = $this->uid_service->hasUid($entity_it);
            while ( !$entity_it->end() )
            {
                switch( $entity_it->object->getEntityRefName() ) {
                    case 'pm_Project':
                        $info = $this->uid_service->getUidInfo($entity_it, true);
                        $items[$entity_it->getId()] = '<a href="'.$info['url'].'">'.$info['caption'].'</a>';
                        break;
                    default:
                        if ( $uid_used ) {
                            $text = $this->uid_service->getUidIconGlobal($entity_it, $entity_it->get('VPD') != $object_it->get('VPD'));
                            if ( !$this instanceof PageBoard ) {
                                $text .= '<span class="ref-name">' . $entity_it->getDisplayNameExt() . '<br/></span>';
                            }
                            $items[$entity_it->getId()] = $text;
                        }
                        else {
                            if ( $entity_it->object instanceof Participant ) {
                                $items[$entity_it->getId()] = $entity_it->getRef('SystemUser')->getDisplayName();
                            }
                            else {
                                $editable = $entity_it->object->entity->get('IsDictionary') != 'Y'
                                    && !$entity_it->object instanceof User
                                    && $entity_it->object->getPage() != '?';

                                if ( $editable ) {
                                    $method = new ObjectModifyWebMethod($entity_it);
                                    $items[$entity_it->getId()] = '<a href="'.$method->getJSCall().'">'.$entity_it->getDisplayName().'</a>';
                                }
                                else {
                                    $items[$entity_it->getId()] = $entity_it->getDisplayName();
                                }
                            }
                        }
                }
                $entity_it->moveNext();
            }
        }
        else {
            $baselines = array();
            foreach( preg_split('/,/',$baselines_data) as $info ) {
                list($id, $baseline) = preg_split('/:/', $info);
                $baselines[$id] = $baseline;
            }
            while ( !$entity_it->end() )
            {
                $this->uid_service->setBaseline($baselines[$entity_it->getId()]);
                $text = $this->uid_service->getUidIconGlobal($entity_it, true);
                if ( !$this instanceof PageBoard ) {
                    $text .= '<span class="ref-name">' . $entity_it->getDisplayNameExt() . '<br/></span>';
                }
                $items[$entity_it->getId()] = $text;
                $entity_it->moveNext();
            }
            $this->uid_service->setBaseline('');
        }

		return $items;
	}

    function drawCell( $object_it, $attr )
	{
	    $plugins = getFactory()->getPluginsManager();
	    
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			if ( $plugin->interceptMethodListDrawCell( $this, $object_it, $attr ) )
			{
				return;
			}
		}
	    
		switch ( $attr )
		{
			case 'UID':
				echo $this->uid_service->getUidIconGlobal($object_it, false);

				break;
				
			default:
				switch ( $this->object->getAttributeType($attr) )
			    {
			        case 'text':
			        case 'wysiwyg':

			            drawMore($object_it, $attr);

			            break;
			            
			        case 'date':
			        	
			            if ( $object_it->get($attr) != '' )
			            {
    			    	    $dates = preg_split('/,/', $object_it->get($attr));
    			    	    
            				foreach( $dates as $key => $date )
            				{
            					$dates[$key] = getSession()->getLanguage()->getDateFormattedShort($date);
            				}

            				echo join(', ', $dates);
			            }
        				
        				break;
			            
			        case 'datetime':
			        	
			            if ( $object_it->get($attr) != '' )
			            {
    			    	    $dates = preg_split('/,/', $object_it->get($attr));
    			    	    
            				foreach( $dates as $key => $date )
            				{
            					$dates[$key] = getSession()->getLanguage()->getDateTimeFormatted($date);
            				}

            				echo join(', ', $dates);
			            }
        				
        				break;
        				
        			case 'color':
			        	
			        	echo '<div class="colorPicker-picker colorPicker-list" style="background-color:'.$object_it->get($attr).';"></div>';
			        	
			        	break;

			        case 'integer':
			        	
			        	if ( $object_it->get($attr) != '' )
			        	{
			        		echo number_format(floatval($object_it->get($attr)), 0, ',', ' ');
			        	}
			        	
			        	break;
			        	
			        case 'float':
			        	
			        	if ( $object_it->get($attr) != '' )
			        	{
                            if ( in_array('hours', $this->getObject()->getAttributeGroups($attr)) ) {
                                echo getSession()->getLanguage()->getDurationWording($object_it->get($attr), 24);
                            }
                            else {
                                echo number_format(floatval($object_it->get($attr)), 2, ',', ' ');
                            }
			        	}
			        	
			        	break;

                    case 'file':
                        echo '<a href="'.$object_it->getFileUrl().'">'.$object_it->get('FileExt').'</a>';
                        break;

			        default:
			            
			            parent::drawCell( $object_it, $attr );
			    }
		}
	}

	function drawTotal( $object_it, $attr )
    {
        $this->drawCell( $object_it, $attr );
    }

	function getForm( $object_it )
	{
		return $this->getTable()->getPage()->getFormRef();
	}

	function getHeaderActions( $attribute )
	{
		return array();
	}

	function getItemActions( $column_name, $object_it ) 
	{
	    $actions = array();

        if ( $this->getUidService()->hasUid($object_it) && !$object_it->object instanceof Project ) {
            $actions[] = array (
                'name' => translate('Открыть'),
                'url' => $object_it->getViewUrl()
            );
        }

	    $form = $this->getForm($object_it);
	    
	    if ( !$form instanceof PageForm )
	    {
	    	$plugin_actions = array();
	    	
    		$plugins = getFactory()->getPluginsManager();
			$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
			foreach( $plugins_interceptors as $plugin )
			{
				$plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $object_it ));
			}
	    	
			if ( count($plugin_actions) > 0 )
			{
				array_push($actions, array( '' ) );
				$actions = array_merge( $actions, $plugin_actions );
			}
			
			return $actions;
	    };
	    
	    $form->show($object_it);
	    
	    return array_merge($actions, $form->getActions());
	}
	
	function getActions( $object_it )
	{
		$actions = $this->getItemActions('', $object_it);

	    $form = $this->getForm($object_it);
	    if ( !$form instanceof PageForm ) return $actions;
	    
	    $form->show($object_it);

	    $delete = $form->getDeleteActions();
        if ( count($delete) > 0 ) {
		    $actions = array_merge($actions, array(array()), $delete);
        }
		
        return $actions;
	}
	
	function getMaxOnPage()
	{
		if ( is_object( $this->getTable() ) )
		{
			return $this->getTable()->getRowsOnPage();
		}
		else
		{
			return 999;
		}
	}

	function getGroup() 
	{
	    $values = $this->getFilterValues();
		if ( $values['group'] != '' ) {
			return $values['group'] != 'none'
				? (in_array($values['group'], $this->getAllowedGroupFields()) ? $values['group'] : '')
				: '';
		}
		else {
			return parent::getGroup();
		}
	}
	
	function getGroupFields()
	{
		$object = $this->getObject();
		$fields = array();
		
		$attrs = $object->getAttributes();
		
		foreach ( $attrs as $key => $attr )
		{
			if ( in_array($key, $this->system_attributes) ) continue;
			
			if ( $key == 'OrderNum' ) continue;
			if ( $key != 'State' && !$this->object->IsReference( $key ) ) continue;
			if ( $this->object->getAttributeUserName($key) == '' ) continue;
			
			array_push( $fields, $key );
		}

		return $fields;
	}

	function getAllowedGroupFields() {
		return $this->getGroupFields();
	}
	
	function setupColumns()
	{
		parent::setupColumns();

        $values = $this->getFilterValues();
		$columns = preg_split('/-/', trim($values['show'],'-'));
	    foreach( $columns as $key ) {
			$this->object->setAttributeVisible( $key, true );
		}

	    $table = $this->getTable();
	    
	    $plugins = getFactory()->getPluginsManager();
	    
	    $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($table->getSection()) : array();
	
	    foreach( $plugins_interceptors as $plugin )
	    {
	        $plugin->interceptMethodListSetupColumns( $this );
	    }
	}

	function getColumnFields()
	{
		$object = $this->getObject();
        $group = $this->getGroup();

		$fields = array();
		foreach ( $this->getColumnsRef() as $key )
		{
			if ( $key == 'OrderNum' || $key == 'Password' || $key == $group ) continue;
			if ( $object->getAttributeType($key) == 'password' ) continue;
			if ( $object->getAttributeUserName($key) == '' ) continue;

			array_push( $fields, $key );
		}

		return array_diff($fields, $this->system_attributes);
	}

	function getScrollable() {
		return false;
	}

	function buildFilterActions( & $base_actions )
	{
	    $actions = array();
	
	    $object = $this->getObject();
	    $filter_values = $this->getFilterValues();
	
	    // columns
	    $fields = $this->getColumnFields();
	    $columns = array();

	    foreach ( $fields as $field )
	    {
	        $name = $object->getAttributeUserName($field);
	        
	        $value = $this->getColumnVisibility( $field );
	        	
	        $script = "javascript: $(this).hasClass('checked') ? filterLocation.showColumn('".$field."', 0) : filterLocation.hideColumn('".$field."', 0); ";
	        
	        $columns[translate($name)] = array( 'url' => $script, 'checked' => $value, 'ref' => $field );
	    }
	
	    ksort($columns);
	    $column_actions = array();
	
	    foreach( $columns as $caption => $column )
	    {
	        $column_actions[$column['ref']] = array ( 
                'url' => $column['url'],
                'ref' => $column['ref'],
                'name' => $caption,
                'checked' => $column['checked'],
                'multiselect' => true,
                'uid' => 'column-'.$column['ref']
	        );
	    }
	
	    if ( count($column_actions) > 0 )
	    {
	        $actions['columns'] = array ( 
	            'name' => translate('Столбцы'),
	            'items' => $column_actions , 
	            'title' => '', 
	            'uid' => 'columns' 
	        );
	    }
	
	    // grouping by
	    $used_group = $filter_values['group'];
	    if ( $used_group == '' ) $used_group = $this->getGroup();
	
	    $fields = $this->getGroupFields();
	    if ( count($fields) > 0 )
	    {
	        $groups = array();
	        foreach ( $fields as $name => $field )
	        {
	            $name = is_numeric($name) ? $object->getAttributeUserName($field) : $name;
                $script = "javascript: filterLocation.setup( 'group=".$field."', 0 ); ";
                $groups[translate($name)] = array ( 'url' => $script, 'checked' => $used_group == $field );
	        }
	
	        ksort($groups);
	        $group_actions = array();
	        	
	        foreach ( $groups as $caption => $group )
	        {
	            array_push( $group_actions,
	            array ( 'url' => $group['url'], 'name' => $caption,
	            'checked' => $group['checked'], 'radio' => true )
	            );
	        }
	
	        if ( count($group_actions) > 0 )
	        {
	            $script = "javascript: filterLocation.setup( 'group=none', 0 ); ";
	
	            array_push( $group_actions,
    	            array (),
    	            array ( 'url' => $script, 'name' => translate('Без группировки'),
            	            'checked' => $used_group == 'none', 'radio' => true )
    	        );
    	
	            array_push($actions, array (
    	            'name' => translate('Группировка'),
    	            'items' => $group_actions )
    	        );
	        }
	    }

	    // sort by
	    $sorts = array();
	    $sort_parms = array(
	            'sort' => translate('Поле 1'),
	            'sort2' => translate('Поле 2'),
	            'sort3' => translate('Поле 3'),
	            'sort4' => translate('Поле 4')
	    );
	
	    foreach( $sort_parms as $sort_parm => $sort_title )
	    {
	        $sortcolumns = array();

	        $parts = preg_split('/\./', $filter_values[$sort_parm]);

	        $fields = $this->getTable()->getSortFields();
        
	        foreach ( $fields as $field )
	        {
	            $name = $object->getAttributeUserName($field);
	            
	            $script = "javascript: filterLocation.setSort( '".$sort_parm."', '".$field."' ); ";
	            
	            $sortcolumns[translate($name)] = array(
	                    'url' => $script,
	            		'ref' => $field,
	            		'checked' => $parts[0] == $field
	            );
	        }
	
	        ksort($sortcolumns);
	        $sort_actions = array();
	        	
	        foreach ( $sortcolumns as $caption => $column )
	        {
	            $sort_actions[$column['ref']] = array ( 
	            		'url' => $column['url'], 
	            		'name' => $caption,
            	        'checked' => $column['checked'], 
	            		'radio' => true 
	            );
	        }
	        	
	        if ( count($sort_actions) > 0 )
	        {
	            array_push( $sort_actions,
	                array (),
    	            array ( 'url' => "javascript: filterLocation.setup( '".$sort_parm."=none', 0 ); ",
            	            'name' => translate('Не задана'), 'radio' => true,
            	            'checked' => $parts[0] == 'none' || $parts[0] == '' ),
            	    array (),
            	    array ( 'url' => "javascript: filterLocation.setSortType( '".$sort_parm."', 'asc' ); ",
            	            'name' => translate('По возрастанию'), 'checked' => $parts[1] == 'A' || $parts[1] == '', 
            	            'uid' => $sort_parm.'-a',
            	            'radio-group' => 'direction',
            	            'radio' => true ),
	                array ( 'url' => "javascript: filterLocation.setSortType( '".$sort_parm."', 'desc' ); ",
            	            'name' => translate('По убыванию'), 'checked' => $parts[1] == 'D', 
            	            'uid' => $sort_parm.'-d',
            	            'radio-group' => 'direction',
            	            'radio' => true )
	            );
	
	            $sorts[$sort_parm] = array ( 
	            		'name' => $sort_title,
            	        'items' => $sort_actions 
	            );
	        }
	    }
	
	    if ( count($sorts) > 0 )
	    {
	        $actions['sorts'] = array ( 
        		'name' => translate('Сортировка'),
        		'items' => $sorts
	        );
	    }
	
	    // rows number
	    if ( $this->HasRows() )
	    {
	        if ( $_REQUEST['rows'] == '' ) $_REQUEST['rows'] = $this->getMaxOnPage();

	        $rows_actions = array();
	        $rows = array( 5, 20, 60, 100 );
	        	
	        foreach ( $rows as $value )
	        {
	            $checked = $_REQUEST['rows'] == $value;
	
	            $script = "javascript: filterLocation.setup( 'rows=".$value."', 0 ); ";
	            	
	            array_push( $rows_actions,
	            array ( 'url' => $script, 'name' => $value.' '.translate('строк'),
	            'checked' => $checked, 'radio' => true )
	            );
	        }
	        	
	        $script = "javascript: filterLocation.setup( 'rows=all', 0 ); ";
	
	        $rows_actions[] =
				array (
					'url' => $script,
					'name' => translate('Все строки'),
					'checked' => in_array($_REQUEST['rows'], array('999','all')), 'radio' => true,
				);

	        $actions['rows'] = array (
				'name' => translate('Количество строк'),
				'items' => $rows_actions,
				'uid' => 'rows'
	        );
	    }
	
	    if ( $base_actions[0]['name'] == '' )
	    {
	        $base_actions = array_merge( $actions, $base_actions );
	    }
	    else
	    {
	        $base_actions = array_merge(
	                array_slice($base_actions, 0, 1),
	                $actions,
	                array_slice($base_actions, 1, count($base_actions) - 1)
	        );
	    }
	}
	
	function buildFilterColumnsGroup( & $actions, $group )
	{
	    $trace_attributes = $this->getObject()->getAttributesByGroup($group);
        if ( $group == 'trace' ) {
            $trace_attributes = array_merge($trace_attributes, $this->getObject()->getAttributesByGroup('source-attribute'));
        }
	    
	    if ( count($trace_attributes) < 1 ) return;
	    
	    $attribute_group = new AttributeGroup();
	    $group_it = $attribute_group->getExact($group);
	    
	    $this->adjustFilterColumns( $actions['columns'], $group, $trace_attributes, $group_it->getDisplayName() );
        if ( count($actions['sorts']) > 0 ) {
            $this->adjustFilterColumns( $actions['sorts']['items']['sort'], $group, $trace_attributes, $group_it->getDisplayName() );
            $this->adjustFilterColumns( $actions['sorts']['items']['sort2'], $group, $trace_attributes, $group_it->getDisplayName() );
            $this->adjustFilterColumns( $actions['sorts']['items']['sort3'], $group, $trace_attributes, $group_it->getDisplayName() );
            $this->adjustFilterColumns( $actions['sorts']['items']['sort4'], $group, $trace_attributes, $group_it->getDisplayName() );
        }
	}
	
	protected function adjustFilterColumns( & $actions, $group, & $trace_attributes, $name )
	{
	    $trace_columns = array();
	    
	    foreach( $actions['items'] as $key => $column )
	    {
	        if ( is_numeric($key) || !in_array($key, $trace_attributes) ) continue;
	        
            $trace_columns[$key] = $column;
            
            unset($actions['items'][$key]);
	    }
	    
	    if ( count($trace_columns) < 1 ) return;

	    $actions['items'] = array_merge( array(
	            $group => array (
        	        'name' => $name,
        	        'items' => $trace_columns, 
        	        'uid' => $group
        	    ),
	            $group.'-' => array()
	            ),
	            $actions['items'] );
	}

	function retrieve()
    {
        $this->extendModel();
        return parent::retrieve();
    }

    function getRenderParms()
	{
		$form = $this->getTable()->getPage()->getFormRef();
	    if ( $form instanceof PageForm ) $form->setRedirectUrl($_SERVER['REQUEST_URI']);

		$it = $this->getIteratorRef();
        $this->shiftNextPage($it, $this->getOffset());
		list($sort_field, $sort_type) = $this->getSortingParms();
		
		return array(
			'list' => $this,
			'object' => $this->getObject(),
			'offset_name' => $this->getOffsetName(),
			'offset' => $this->getOffset(),
			'table_id' => $this->getId(),
			'no_items_message' => $this->getNoItemsMessage(),
			'it' => $it,
			'display_numbers' => $this->IsNeedToDisplayNumber(),
			'display_operations' => $this->IsNeedToDisplayOperations(),
			'numbers_column_width' => $this->getNumbersColumnWidth(),
			'need_to_select' => $this->IsNeedToSelect(),
			'columns' => $this->getColumnsRef(),
			'rows_num' => min($this->getItemsCount($it) - $this->getOffset(), $this->getMaxOnPage()),
			'group_field' => $this->getGroup(),
			'groups' => $this->getGroupFields(),
		    'table_class_name' => 'table table-hover wishes-table',
		    'list_mode' => $this->list_mode,
			'created_datetime' => SystemDateTime::date(),
			'scrollable' => $this->getScrollable(),
			'reorder' => false,
			'sort_field' => $sort_field,
			'sort_type' => $sort_type,
			'show_header' => $this->IsNeedHeader(),
			'autorefresh' => getFactory()->getAccessPolicy()->can_read($this->getObject()),
		);
	}
	
	function getSortingParms()
	{
		foreach( $this->getSorts() as $clause )
		{
			if ( !$clause instanceof SortAttributeClause ) continue;
			
			if ( $clause->getAttributeName() == $this->getGroup() ) continue;
			
			$sort_clause = $clause;
			
			break;
		}
		
		return array( 
				is_object($sort_clause) ? $sort_clause->getAttributeName() : '',
				is_object($sort_clause) ? strtolower($sort_clause->getSortType()) : ''
		);
	}
	
	function getRenderView()
	{
	    return $this->view;
	}
	
	function getTemplate()
	{
	    return "core/PageList.php";
	}
	
	function render( $view, $parms )
	{
	    $this->view = $view;

		echo $view->render( $this->getTemplate(), array_merge($parms, $this->getRenderParms()) );

		unset($this->view);
		$this->view = null;
	}
}
