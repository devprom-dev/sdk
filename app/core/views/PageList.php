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
    private $group = null;
    private $skipIvisiblePersisters = true;
    private $itemsHash = '';

 	function PageList( $object )
 	{
 		parent::__construct( $object );

        $this->uid_service = new ObjectUID('', $object);

		$plugins = getFactory()->getPluginsManager();
		$this->plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
 	}
 	
  	function __destruct()
 	{
 		$this->view = null;
 		$this->table = null;
 	}
 	
 	function buildSystemAttributes() {
	    return $this->getObject()->getAttributesByGroup('system');
 	}

	function extendModel()
	{
		$object = $this->getObject();

		if ( !in_array($object->getAttributeType('UID'), array('','integer')) ) {
			$object->setAttributeOrderNum('UID', 0);
		}
		else if ( $this->uid_service->hasUidObject( $object ) ) {
			$object->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
		}

        $plugins = getFactory()->getPluginsManager();
        $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getTable()->getSection()) : array();
        foreach( $plugins_interceptors as $plugin ) {
            $plugin->interceptMethodListSetupColumns( $this );
        }

        $this->system_attributes = $this->buildSystemAttributes();
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

    function getFilterValues() {
 	    return $this->getTable()->getFilterValues();
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

        $filters = $this->getTable()->getPredicateFilterValues();
        $predicates = array_merge(
            $this->getPredicates( $filters ),
            $object->getFilters()
        );
        $predicates[] = new FilterVpdPredicate();

        $plugins = getFactory()->getPluginsManager();
        $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getTable()->getSection()) : array();
        foreach( $plugins_interceptors as $plugin ) {
            $plugin->interceptMethodListGetPredicates( $this, $predicates, $filters );
        }

        $baseSorts = $this->getSorts();
        $sorts = array();
        foreach( $baseSorts as $sort ) {
            if ( $sort instanceof SortAttributeClause ) {
                $sorts[] = $sort->getAttributeName();
            }
        }

        $persisters = $this->getPersisters($object, $sorts);
        $sorts = $baseSorts;

        $registry = $object->getRegistry();
        $registry->setPersisters(array());
        $registry->setSorts(array());

        $ids = $this->getIds($filters);
        if ( count($ids) > 0 ) {
            $predicates[] = new FilterInPredicate($ids);
        }

        $this->itemsCount = $this->buildItemsCount($registry, $predicates);
        if ($this->itemsCount > 0) {
            $limit = $this->getMaxOnPage();
            if (is_numeric($limit) && $limit > 0) {
                $registry->setLimit($limit > 1 ? $limit : 99999);
                $offset = $this->getOffset();
                if (is_numeric($offset) && $offset > 0) {
                    $registry->setOffset($offset);
                }
            }
        }

        if ( $this->IsNeedToSelect() ) {
            $this->itemsHash = $this->buildItemsHash($object, $predicates);
        }

        return $registry->Query(
            array_merge(
                $predicates, $sorts, $persisters
            )
        );
    }

    function buildItemsHash($object, $predicates) {
 	    return \TextUtils::buildIds(
            $object->getRegistryBase()->Query(
                $predicates
            )->idsToArray()
        );
    }

    function getItemsHash() {
 	    return $this->itemsHash;
    }

    function buildItemsCount($registry, $predicates) {
        return $registry->Count($predicates);
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
            if ( !$this->skipIvisiblePersisters ) continue;
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

	protected function IsAttributeInQuery( $attribute )
    {
        if ( $this->getColumnVisibility($attribute) ) return true;
        $attributeCaption = $this->getObject()->getAttributeUserName($attribute);
        foreach( $this->getObject()->getAttributesByGroup('computed') as $computedAttribute ) {
            if ( strpos($this->getObject()->getDefaultAttributeValue($computedAttribute), $attributeCaption) !== false ) return true;
        }
		return false;
	}

    function getIds( $values )
    {
        $keyFilter = strtolower(get_class($this->getObject()));
		$ids = $_REQUEST[$keyFilter];
		if ( $ids == '' ) {
			$ids = $_REQUEST[$this->getObject()->getIdAttribute()];
		}
        if ( $ids == '' ) {
            $ids = $_REQUEST['ids'];
        }
        if ( $ids == '' ) {
            $ids = $values[$keyFilter];
        }
        return \TextUtils::parseIds($ids);
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

	function getGroupObject() {
 	    return $this->getObject()->getAttributeObject($this->getGroup());
    }

	function buildGroupIt() {
        if ( $this->getGroup() == '' ) {
            return $this->getObject()->getEmptyIterator();
        }
        if ( !$this->getObject()->IsReference($this->getGroup()) ) {
            return $this->getObject()->getEmptyIterator();
        }

        $group_object = $this->getGroupObject();
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

    function getGroupQuery()
    {
        $values = $this->getTable()->getFilterValues();
        if ( $values['sortgroup'] != '' ) {
            return array(
                new SortAttributeClause($values['sortgroup'])
            );
        }
        return array();
    }

	function getGroupActions( $ref_it )
    {
		$actions = array();

		$method = new ObjectModifyWebMethod($ref_it);
		if ( $method->hasAccess() ) {
			$actions[] = array (
			    'uid' => 'row-modify',
				'name' => $method->getCaption(),
				'url' => $method->getJSCall()
			);
		}

		$values = $this->getTable()->getFilterValues();
		$parts = preg_split('/\./', $values['sortgroup']);

        $sortActions = array();
        $sortAttributes = array_diff(
            array_keys($ref_it->object->getAttributes()),
            $ref_it->object->getAttributesByGroup('system')
        );

        foreach ( $sortAttributes as $attribute )
        {
            if ( $ref_it->object->getAttributeDbType($attribute) == '' ) continue;
            if ( !$ref_it->object->IsAttributeStored($attribute) ) continue;

            $type = $ref_it->object->getAttributeType($attribute);
            if ( in_array($type, array('text','wysiwyg','password','file', 'char')) ) continue;

            $name = $ref_it->object->getAttributeUserName($attribute);

            $sortActions[translate($name)] = array(
                'name' => translate($name),
                'url' => "javascript: filterLocation.setSort( 'sortgroup', '".$attribute."' ); ",
                'ref' => $attribute,
                'checked' => $parts[0] == $attribute
            );
        }
        ksort($sortActions);

        if ( count($sortActions) > 0 )
        {
            array_push( $sortActions,
                array (),
                array ( 'url' => "javascript: filterLocation.setSortType( 'sortgroup', 'asc' ); ",
                    'name' => translate('По возрастанию'), 'checked' => $parts[1] == 'A' || $parts[1] == '',
                    'uid' => 'sortgroup-a',
                    'radio-group' => 'direction',
                    'radio' => true ),
                array ( 'url' => "javascript: filterLocation.setSortType( 'sortgroup', 'desc' ); ",
                    'name' => translate('По убыванию'), 'checked' => $parts[1] == 'D',
                    'uid' => 'sortgroup-d',
                    'radio-group' => 'direction',
                    'radio' => true )
            );

            $actions = array_merge(
                $actions,
                array(
                    array(),
                    array(
                        'name' => translate('Сортировка'),
                        'items' => $sortActions
                    )
                )
            );
        }

		return $actions;
	}

	function getReferenceIt( $attribute  )
	{
		if ( is_object($this->references_it[$attribute]) ) return $this->references_it[$attribute]->copyAll();
		 
		$object = $this->getObject()->getAttributeObject($attribute);

		if ( !is_object($object) || count($this->iterator_data) < 1 ) {
				return $this->references_it[$attribute] = $this->getObject()->getEmptyIterator();
		}
		
		$data = array_filter($this->iterator_data, function($value) use ($attribute) {
				return $value[$attribute] != '';
		});
		if ( count($data) < 1 ) {
		    if ( is_object($object) ) {
		        return $this->references_it[$attribute] = $object->getEmptyIterator();
            }
		    return $this->references_it[$attribute] = $this->getObject()->getEmptyIterator();
        }

		$ids = array();
		
		foreach( $data as $key => $row ) $ids[] = $row[$attribute];

		$sorts = array();
		if ( $object instanceof MetaobjectStatable ) {
			$sorts[] = new SortAttributeClause('State');
		}
		$sorts[] = array_merge($sorts, $object->getSortDefault());

		$registry = $object->getRegistry();
		$registry->setPersisters(array_filter($registry->getPersisters(), function($persister) {
			return is_a($persister, 'TestExecutionResultPersister')
				|| is_a($persister, 'TestCaseExecutionResultPersister')
				|| is_a($persister, 'WikiPageDetailsPersister')
                || is_a($persister, 'EntityProjectPersister')
                || $persister->IsPersisterImportant();
		}));

		$filterValues = $this->getTable()->getPredicateFilterValues();
		if ( $filterValues[$attribute] != '' ) {
            $sorts = array_merge( $sorts,
                $this->getTable()->getFilterPredicates(\TextUtils::parseAttributeFilter($filterValues[$attribute]))
            );
        }

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

		$sort_parms = array( 'sort', 'sort2', 'sort3', 'sort4' );
		$values = $this->getTable()->getFilterValues();

		if ($_REQUEST['mode'] != 'children' ) {
            array_unshift($sort_parms, '_group');
        }

        $groupSort = $this->getGroupSort();
        if ( $groupSort == '' ) $groupSort = 'A';
        $values['_group'] = $this->getGroup().".".$groupSort;

		foreach( $sort_parms as $sort_parm )
		{
        	$sort_field = $values[$sort_parm];
		    $sort_attribute = array_shift(preg_split('/\./', $sort_field));

        	if ( $sort_parm == '_group' && $this->getObject()->hasAttribute($sort_attribute) ) {
        	    $clause = $this->getTable()->getSortAttributeClause( $sort_field );
        	    if ( is_object($clause) && $values['sortgroup'] != '' ) {
                    $clause->setReferenceSorts($sort_attribute, array(new SortAttributeClause( $values['sortgroup'] )));
                }
				$sorts[] = $clause;
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
	
	function getFiltersName()
	{
		return $this->getTable()->getFiltersName();
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
		return true;
	}

	function IsNeedHeader()
	{
		return $_REQUEST['dashboard'] == '';
	}
	
	function getColumnVisibility( $attr )
	{
		$filter_values = $this->getTable()->getFilterValues();

		if ( is_numeric($attr) ) {
			$attr = (string) $attr; 
		}

		$hidden = preg_split('/-/', $filter_values['hide'] );
		if ( in_array( $attr, $hidden ) ) return false;

		$visible = preg_split('/-/', $filter_values['show'] );
		if ( in_array( $attr, $visible ) ) {
			if ( $attr == 'Password' ) return false;
			return in_array($attr, $this->getColumnsRef());
		}

		if ( count($visible) > 0 && $filter_values['show'] != '' ) return false;
        if ( $filter_values['hide'] == 'all' ) return false;

		return parent::getColumnVisibility( $attr );
	}

	function getColumnWidth( $attr )
	{
		switch ( $attr )
		{
			case 'UID':
            case 'OrderNum':
                return '1%';

			case 'RecordCreated':
			case 'RecordModified':
			    return '130';
				
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
			    switch( $this->getObject()->getAttributeType($attr) ) {
                    case 'float':
                    case 'integer':
                        return 'right';
                    default:
                        return parent::getColumnAlignment( $attr );
                }
		}
	}
	
	function getRowClassName( $object_it )
	{
	    return '';
	}
	
	function getHeaderAttributes( $attr )
	{
		$object = $this->getObject();
		$className = $this->getColumnAlignment($attr);
		$values = $this->getTable()->getFilterValues();
		
		$parts = preg_split('/\./', $values['sort']);
		$sort = $parts[0];

		$sort_type = $parts[1];

		if ( !in_array($object->getAttributeDbType($attr), array('', 'COLOR', 'IMAGE')) )
		{
            if ( $sort_type == '' || $sort != $attr ) {
                $sort_parm = $attr.'.A';
            }
            else {
                $sort_parm = $sort_type == 'D' ? $attr.'.A' : $attr.'.D';
            }

            return array (
                'class' => $className,
                'sort' => $sort == $attr ? ($sort_type == 'D' ? "up" : "down") : '',
                'script' => "javascript: filterLocation.setup('group=none',1);filterLocation.setup('sort=".$sort_parm."',1);",
                'name' => $this->getColumnName($attr)
            );
		}
		
		if ( $object->getAttributeDbType($attr) == 'COLOR' )
		{
			return array (
				'class' => $className,
				'script' => "#",
				'name' => '<i class="icon-tint"></i>'
			); 
		}
		
		return array (
			'class' => $className,
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

	function drawGroupRow($group_field, $group_field_value, $object_it, $columns, $guid)
	{
		echo '<td colspan="'.$columns.'" class="row-clmn">';
		    echo '<div class="plus-minus-toggle" data-toggle="collapse" href="#gor' . $guid . '"></div>';
			$this->drawGroup($group_field, $object_it);

            if ( !$object_it->object->IsReference($group_field) || $group_field_value == '' ) {
                echo '<a class="btn btn-check btn-transparent" href="javascript: checkGroupTrue(\'' . $group_field_value . '\')"><i class="icon-check"></i></a>';
            }
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

							array_unshift($actions, array(
							   'name' => translate('Выбрать все'),
                               'url' => "javascript: checkGroupTrue('" . $object_it->get($group_field) . "')"
                            ));

                            echo $this->getRenderView()->render('core/RowGroupMenu.php', array (
                                'title' => $group_title,
                                'items' => $actions,
                                'id' => $object_it->getId()
                            ));
							return;
						}
						echo $group_title;
					}
					else
					{
                        if ( in_array($object_it->object->getAttributeType($group_field), array('date','datetime')) ) {
                            echo $object_it->getDateFormattedShort($group_field);
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
        if ( $referenceIt->object->getEntityRefName() == 'entity' ) {
            return $object_it->object->getAttributeUserName($groupField);
        }
        else {
            return $referenceIt->object->getDisplayName();
        }
    }
	
	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'State':
			    if ( $this->getObject() instanceof MetaobjectStatable ) {
                    echo $object_it->getStateIt()->getDisplayName();
                }
                else {
			        parent::drawRefCell($entity_it, $object_it, $attr);
                }
				break;

			default:
				foreach( $this->plugins_interceptors as $plugin ) {
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

				        echo $this->getRenderView()->render('core/Attachments.php',
                            array(
                                'files' => $files,
                                'random' => $entity_it->getId()
                            )
                        );
				        
				        break;

				    default:
                		$ids = $entity_it->idsToArray();

                        echo '<span class="tracing-ref" entity="'.get_class($entity_it->object).'">';
                            $widget_it = $this->getTable()->getReferencesListWidget($entity_it, $attr);
                            if ( $widget_it->getId() != '' && count($ids) > 1 )
                            {
                                $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($entity_it, 'ids', $widget_it);
                                $text = count($ids) > 10
                                            ? str_replace('%1', count($ids) - 10, text(2028))
                                            : text(2034);

                                $item_it = $entity_it->object->createCachedIterator(array_slice($entity_it->getRowset(),0,10));

                                echo '<span>'.join('</span> <span>',$this->getRefNames($item_it, $object_it, $attr)).'</span>';
                                echo ' <a class="dashed" target="_blank" href="'.$url.'">'.$text.'</a>';
                            }
                            else {
                                $item_it = $entity_it->object->createCachedIterator(array_slice($entity_it->getRowset(),0,30));
                                echo '<span>'.join('</span> <span>',$this->getRefNames($item_it, $object_it, $attr)).'</span>';
                            }
                        echo '</span>';
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

    public function getRefNames($entity_it, $object_it, $attr )
	{
		$items = array();
        $baselines_data = $object_it->get($attr.'Baselines');

        if ( !is_object($entity_it->object->entity) ) return $items;

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
                                $text .= '<span class="ref-name">' . $entity_it->getDisplayNameExt() . '</span>';
                            }
                            $items[$entity_it->getId()] = $text;
                        }
                        else {
                            if ( $entity_it->object instanceof Participant ) {
                                $items[$entity_it->getId()] = $entity_it->getRef('SystemUser')->getDisplayName();
                            }
                            else {
                                if ( $entity_it->object->getPage() == '?' || $entity_it->object instanceof User ) {
                                    $items[$entity_it->getId()] = $entity_it->getDisplayName();
                                }
                                else {
                                    if ( $entity_it->object->getPageFormPopup() ) {
                                        $method = new ObjectModifyWebMethod($entity_it);
                                        $items[$entity_it->getId()] = '<a href="'.$method->getJSCall().'">'.$entity_it->getDisplayName().'</a>';
                                    }
                                    else {
                                        $items[$entity_it->getId()] = '<a target="_blank" href="'.$entity_it->getUidUrl().'">'.$entity_it->getDisplayName().'</a>';
                                    }
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
                    $text .= '<span class="ref-name">' . $entity_it->getDisplayNameExt('', $baselines[$entity_it->getId()]) . '</span>';
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
        $groups = $this->getObject()->getAttributeGroups($attr);

        $plugins = getFactory()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		foreach( $plugins_interceptors as $plugin ) {
			if ( $plugin->interceptMethodListDrawCell( $this, $object_it, $attr ) ) {
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
			        case 'date':
			            if ( $object_it->get($attr) != '' )
			            {
    			    	    $dates = preg_split('/,/', $object_it->get($attr));
            				foreach( $dates as $key => $date ) {
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

                    case 'HoursProgress':
                        if ( in_array('percentage', $groups) ) {
                            if ( $object_it->get($attr) != '' ) {
                                echo $object_it->get($attr) . '%';
                            }
                        }
                        else {
                            if ( $object_it->get($attr) != '' ) {
                                echo number_format(floatval($object_it->get($attr)), 0, ',', ' ');
                            }
                        }
			        	break;
			        	
			        case 'float':
			        	if ( $object_it->get($attr) != '' )
			        	{
                            if ( in_array('hours', $groups) ) {
                                echo getSession()->getLanguage()->getHoursWording($object_it->get($attr));
                            }
                            elseif ( in_array('astronomic-time', $groups) ) {
                                echo getSession()->getLanguage()->getDurationWording($object_it->get($attr));
                            }
                            elseif ( in_array('working-time', $groups) ) {
                                echo getSession()->getLanguage()->getDurationWording($object_it->get($attr), 8);
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

	function getForm($object_it)
	{
		return $this->getTable()->getPage()->getFormRef();
	}

	function getHeaderActions( $attribute )
	{
		return array();
	}

    function getItemClass($it) {
 	    return get_class($it->object);
    }

	function getItemActions( $column_name, $object_it ) 
	{
	    $actions = array();

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
        $actions = array_merge($actions, $form->getActions());

        if ( $this->getUidService()->hasUid($object_it) && !$object_it->object instanceof Project ) {
            if ( $actions['modify']['name'] != translate('Открыть') ) {
                array_unshift($actions,
                    array (
                        'name' => translate('Открыть'),
                        'url' => $object_it->getUidUrl()
                    )
                );
            }
        }

        return $actions;
    }
	
	function getActions( $object_it )
	{
		$actions = $this->getItemActions('', $object_it);

	    $form = $this->getForm($object_it);
	    if ( !$form instanceof PageForm ) return $actions;
	    
	    $form->show($object_it);

	    $delete = $form->getDeleteActions($object_it);
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
			return defined('MAX_LIST_ITEMS') ? MAX_LIST_ITEMS : 1024;
		}
	}

	function getGroup() 
	{
	    if ( $this->group ) return $this->group;
	    return $this->group = $this->buildGroup();
	}

	function buildGroup()
    {
        $values = $this->getTable()->getFilterValues();
        if ( $values['group'] != '' ) {
            $values['group'] = array_shift(preg_split('/\./', $values['group']));
            return $values['group'] != 'none'
                ? (in_array($values['group'], $this->getAllowedGroupFields()) ? $values['group'] : '')
                : '';
        }
        else {
            return $this->getGroupDefault();
        }
    }

	function getGroupSort()
    {
        $values = $this->getTable()->getFilterValues();
        $sort = array_pop(preg_split('/\./', $values['group']));
        return in_array($sort, array('A','D')) ? $sort : '';
    }
	
	function getGroupFields()
	{
		$object = $this->getObject();
		$fields = array();
		$skipTypes = array('datetime', 'wysiwyg', 'text', 'largetext');
		
		$attrs = $object->getAttributes();

		foreach ( $attrs as $key => $attr )
		{
			if ( in_array($key, $this->system_attributes) ) continue;
            if ( $key != 'State' && in_array($this->object->getAttributeType($key), $skipTypes) ) continue;

			if ( $key == 'OrderNum' ) continue;
			if ( $key != 'State' && !$this->object->IsReference( $key ) && $object->getAttributeOrigin($key) != ORIGIN_CUSTOM ) continue;

			$title = $this->object->getAttributeUserName($key);
			if ( $title == '' ) continue;

            $fields[$title] = $key;
		}

		return $fields;
	}

	function getAllowedGroupFields() {
		return $this->getGroupFields();
	}
	
	function getColumnFields()
	{
		$object = $this->getObject();
        $skipFields = array('Password', 'TransitionComment');

		$fields = array();
		foreach ( array_keys($this->getObject()->getAttributes()) as $key )
		{
			if ( in_array($key, $skipFields) ) continue;
			if ( $object->getAttributeType($key) == 'password' ) continue;
			if ( $object->getAttributeUserName($key) == '' ) continue;

			array_push( $fields, $key );
		}

		return array_diff($fields, $this->system_attributes);
	}

	function getScrollable() {
		return false;
	}

	function getSettingsViewParms()
    {
        $parms = array();

        $columns = array();
        $visible = array();
        foreach ( $this->getColumnsRef() as $field ) {
            $value = PageList::getColumnVisibility( $field );
            if ( $value ) $visible[] = $field;
            $columns[$field] = $this->getObject()->getAttributeUserName($field);
        }
        uasort($columns, function($left, $right) {
            return $left > $right;
        });

        $parms['show'] = array(
            'attribute' => 'multiple multicolumn',
            'name' => text(2919),
            'options' => $columns,
            'value' => $visible
        );

        $filter_values = $this->getTable()->getFilterValues();

        $columns = array(
            'none' => translate('<нет значения>')
        );
        foreach ( $this->getGroupFields() as $title => $field ) {
            if ( is_numeric($title) ) $title = $this->getObject()->getAttributeUserName($field);
            $columns[$field] = $title;
        }
        uasort($columns, function($left, $right) {
            return $left > $right;
        });

        $group = $filter_values['group'];
        if ( $group == '' ) $group = $this->getGroup();

        $sorts = explode('.',$group);
        $sortValue = count($sorts) > 1 ? array_pop($sorts) : 'A';

        $parms['group'] = array(
            'attribute' => 'sort-order ' . (count($columns) > 3 ? 'multicolumn' : ''),
            'name' => translate('Группировка'),
            'options' => $columns,
            'value' => array_shift(explode('.',$group)),
            'sort-value' => $sortValue
        );

        $columns = array(
            'none' => translate('<нет значения>')
        );
        foreach ( $this->getTable()->getSortFields() as $field ) {
            $columns[$field] = $this->getObject()->getAttributeUserName($field);
        }
        uasort($columns, function($left, $right) {
            return $left > $right;
        });

        foreach( array('sort', 'sort2', 'sort3', 'sort4') as $key => $sortParm ) {
            $sorts = explode('.',$filter_values[$sortParm]);
            $sortValue = count($sorts) > 1 ? array_pop($sorts) : 'A';
            $parms[$sortParm] = array(
                'attribute' => 'sort-order ' . (count($columns) > 3 ? 'multicolumn' : ''),
                'name' =>  translate('Сортировка') . ' ' . ($key + 1),
                'options' => $columns,
                'value' => array_shift(explode('.',$filter_values[$sortParm])),
                'sort-value' => $sortValue
            );
        }

        return $parms;
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
			'itemsHash' => $this->itemsHash,
			'created_datetime' => SystemDateTime::date(),
			'scrollable' => $this->getScrollable(),
            'draggable' => $this->getDraggable(),
			'reorder' => false,
			'sort_field' => $sort_field,
			'sort_type' => $sort_type,
			'sorts' => $this->getTable()->getSortFields(),
			'show_header' => $this->IsNeedHeader(),
			'autorefresh' => getFactory()->getAccessPolicy()->can_read($this->getObject()),
            'groupAttribute' => $this->getGroup()
		);
	}

	function getDraggable() {
        return $this->getObject()->hasAttribute('OrderNum');
    }

	function getSortingParms()
	{
        $values = $this->getTable()->getFilterValues();
        list($field, $order) = preg_split('/\./', $values['sort']);
        $order = $order == '' ? 'asc': ($order == 'D' ? 'desc' : 'asc');
        return array($field, $order);
	}
	
	function getRenderView() {
	    return $this->view;
	}

	function setRenderView( $view ) {
        $this->view = $view;
    }

	function getTemplate()
	{
	    return "core/PageList.php";
	}
	
	function render( $view, $parms )
	{
	    $this->setRenderView($view);
		echo $view->render( $this->getTemplate(), array_merge($parms, $this->getRenderParms()) );
	}

	function skipInvisiblePersisters( $flag ) {
 	    $this->skipIvisiblePersisters = $flag;
    }

    function drawFooter() {
    }
}
