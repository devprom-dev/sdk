<?php

define ('GROUP_STYLE_ROW', 'row');
define ('GROUP_STYLE_COLUMN', 'column');

class PageBoard extends PageList
{
    var $view;
    
    private $board_attribute_iterator = null;
	private $column_descriptions = array();
    private $plugins = null;
	private $uid_visible = true;
	private $maxCellVisibleItems = 0;

 	function __construct( $object )
	{
		parent::__construct( $object );

		$plugins = getFactory()->getPluginsManager();
		$this->plugins = is_object($plugins) 
				? $plugins->getPluginsForSection(getSession()->getSite()) : array();

        $this->maxCellVisibleItems = defined('BOARD_MAX_CELL_ITEMS') ?  BOARD_MAX_CELL_ITEMS : 30;
	}

	function setTable( $table )
	{
		parent::setTable( $table );
		
		$table->resetFilterValues();
	}

	function extendModel()
	{
		parent::extendModel();
		$object = $this->getObject();

		foreach( $object->getAttributes() as $attribute => $info ) {
			if ( $object->getAttributeType($attribute) == 'datetime' ) {
				$object->setAttributeType($attribute, 'date');
			}
		}
        $object->addAttribute( 'Basement', '', '', false, false, '', 99999 );
	}

    function getColumnFields()
    {
        return array_merge(
            parent::getColumnFields(),
            array(
                'Basement'
            )
        );
    }

    function getOffset()
    {
        return 0;
    }

	function getBoardAttribute()
 	{
 		return 'State';
 	}

    function getBoardAttributeFilter()
    {
        return 'state';
    }

 	function buildBoardAttributeIterator()
 	{
        $state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
        if ( $this->getTable()->hasCrossProjectFilter() ) {
            if ( $this->hasCommonStates() ) {
                $vpds = $this->getProjectIt()->fieldToArray('VPD');
                return $state_it->object->getRegistry()->Query(
                    array (
                        new FilterVpdPredicate(array_shift(array_values($vpds))),
                        new StateQueueLengthPersister(array(), $vpds),
                        new SortAttributeClause('OrderNum')
                    )
                );
            }
            else {
                $metastate = getFactory()->getObject('StateMeta');
                $metastate->setAggregatedStateObject($state_it->object);
                return $metastate->getRegistry()->Query(array());
            }
        }
        else {
            $project = getSession()->getProjectIt()->getId();
            return $state_it->object->createCachedIterator(
                array_values(array_filter($state_it->getRowset(), function($row) use ($project) {
                    return $row['Project'] == $project;
                }))
            );
        }
 	}
 	
 	function getBoardAttributeIterator()
 	{
 		if ( is_object($this->board_attribute_iterator) ) {
 			return $this->board_attribute_iterator->copyAll();
 		}

		$it = $this->buildBoardAttributeIterator();
		while( !$it->end() ) {
			$this->column_descriptions[$it->get('ReferenceName')] = $it->getHtmlDecoded('Description');
			$it->moveNext();
		}

		$it->moveFirst();
 		return $this->board_attribute_iterator = $it;
 	}

 	function getBoardStates()
 	{
 		$attribute_it = $this->getBoardAttributeIterator();

 		if ( $attribute_it->getId() < 1 ) return array();
 		
 		$states = array_values(array_unique($attribute_it->fieldToArray('ReferenceName')));

 		return $states;
 	}

	function getBoardValues()
	{
		$states = $this->getBoardStates();

		$boardvalues = array();
		foreach( $states as $key => $value ) {
			$boardvalues[$key] = ' '.$value;
		}

		return $boardvalues;
	}

 	function getBoardNames() {
 		return $this->getBoardTitles();
 	}
 	
 	function getBoardTitles()
 	{
        $names = array();

        $attribute_it = $this->getBoardAttributeIterator();
        while ( !$attribute_it->end() ) {
            $names[$attribute_it->get('ReferenceName')] = $attribute_it->get('Caption');
            $attribute_it->moveNext();
        }

        return $names;
 	}

 	function getGroupFieldObject( $field_name )
 	{
 		if ( $this->object->IsReference($field_name) ) {
 			return $this->object->getAttributeObject( $field_name );
 		}
 		else
 		{
 		}
 	}

	function getGroupNullable( $field_name, $state ) {
 	    if ( $this->getBoardAttributeIterator()->get('ReferenceName') != $state ) return false;
		return $this->getObject()->getAttributeType($field_name) != ''
            && !$this->getObject()->IsAttributeRequired($field_name);
	}

 	function getModifyActions( $object_it )
 	{
		$actions = array();
		
		$method = new ObjectModifyWebMethod($object_it);
		if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => $method->getCaption(),
                'url' => $method->getJSCall()
            );
        }

		return $actions;
 	}
 	
	function getActions( $object_it ) 
	{
		$actions = $this->getModifyActions( $object_it );
		
		$form = $this->getTable()->getPage()->getFormRef();

	    if ( !$form instanceof PageForm ) return array();

	    $form->show($object_it);

	    $transition_actions = $form->getTransitionActions();
	    if ( count($transition_actions) < 1 ) return $actions;

		if ( count($transition_actions) > 4 ) {
			$actions[] = array();
			$actions[] = array (
				'name' => translate('Состояние'),
				'items' => $transition_actions
			);
		}
		elseif ( count($transition_actions) > 0 ) {
			$actions = array_merge( $actions, array(array()), $transition_actions );
		}

		$plugin_actions = array();
		foreach( $this->plugins as $plugin ) {
			$plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $object_it ));
		}
		if ( count($plugin_actions) > 0 ) {
			$actions[] = array();
			$actions = array_merge( $actions, $plugin_actions );
		}

		$more_actions = $form->getMoreActions();
		if ( count($more_actions) > 0 ) {
			$actions[] = array('uid' => 'middle');
			$actions = array_merge($actions, $more_actions);
		}

		$actions[] = array();
		$actions['create'] = array (
			'name' => translate('Создать'),
			'items' => $form->getNewRelatedActions(),
			'uid' => 'create'
		);

		foreach( $this->plugins as $plugin ) {
			$plugin->interceptMethodFormGetActions( $form, $actions );
		}

		return $actions;
	}
	
	function getHeaderActions( $board_value )
	{
		$actions = array();
		
		$actions[] = array (
				'name' => text(2017),
				'url' => "javascript:selectCards('".$board_value."');"
		);

		$widget_it = $this->getTable()->getReferencesListWidget($this->getObject(), '');
		if ( $widget_it->getId() != '' ) {
			$actions[] = array (
				'name' => text(2271),
				'url' => $widget_it->getUrl('filter=skip&'.$this->getBoardAttributeFilter().'='.$board_value)
			);
		}

		$actions[] = array();
		$actions[] = array (
			'name' => $_COOKIE[$this->getId()]['column/'.trim($board_value)] == '' ? text(2149) : text(2150),
			'alt' => $board_value,
			'uid' => "collapse-cards",
			'class' => $_COOKIE[$this->getId()]['column/'.trim($board_value)] == '' ? text(2150) : text(2149)
		);
		$actions[] = array (
			'name' => text(2148),
			'url' => "javascript:filterLocation.turnOn('hiddencolumns', '".$board_value."', 1);"
		);

		return $actions;
	}
	
	function IsNeedToDelete( ) { return true; }

 	function getItemStyle( $it )
 	{
        // priority driven coloring
        return 'background:'.$this->getPriorityBackgroundColor($it->get('Priority')).';';
 	}
 	
	function getCardColor( $object_it )
	{ 	
	}
 	
	function getPages()
	{
		return 1;
	}
 	
	function drawItemMenu( $object_it, $style = "" )
	{
		$actions = $this->getActions($object_it);
		
		echo '<div id="context-menu-'.$object_it->getId().'">';
		
			echo $this->view->render('core/PopupMenu.php', array (
			        'title' => '&nbsp;',
			        'items' => $actions
			));
		
		echo '</div>';
	}
	
	function drawCheckbox( $object_it )
	{
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) return;
		echo '<input type="checkbox" class="checkbox" name="to_delete_'.$object_it->getId().'">';
	}

	function drawHeader( $board_value, $board_title )
	{
		$actions = $this->getHeaderActions($board_value);
		
		if ( count($actions) < 1 ) {
			echo $board_title;
			return;
		}

		echo '<div id="context-menu-'.$board_value.'" class="brd-head-menu">';
			echo $this->view->render('core/PageBoardMenu.php', array (
				'title' => $board_title,
				'items' => $actions,
				'hint' => htmlentities($this->column_descriptions[$board_value])
			));
		echo '</div>';
	}
	
	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'Caption':
            case 'CaptionNative':
			    echo '<div class="bi-cap '.($this->uid_visible ? '' : 'bi-cap-large').'">';
    			    echo $object_it->get($attr);

    			    foreach( $this->wysiwygAttributes as $attribute ) {
    			        if ($attribute == 'RecentComment' ) continue;
                        echo $object_it->getHtmlDecoded($attribute);
                    }
				echo '</div>';
				break;

			case 'UID':
			    parent::drawCell( $object_it, $attr );
			    break;

            case 'Basement':
                $this->drawCheckbox($object_it);
                break;

			default:
			    if ( $object_it->get($attr) === '' ) return;
		        switch ( $object_it->object->getAttributeType($attr) )
		        {
		            case 'date':
                    case 'datetime':
    					echo '<div class="date-attr" title="'.translate($this->object->getAttributeUserName($attr)).'">';
    						echo '<img src="/images/date.png"> ';
							parent::drawCell( $object_it, $attr );
    					echo '</div>';
		                
    					break;

                    case 'wysiwyg':
                        if ( !$this->getColumnVisibility('Caption') ) {
                            echo '<div class="bi-cap bi-cap-large">';
                                echo $object_it->getHtmlDecoded($attr);
                            echo '</div>';
                        }
                        break;

		            default:
		                if ( in_array('astronomic-time', $this->object->getAttributeGroups($attr)) ) {
		                    if ( $object_it->get($attr) != '' ) {
                                echo '<i class=icon-time title="'.translate($this->object->getAttributeUserName($attr)).'"></i>';
                                parent::drawCell( $object_it, $attr );
                                echo ' &nbsp; ';
                            }
                            break;
                        }
    					echo '<div class="card-f">';
		                    $editable = $this->object->getAttributeEditable($attr)
                                && getFactory()->getAccessPolicy()->can_modify_attribute($this->object, $attr);

    					    $title = translate($this->object->getAttributeUserName($attr));
    					    if ( $editable ) {
                                $script = "javascript:processBulk('{$title}','"
                                    ."?formonly=true&operation=Attribute{$attr}','{$object_it->getId()}', devpromOpts.updateUI);";
                                echo "<span class='editable' onclick=\"{$script}\">{$title}: ";
                                    parent::drawCell( $object_it, $attr );
                                echo "</span>";
                            }
    					    else {
    					        echo $title . ': ';
                                parent::drawCell( $object_it, $attr );
                            }
    				    echo '</div>';
		        }
		}
	}
	
	function drawRefCell( $ref_it, $object_it, $attr )
	{
		if ( $object_it->get($attr) == '' ) return;

		switch ( $ref_it->object->getEntityRefName() )
		{
		    case 'pm_Attachment':
		    	parent::drawRefCell( $ref_it, $object_it, $attr );
		    	break;

		    default:
                echo '<div class="card-f">';
                    $editable = $this->object->getAttributeEditable($attr)
                        && getFactory()->getAccessPolicy()->can_modify_attribute($this->object, $attr);

                    $title = $this->getFieldTitle($attr);
                    if ( $editable ) {
                        $script = "javascript:processBulk('{$title}','"
                            ."?formonly=true&operation=Attribute{$attr}','{$object_it->getId()}', devpromOpts.updateUI);";
                        if ( $title != '' ) $title .= ': ';
                        echo "<span class='editable' onclick=\"{$script}\">{$title}";

                        if ( $this->getUidService()->hasUid($ref_it) ) {
                            echo "</span>";
                            echo join( ' ', $this->getRefNames($ref_it, $object_it, $attr));
                        }
                        else {
                            echo join( ' ', $this->getRefNames($ref_it, $object_it, $attr));
                            echo "</span>";
                        }
                    }
                    else {
                        if ( $title != '' ) $title .= ': ';
                        echo $title . join( ' ', $this->getRefNames($ref_it, $object_it, $attr));
                    }
                echo '</div>';
		}
	}

    public function getRefNames($entity_it, $object_it, $attr )
    {
        $items = array();
        if ( !is_object($entity_it->object->entity) ) return $items;

        $uid_used = $this->getUidService()->hasUid($entity_it);
        while ( !$entity_it->end() )
        {
            switch( $entity_it->object->getEntityRefName() ) {
                case 'pm_Project':
                    $info = $this->getUidService()->getUidInfo($entity_it, true);
                    $items[$entity_it->getId()] = '<a href="'.$info['url'].'">'.$info['caption'].'</a>';
                    break;
                default:
                    if ( $uid_used ) {
                        $items[$entity_it->getId()] =
                            $this->getUidService()->getUidIconGlobal(
                                    $entity_it, $entity_it->get('VPD') != $object_it->get('VPD'));
                    }
                    else {
                        $items[$entity_it->getId()] = $entity_it->getDisplayName();
                    }
            }
            $entity_it->moveNext();
        }
        return $items;
    }

	function getFieldTitle( $attr ) {
 	    return translate($this->object->getAttributeUserName($attr));
    }

	function drawAppendCard($boardValue, $groupValue)
    {
        if ( $this->getBoardAttribute() == 'State' ) {
            if ( $this->getBoardAttributeIterator()->moveTo('ReferenceName', trim($boardValue))->get('IsNewArtifacts') != 'Y' ) return;
        }

        $method = $this->new_action['method'];
        if ( is_object($method) ) {
            $parms = array(
                $this->getBoardAttribute() => trim($boardValue)
            );
            if ($groupValue != '') {
                $group_it = $this->getGroupIt();
                $group_it->moveToId($groupValue);
                if ( $group_it->get('VPD') != '' && $this->getBoardAttribute() == 'State' ) {
                    $method->setVpd($group_it->get('VPD'));
                    $parms[$this->getBoardAttribute()] = trim(array_shift(
                        array_intersect(
                            preg_split('/,/', trim($boardValue)),
                            $this->projectStates[$group_it->get('VPD')]
                        )
                    ));
                }
                $parms[$this->getGroup()] = trim($groupValue);
            }
            $parms = $this->getAppendCardParms($boardValue, $groupValue, $parms);
            $url = $method->getJSCall($parms, $this->getAppendCardTitle($boardValue, $groupValue));

            echo '<div class="actions-wrap '.(defined('UI_EXTENSION') && !UI_EXTENSION ? 'hidden' : '').'"><div class="actions-area">';
            echo '<a href="' . $url . '" class="append-card btn btn-xs btn-success"><i class="icon-plus icon-white"></i></a>';
            echo '<a more="'.$boardValue.'" group="'.$groupValue.'" class="btn btn-light btn-xs collapse-cards" title="'.text(2146).'"><i class="icon-resize-small"></i></a>';
            echo '</div></div>';
        }
    }

    function getAppendCardTitle($boardValue, $groupValue) {
 	    return '';
    }

    function getAppendCardParms($boardValue, $groupValue, $parms) {
 	    return $parms;
    }

	function drawCellBasement( $boardValue, $groupValue )
	{
	}

    function getSettingsViewParms()
    {
        $parms = parent::getSettingsViewParms();
        $this->extendModel();

        $values = $this->getTable()->getFilterValues();
        $this->boardrefnames = $this->getBoardStates();
        $this->boardnames = $this->getBoardTitles();
        $active = \TextUtils::parseFilterItems($values['hiddencolumns'], ',-');

        $columns = array();
        $visible = array();
        foreach ( $this->boardrefnames as $key => $column )
        {
            if ( !in_array($column, $active) && $column != $values['hiddencolumns'] ) {
                $visible[] = $column;
            }
            $columns[$column] = $this->boardnames[$column];
        }
        uasort($columns, function($left, $right) {
            return $left > $right;
        });

        $parms['hiddencolumns'] = array(
            'attribute' => 'multiple',
            'name' => text(2919),
            'options' => $columns,
            'value' => $visible
        );

        $parms['color'] = array(
            'name' => text(2918),
            'options' => array (
                'state' => translate('По состоянию'),
                'type' => translate('По типу'),
                'priority'  => translate('По приоритету')
            ),
            'value' => $values['color']
        );

        $parms['show']['name'] = text(2920);
        return $parms;
    }

	function draw( $view )
	{
		$this->view = $view;
		$this->offset = 0;
		$this->it = $this->getIteratorRef()->copyAll();

		$it = $this->it;
		$it->moveFirst();
    	
		$filter_values = $this->getTable()->getFilterValues();
    	$sort_values = preg_split('/\./', $this->getTable()->getSort());
    	$entity_ref_name = $it->object->getEntityRefName();

    	$references = array();
    	foreach( $this->getObject()->getAttributes() as $key => $attribute ) {
    		$references[$key] = $this->object->IsReference($key); 
    	}

		$cellSettings = array();
    	$groupSettings = array();
		$columnSettings = array();

    	$modifiable = getFactory()->getAccessPolicy()->can_modify($this->getObject());
		$globals = $view->getGlobals();
		?>
        <div class="list-container">
		<table id="<?php echo $this->getId() ?>" class="table board-table board-size-xl" cellspacing="0" cellpadding="0" border="0" uid="<?=$globals['widget_id']?>">
			<?
			// в случае группировки группирующее поле отображается первым
			$group_field = $this->getGroup();
			if ( $this->getObject()->getAttributeType($group_field) == '' ) $group_field = '';
			
			// получим все названия опорного атрибута
			$board_names = $this->getBoardNames();

			// получим все значения опорного атрибута
			$board_values = $this->getBoardValues();

			if ( is_array($_COOKIE[$this->getId()]) ) {
				foreach( $_COOKIE[$this->getId()] as $setting => $value ) {
					$parts = preg_split("/\//", $setting);
					if ( $parts[0] == 'size' ) {
						if ( $parts[1] == 'row' ) {
							foreach( $board_values as $columnId ) {
								$cellSettings[trim($columnId)][$parts[2]] = $value;
                                $groupSettings[$parts[2]][] = $value;
							}
                            $groupSettings[$parts[2]] = array_unique($groupSettings[$parts[2]]);
						}
						else {
							$cellSettings[$parts[1]][$parts[2]] = $value;
						}
					}
					if ( $parts[0] == 'column' ) {
						$columnSettings[$parts[1]] = $value;
					}
				}

			}

			if ( $filter_values['hiddencolumns'] != '' ) {
				foreach( \TextUtils::parseFilterItems($filter_values['hiddencolumns'], ',-') as $ref_name ) {
				    unset($board_names[$ref_name]);
                    unset($board_names[intval($ref_name)]);

                    if ( $ref_name == '0' ) $ref_name = '';
                    $index = array_search(' '.$ref_name, $board_values);
                    if ( $index !== false ) unset($board_values[$index]);
				}
			}

			$board_values = array_values($board_values);

			if ( count($board_names) < 1 || count($board_values) < 1 )
			{
				$board_it = $this->it->getRef(
					$this->getBoardAttribute());
					
				$board_it = $board_it->object->getAll();

				for( $i = 0; $i < $board_it->count(); $i++ )
				{
					array_push($board_names, 
						$board_it->getDisplayName());
						
					array_push($board_values, 
						$board_it->getId());
					
					$board_it->moveNext();
				}
			}				

            $headerRow = '';
			// отрисовываем значения опорного атрибута в заголовке списка
            ob_start();
			foreach( $board_names as $ref_name => $title )
			{
				$className = $columnSettings[trim($ref_name)];
				if ( $className == '' ) {
					$width = 'width="'.round(100 / (count($board_names)), 0).'%"';
				}
				else {
					$width = 'width="1%"';
				}
                echo '<th value="'.trim($ref_name).'" align=center class="list_header '.$className.'" '.$width.'>';
					$this->drawHeader($ref_name, $title);
                echo '</th>';
			}
            $headerRow .= ob_get_contents();
            ob_end_clean();

            echo '<thead>';
            echo '<tr class="board-columns">';
            echo $headerRow;
            echo '</tr>';
            echo '</thead>';

            echo '<tbody>';
			// получим список атрибутов для сущности
			$attrs = $this->getColumnsRef();

			// формируем массив индексов столбцов, отсортированных
			// в нужном пользователю порядке
			$attr_index = array();
			foreach( $attrs as $key => $attr ) 
			{
				if ( !$this->getColumnVisibility($attr) )
				{
					continue;
				}

				$order_index = (9999 + $key);
				$attr_index[$order_index] = $key;
			}

			ksort($attr_index);
			$attr_index = array_values($attr_index);
		
			// отрисовываем строки
			$groupOrder = $this->getGroupSort();
			$column_keys = array_flip($board_values);
			$board_cells = array();
			$rows_keys = array();

			$groupNullable = $this->getGroupNullable($group_field, trim(array_shift(array_values($board_values))));
            $firstColumnNoGroup = $this->dontGroupFirstColumn($group_field)
                && $groupNullable && count($board_values) > 1;

            if ( defined('BOARD_BACKLOG_V1') && BOARD_BACKLOG_V1 ) {
                $firstColumnNoGroup = false;
            }

			$group_it = $this->getGroupIt();
			while( !$group_it->end() )
			{
				$rows_keys[$group_it->getId()] = $this->getObject()->createCachedIterator(
                        array (array (
                            $group_field => $group_it->getId(),
                            'VPD' => $group_it->get('VPD')
                        ))
					);
				foreach($column_keys as $key => $value ) {
					$board_cells[$group_it->getId()][$value] = array();
				}
				$group_it->moveNext();
			}

			if ( $group_field != '' && !$this->getObject()->IsAttributeRequired($group_field) && $this->getGroupFilterValue() == '' )
			{
				$cells = array();
				foreach($column_keys as $key => $value ) {
					$cells[$value] = array();
				}
				$keys = array();
				if ( $groupNullable ) {
					$keys[''] = $this->getObject()->createCachedIterator(
									array (
										array ( $group_field => '' )
									)
								);
				}
				if ( $groupOrder == "A" || $firstColumnNoGroup ) {
					$rows_keys = $keys + $rows_keys;
					$board_cells = array('' => $cells) + $board_cells;
				}
				else {
					$rows_keys = $rows_keys + $keys;
					$board_cells = $board_cells + array('' => $cells);
				}
			}

			if ( !is_array($board_cells['']) ) {
                $firstColumnNoGroup = false;
            }

			$boardAttribute = $this->getBoardAttribute();
			while( !$it->end() )
			{
			    $column_value = array_pop(preg_split('/,/',$it->get($boardAttribute)));
			    
				$column = array_pop(
					array_filter(array_keys($column_keys), function($value) use($column_value) {
						return in_array($column_value, preg_split('/,/', trim($value)));
					})
				);
				if ( !array_key_exists($column, $column_keys) ) {
					$it->moveNext(); continue;
				}

				$column = $column_keys[$column];
				$group_key = $group_field != '' ? $it->get($group_field) : '-2';
                if ( $firstColumnNoGroup && $column == 0 ) {
                    $group_key = '';
                }
                if ( !array_key_exists($group_key, $board_cells) ) {
                    $group_key = '-3';
                }

                if ( count($board_cells[$group_key]) < 1 ) {
                    $board_cells[$group_key] = array_pad(array(), count($board_values), array());
                }
                if ( !is_object($rows_keys[$group_key]) || $rows_keys[$group_key]->getId() == '' ) {
                    $rows_keys[$group_key] = $it->copy();
                }

                if ( count($board_cells[$group_key][$column]) < $this->maxCellVisibleItems ) {
                    $board_cells[$group_key][$column][] = $it->copy();
                }
                else {
                    $hidden_cells[$group_key][$column][] = $it->getId();
                }
				$it->moveNext();
			}		
			$it->moveFirst();	

			$columns_number = count($board_values);
    		if ( $firstColumnNoGroup ) {
			    $columns_number--;
            }

			$rowspanCoeff = $this->hasCellBasement() ? 3 : 2;

			foreach( $board_cells as $group_key => $row )
			{
				$row_it = $rows_keys[$group_key];
				if ( $group_field != '' && !is_object($row_it) ) continue;

				if ( $group_field != '' && (!$firstColumnNoGroup || $group_key != '') )
				{
				    $groupValue = $row_it->get($group_field);
					echo '<tr class="info" group-id="'.$group_key.'">';
						echo '<td class="board-group row-clmn" colspan="'.$columns_number.'" style="background:'.$this->getGroupBackground($group_field, $row_it).'">';
                            echo '<div class="plus-minus-toggle '.(count($groupSettings[$groupValue]) > 0 ? 'collapsed' : '' ).'" data-toggle="collapse" onclick="javascript: resizeCardsInGroup(\''.$group_key.'\')"></div>';
                            switch( $group_key ) {
                                case '-3':
                                    echo text(3126);
                                    break;
                                default:
                                    $this->drawGroup($group_field, $row_it);
                            }
                            if ( !$row_it->object->IsReference($group_field) ) {
                                echo '<a class="btn btn-check btn-transparent" href="javascript: checkGroupTrue(\'' . $group_key . '\')"><i class="icon-check"></i></a>';
                            }
					    echo '</td>';
					echo '</tr>';
				}
				
				echo '<tr class="row-cards">';
				
				foreach( $row as $prev_board_index => $columns )
				{
					if ( $group_field == 'Project' ) {
						$project_it = $this->getGroupIt();
						$project_it->moveToId($group_key);
						$project_attr = ' project="'.$project_it->get('CodeName').'"';
					}

					$cellClass = $cellSettings[trim($board_values[$prev_board_index])][$group_key];
					if ( $cellClass == '' ) $cellClass = $columnSettings[trim($board_values[$prev_board_index])];

					if ( $firstColumnNoGroup && $group_key != '' && $prev_board_index == 0 ) continue;

					if ( $firstColumnNoGroup && $prev_board_index == 0 ) {
                        echo '<td class="board-column" rowspan="'.(count(array_keys($board_cells))*$rowspanCoeff).'">';
                    }
                    else {
                        echo '<td class="board-column">';
                    }
					echo '<div class="list_cell '.$cellClass.'" more="'.$board_values[$prev_board_index].'" group="'.$group_key.'" sort="'.$sort_values[0].'" '.$project_attr.'>';
					
					foreach( $columns as $column_it )
					{
						$uid = htmlentities($this->getUidService()->getUidOnly($column_it), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
						
						$style = $this->getItemStyle( $column_it );
						
						$spinner = $this->getCardColor( $column_it );
						
						if ( $spinner != '' ) {
						    $spinner = 'background-color:'.$spinner;
                            $attributes_style = '';
                        }
                        else {
                            $spinner = 'text-align:left;';
                            $attributes_style = 'padding-right: 5px;';
                        }

						$order_num = $column_it->get('OrderNum') < 1 ? ($i + 1) : $column_it->get('OrderNum');


						echo '<div class="board_item" data-toggle="context" data-target="#context-menu-'.$column_it->getId().'" project="'.$column_it->get('ProjectCodeName').'" object="'.$column_it->getId().'" group="'.$group_key.'" state="'.$column_it->get('State').'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'" modifiable="'.$modifiable.'" entity="'.$entity_ref_name.'" modified="'.$column_it->get('AffectedDate').'" uid="'.$uid.'">';
							echo '<div class="board_item_body" style="'.$style.'">';
							if ( $spinner != '' ) echo '<div class="board_item_spinner" style="'.$spinner.'">&nbsp;</div>';
							echo '<div class="board_item_attributes" style="'.$attributes_style.'">';
							echo '<div class="item_attrs" style="display:none;" modified="'.$column_it->get_native('RecordModified').'"></div>';
							echo '<div class="ca-field">';
							for( $j = 0; $j < count($attr_index); $j++)
							{
								$attr = $attrs[$attr_index[$j]];
								
								if( $references[$attr] ) 
								{
									$this->drawRefCell($this->getFilteredReferenceIt($attr, $column_it->get($attr)), $column_it, $attr);
								} 
								else 
								{
									$this->drawCell( $column_it, $attr );
								}
							}
							echo '</div>';
							echo '</div>';

                        $this->drawItemMenu($column_it);
						echo '</div>';
						echo '</div>';
                    }
                    $this->drawAppendCard($board_values[$prev_board_index], $group_key);
					echo '</div>';

                    $hiddenIdsChunks = array_chunk($hidden_cells[$group_key][$prev_board_index], $this->maxCellVisibleItems);
                    if ( count($hiddenIdsChunks) > 0 ) {
                        echo '<div class="clearfix"></div>';
                        echo '<div>';
                        foreach( $hiddenIdsChunks as $chunk ) {
                            echo '<a class="cell-hidden-ids btn btn-light btn-xs" title="'.text(2822).'" ids="'.join(',',$chunk).'">...</a>';
                        }
                        echo '</div>';
                    }
                    echo '</td>';
				}
				echo '</tr>';

				if ( $this->hasCellBasement() ) {
                    echo '<tr class="row-basement" group-id="' . $group_key . '">';
                    if (!defined('SKIP_BOARD_SECTIONS')) {
                        foreach ($row as $prev_board_index => $columns) {
                            if ($firstColumnNoGroup && $prev_board_index == 0) continue;
                            echo '<td class="cell-add-btn">';
                            $this->drawCellBasement($board_values[$prev_board_index], $group_key);
                            echo '</td>';
                        }
                    }
                    echo '</tr>';
                }
			}
			if ( $firstColumnNoGroup ) {
                echo '<tr class="row-cards"><td colspan="'.($columns_number).'">';
                echo '</td></tr>';
            }

			if ( $this->it->count() < 1 && count($board_cells) < 1 )
			{
				echo '<tr class="row-cards"><td class="list_cell board-column" colspan="'.($columns_number).'" style="padding:6px;">';
					echo $this->getNoItemsMessage();
				echo '</td></tr>';
			}
			?>
			</tbody>
		</table>
        </div>
		<a name="bottomoffset"></a>
		<?
		
		if ( $_REQUEST['tableonly'] == '' )
		{
			$this->drawScripts();
		}
		
		unset($this->view);
		$this->view = null;
	}	
	
	function drawScripts()
	{
	    $object = $this->getObject();
		$group_field = $this->getGroup();
		$board_values = $this->getBoardValues();
		$columns = count($board_values);
		$values = $this->getTable()->getFilterValues();

		?>
		<script type="text/javascript">
		filterLocation.parms['hiddencolumns'] = '<? echo $values['hiddencolumns']; ?>';
		var boardItemOptions = jQuery.extend({},draggableOptions);
		
		$(document).ready( function () {
		    boardItemOptions.getItemWidth = function ()
			{
				var columnWidth = 
					Math.min.apply(Math, $('td.board-column').map(function() {
					        return $(this).width();
					    }).get()) - 30;
			    
				var cardSpace = 2;
				var minWidth = $('.left-on-card a').first().width() + 30;
				var sizes = ['xs','s','m','l','xl'];
				var maxSizes = {
						'xl': <?=($columns <= 4 ? 140 : 120)?>,
						'l': <?=($columns <= 4 ? 120 : 110)?>,
						'm': Math.max(minWidth,90),
						's': Math.max(minWidth,80),
						'xs': Math.max(minWidth,70)	
				};
				var minCardWidth = maxSizes['xl'];
				$.each(sizes, function(i,v) {
					if ( $('.board-table').hasClass('board-size-'+v) ) {
						minCardWidth = maxSizes[v];
					}
				});
				var itemsInColumn = 1;

				for( var i = 1; i < 100; i++ ) {
					if ( (Math.floor(columnWidth / i - (i - 1) * cardSpace) < minCardWidth) ) {
						itemsInColumn = i - 1;
						break;
					}
				}

				var itemWidth = columnWidth / itemsInColumn - (itemsInColumn - 1) * cardSpace;

				return itemsInColumn <= 1 ? Math.max(columnWidth, minCardWidth) : Math.max(itemWidth, minCardWidth);
			};
			boardItemOptions.cellCSSPath = ".board-column,.board_item";
            boardItemOptions.className =  '<?=get_class($this->object)?>';
			boardItemOptions.entityRefName =  '<?=$this->object->getEntityRefName()?>';
			boardItemOptions.classUserName = '<?=$this->object->getDisplayName()?>';
			boardItemOptions.transitionTitle = '<?=text(1011)?>';
			boardItemOptions.locked = false;
			boardItemOptions.groupAttribute = '<?=$group_field?>';
			boardItemOptions.boardAttribute = '<?=$this->getBoardAttribute()?>';
			boardItemOptions.boardCreated = '<?=SystemDateTime::date()?>';
			boardItemOptions.redrawItemUrl = '<?=strtolower(get_class($object))?>';
			boardItemOptions.sliderTitle = '<?=text(2019)?>';
            boardItemOptions.itemFormUrl = '<?=$this->getItemFormUrl()?>';
            board( boardItemOptions );
		});
		</script>
		<?
	}

	function getItemFormUrl() {
 	    return '';
    }

	function render( $view, $parms )
	{
	    $this->setRenderView($view);
		$this->uid_visible = $this->getColumnVisibility('UID');

		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( $method->hasAccess() ) {
			$this->new_action = array (
				'name' => translate('Добавить'),
				'method' => $method
			);
		}
		$state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
		while( !$state_it->end() ) {
			$this->projectStates[$state_it->get('VPD')][] = $state_it->get('ReferenceName');
			$state_it->moveNext();
		}

        foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
            if ( in_array($attribute, array('Caption', 'CaptionNative')) ) continue;
            if ( $this->getObject()->getAttributeType($attribute) != 'wysiwyg' ) continue;
            if ( !$this->getColumnVisibility($attribute) ) continue;
            $this->wysiwygAttributes[] = $attribute;
        }

		echo $view->render("core/PageBoard.php",
			array_merge($parms, $this->getRenderParms()) );
	}

	function getMaxOnPage() {
		return 9999;
	}

	function dontGroupFirstColumn( $group ) {
 	    return false;
    }

    function IsNeedNavigator() {
        return false;
    }

    function hasCellBasement() {
 	    return false;
    }

    function getColumnVisibility( $attribute )
    {
        if ( $attribute == 'Basement' ) return true;
        return parent::getColumnVisibility( $attribute );
    }

    function getMaxGroups() {
        return defined('LIST_MAX_GROUPS') ? LIST_MAX_GROUPS : 30;
    }

    private $new_action = array();
 	private $wysiwygAttributes = array();
}