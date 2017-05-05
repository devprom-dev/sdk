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

 	function PageBoard( $object ) 
	{
		parent::__construct( $object );
		
		$plugins = getFactory()->getPluginsManager();
		$this->plugins = is_object($plugins) 
				? $plugins->getPluginsForSection(getSession()->getSite()) : array();
	}

	function setTable( & $table )
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
		$project = getSession()->getProjectIt()->getId();
		$state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
		return $state_it->object->createCachedIterator(
			array_values(array_filter($state_it->getRowset(), function($row) use ($project) {
				return $row['Project'] == $project;
			}))
		);
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

		foreach( $states as $value )
		{
			$boardvalues[$value] = ' '.$value;
		}

		return $boardvalues;
	}

 	function getBoardNames()
 	{
 		$attribute_it = $this->getBoardAttributeIterator();
 		
 		$names = array();
 		
 		while ( !$attribute_it->end() )
 		{
			$names[$attribute_it->get('ReferenceName')][] = $attribute_it->get('Caption');
 				
 			$attribute_it->moveNext();
 		}
 		
 		foreach( $names as $key => $name )
 		{
 		    $names[$key] = join('/', array_unique($name));
 		}

 		return $names;
 	}
 	
 	function getBoardTitles()
 	{
 		$attribute_it = $this->getBoardAttributeIterator();
 		return array_values(array_unique($attribute_it->fieldToArray('Caption')));
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

	function getGroupNullable( $field_name ) {
		return $this->getObject()->getAttributeType($field_name) != '' && !$this->getObject()->IsAttributeRequired($field_name);
	}

 	function getGroupStyle()
 	{
 	    return GROUP_STYLE_ROW;
 	}
 	
 	function getModifyActions( $object_it )
 	{
		$actions = array();
		
		$method = new ObjectModifyWebMethod($object_it);

		$method->setRedirectUrl('donothing');
		
		$actions[] = array(
				'name' => translate('Изменить'), 
				'url' => $method->getJSCall() 
		);
			
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

		$widget_it = $this->getTable()->getReferencesListWidget($this->getObject());
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
 		return '';
 	}
 	
	function getCardColor( $object_it )
	{ 	
	}
 	
	function getPages()
	{
		$rows_per_columns = array(0);
		
		$this->it->moveFirst();
		while ( !$this->it->end() )
		{
			$rows_per_columns[$this->it->get($this->getBoardAttribute())]++;
			$this->it->moveNext();
		}
		
		return max($rows_per_columns) / $this->getMaxOnPage();
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

		echo '<div id="context-menu-'.$board_value.'">';
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
			    echo '<div class="bi-cap '.($this->uid_visible ? '' : 'bi-cap-large').'">';
					$this->drawCell( $object_it, 'CaptionNative' );
				echo '</div>';
				break;

			case 'CaptionNative':
				echo $object_it->getWordsOnly('Caption', 16);
				break;

			case 'UID':
			    parent::drawCell( $object_it, $attr );
			    break;
			    
			default:
				
			    if ( $object_it->get($attr) == '' ) return;

		        switch ( $object_it->object->getAttributeType($attr) )
		        {
		            case 'date':
		            case 'datetime':
		                
    					echo '<div class="date-attr" title="'.translate($this->object->getAttributeUserName($attr)).'">';
    						echo '<img src="/images/date.png"> ';
							parent::drawCell( $object_it, $attr );
    					echo '</div>';
		                
    					break;
		                
		            default:
    					echo '<div style="clear:both;padding:0 0 0 0;overflow:hidden;">';
    					    echo translate($this->object->getAttributeUserName($attr)).': ';
    					    parent::drawCell( $object_it, $attr );
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
				if ( !$this->getUidService()->hasUID($ref_it) ) {
					echo translate($object_it->object->getAttributeUserName( $attr )).': ';
				}
				parent::drawRefCell($ref_it , $object_it, $attr);
		}
	}

	function drawCellBasement( $boardValue, $groupValue )
	{
		$method = $this->new_action['method'];
		if ( is_object($method) ) {
			$parms = array(
				$this->getBoardAttribute() => trim($boardValue)
			);
			if ($groupValue != '') {
				$group_it = $this->getGroupIt();
				$group_it->moveToId($groupValue);
				if ( $group_it->get('VPD') != '' ) {
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
			$url = $method->getJSCall($parms);
			echo '<a href="' . $url . '" class="append-card btn btn-mini btn-success pull-left"><i class="icon-plus icon-white"></i></a>';
		}
		echo '<a more="'.$boardValue.'" group="'.$groupValue.'" class="btn btn-mini collapse-cards pull-right" title="'.text(2146).'"><i class="icon-resize-small"></i></a>';
	}

	function buildFilterActions( & $base_actions )
	{
		parent::buildFilterActions( $base_actions );
		
		foreach ( $base_actions as $key => $action ) {
		    if ( $action['uid'] == 'columns' ) {
		        $base_actions[$key]['name'] = translate('Атрибуты');
		    }
			if ( $action['uid'] == 'rows' ) {
				unset($base_actions[$key]);
			}
		}
		
		$base_actions = array_merge(
		    array_slice($base_actions, 0, count($base_actions) - 2),
		    $this->buildFilterAttributes(),
			$this->buildFilterColorScheme(),
		    array_slice($base_actions, count($base_actions) - 2, count($base_actions) - 1)
		);
	}

	function buildFilterAttributes()
	{
		$actions = array();
		
		$columns = array();
		$values = $this->getFilterValues();

		$this->boardrefnames = $this->getBoardStates();
		$this->boardnames = $this->getBoardTitles();
		
		$active = preg_split('/,/', $values['hiddencolumns']);
		
		foreach ( $this->boardrefnames as $key => $column )
		{
			$checked = !in_array($column, $active) && $column != $values['hiddencolumns'];
			
			$script = "javascript: $(this).hasClass('checked') ? filterLocation.turnOff('hiddencolumns', '".$column."', 0) : filterLocation.turnOn('hiddencolumns', '".$column."', 0); ";
			
			$columns[$this->boardnames[$key]] = array ( 'url' => $script, 'checked' => $checked );
		}
		
		ksort($columns);
		$column_actions = array();
		
		foreach ( $columns as $caption => $column )
		{
			array_push( $column_actions, 
				array ( 'url' => $column['url'], 'name' => $caption, 
						'checked' => $column['checked'], 'multiselect' => true )
			);
		}
		
		if ( count($column_actions) > 0 )
		{
			array_push($actions, array ( 'name' => translate('Столбцы'), 
				'items' => $column_actions , 'title' => '' ) );
		}
		
		return $actions;
	}
	
	function buildFilterColorScheme()
	{
		$values = $this->getFilterValues();

		$schemes = array (
				'state' => translate('По состоянию'),
				'type' => translate('По типу'),
				'priority'  => translate('По приоритету')
		);
		
		$items = array();
		
		foreach ( $schemes as $scheme => $caption )
		{ 
			$items[] = array ( 
					'url' => "javascript: filterLocation.setup( 'color=".$scheme."', 0 ); ", 
					'name' => $caption, 
					'checked' => $values['color'] == $scheme,
					'radio' => true
			);
		} 
		
		$actions = array();
		
		$actions[] = array ( 
			'name' => translate('Цвета'), 
			'items' => $items
		);

		return $actions;
	}
	
	function draw( $view )
	{
		$this->view = $view;
		$this->offset = 0;
		$this->it = $this->getIteratorRef()->copyAll();

		$it = $this->it;
		$it->moveFirst();
    	
		$filter_values = $this->getFilterValues();
    	$sort_values = preg_split('/\./', $this->getTable()->getSort());
    	$entity_ref_name = $it->object->getEntityRefName();

    	$references = array();
    	foreach( $this->getObject()->getAttributes() as $key => $attribute ) {
    		$references[$key] = $this->object->IsReference($key); 
    	}

		$cellSettings = array();
		$columnSettings = array();

    	$modifiable = getFactory()->getAccessPolicy()->can_modify($this->getObject());
		$globals = $view->getGlobals();
		?>
		<table id="<?php echo $this->getId() ?>" class="table board-table board-size-xl" cellspacing="0" cellpadding="0" border="0" uid="<?=$globals['widget_id']?>">
		    <tbody>
			<tr>
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
							}
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

			if ( $filter_values['hiddencolumns'] != '' )
			{
			    unset($board_names[$filter_values['hiddencolumns']]);
			    unset($board_values[$filter_values['hiddencolumns']]);
			
				foreach( preg_split('/,/',$filter_values['hiddencolumns']) as $ref_name )
				{
				    unset($board_names[$ref_name]);
				    unset($board_values[$ref_name]);
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
			
			if ( $group_field != '' && $this->getGroupStyle() == GROUP_STYLE_COLUMN )
			{
				echo '<th align="center" class="list_header" width="20%">';
					$group_attribute = $this->getGroupFieldObject($group_field);
					if ( is_object($group_attribute) )
					{
						echo $group_attribute->getDisplayName(); 
					}
					else
					{
						echo $this->it->object->getAttributeUserName( $group_field );
					}
				echo '</th>'; 
			}

			// отрисовываем значения опорного атрибута в заголовке списка
			foreach( $board_names as $ref_name => $title )
			{
				$className = $columnSettings[trim($ref_name)];
				if ( $className == '' ) {
					$width = 'width="'.round(100 / (count($board_names)), 100).'%"';
				}
				else {
					$width = 'width="1%"';
				}
				echo '<th align=center class="list_header '.$className.'" '.$width.'>';
					$this->drawHeader($ref_name, $title);
				echo '</th>'; 
			}
			
			echo '</tr>';

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
			$prev_group = "-1";
			$groupOrder = $this->getGroupOrder();
			$column_keys = array_flip($board_values);
			$board_cells = array();
			$rows_keys = array();

			$group_it = $this->getGroupIt();
			while( !$group_it->end() )
			{
				$rows_keys[$group_it->getId()] = $this->getObject()->createCachedIterator(
							array (
								array ( $group_field => $group_it->getId() )
							)
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
				if ( $this->getGroupNullable($group_field) ) {
					$keys[''] = $this->getObject()->createCachedIterator(
									array (
										array ( $group_field => '' )
									)
								);
				}
				if ( $groupOrder == "A" ) {
					$rows_keys = $keys + $rows_keys;
					$board_cells = array('' => $cells) + $board_cells;
				}
				else {
					$rows_keys = $rows_keys + $keys;
					$board_cells = $board_cells + array('' => $cells);
				}
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

                if ( count($board_cells[$group_key]) < 1 ) {
                    $board_cells[$group_key] = array_pad(array(), count($board_values), array());
                }
                if ( !is_object($rows_keys[$group_key]) || $rows_keys[$group_key]->getId() == '' ) {
                    $rows_keys[$group_key] = $it->copy();
                }

				$board_cells[$group_key][$column][] = $it->copy();
				$it->moveNext();
			}		
			$it->moveFirst();	

			$columns_number = count($board_values) 
				+ ($group_field != '' && $this->getGroupStyle() == GROUP_STYLE_COLUMN ? 1 : 0);
			
			foreach( $board_cells as $group_key => $row )
			{
				$row_it = $rows_keys[$group_key];
				if ( $group_field != '' && !is_object($row_it) ) continue;

				if ( $group_field != '' && $this->getGroupStyle() == GROUP_STYLE_ROW )
				{
					echo '<tr class="info" group-id="'.$group_key.'">';
						echo '<td class="board-group" colspan="'.$columns_number.'" style="background:'.$this->getGroupBackground($group_field, $row_it).'">';
							echo '<span class="pull-left">';
								$this->drawGroup($group_field, $row_it);
							echo '</span>';
							echo '<a group="'.$row_it->get($group_field).'" class="btn btn-mini group-collapse-cards pull-right" title="'.text(2276).'"><i class="icon-resize-small"></i></a>';
					echo '</td>';
					echo '</tr>'; 
				}
				
				echo '<tr class="row-cards">';
				
				if ( $group_field != '' && $this->getGroupStyle() == GROUP_STYLE_COLUMN )
				{
					$row_it = $rows_keys[$group_key];
					echo '<td class="list_cell board-column" style="background:'.$this->getGroupBackground($group_field, $row_it).'">';
						$this->drawGroup($group_field, $row_it);
					echo '</td>'; 
				}
				
				foreach( $row as $prev_board_index => $columns )
				{
					if ( $group_field == 'Project' ) {
						$project_it = $this->getGroupIt();
						$project_it->moveToId($group_key);
						$project_attr = ' project="'.$project_it->get('CodeName').'"';
					}

					$cellClass = $cellSettings[trim($board_values[$prev_board_index])][$group_key];
					if ( $cellClass == '' ) $cellClass = $columnSettings[trim($board_values[$prev_board_index])];

				    echo '<td class="board-column">';
					echo '<div class="list_cell '.$cellClass.'" more="'.$board_values[$prev_board_index].'" group="'.$group_key.'" sort="'.$sort_values[0].'" '.$project_attr.'>';
					
					foreach( $columns as $column_it )
					{
						$uid = htmlentities($this->getUidService()->getUidOnly($column_it), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
						
						$style = $this->getItemStyle( $column_it );
						
						$spinner = $this->getCardColor( $column_it );
						
						if ( $spinner != '' ) $spinner = 'background-color:'.$spinner.';border:1px solid '.$spinner;
		
						$order_num = $column_it->get('OrderNum') < 1 ? ($i + 1) : $column_it->get('OrderNum'); 
							
						echo '<div class="board_item" data-toggle="context" data-target="#context-menu-'.$column_it->getId().'" style="margin: 0 8px 0 0;" project="'.$column_it->get('ProjectCodeName').'" object="'.$column_it->getId().'" group="'.$group_key.'" state="'.$column_it->get('State').'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'" modifiable="'.$modifiable.'" entity="'.$entity_ref_name.'" modified="'.$column_it->get('AffectedDate').'" uid="'.$uid.'">';
							echo '<div class="board_item_separator" group="'.$group_key.'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'">&nbsp;</div>';
							echo '<div class="board_item_body" style="'.$style.'">';
							if ( $spinner != '' ) echo '<div class="board_item_spinner" style="'.$spinner.'">&nbsp;</div>';
							echo '<div class="board_item_attributes">';
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
					echo '</div>';
                    echo '</td>';
				}
				echo '</tr>';

				echo '<tr class="row-basement" group-id="'.$group_key.'">';
					foreach( $row as $prev_board_index => $columns ) {
						echo '<td class="cell-add-btn">';
							$this->drawCellBasement($board_values[$prev_board_index], $group_key);
						echo '</td>';
					}
				echo '</tr>';
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
	    
		$filter_object = new FilterAutoCompleteWebMethod( $object );
		$filter_object->setFilter( $this->getFiltersName() );
		
		$group_field = $this->getGroup();
		$group_style = $this->getGroupStyle();
		$board_values = $this->getBoardValues();
		
		$columns = count($board_values) + ($group_field != '' && $group_style == GROUP_STYLE_COLUMN ? 1 : 0);
		$xoffset = $columns * 47;
		
		$values = $this->getFilterValues();
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
			boardItemOptions.cellCSSPath = ".board-column,.board_item_separator";
			boardItemOptions.className =  '<? echo $this->object->getClassName() ?>';
			boardItemOptions.classUserName = '<? echo $this->object->getDisplayName() ?>';
			boardItemOptions.transitionTitle = '<? echo text(1011) ?>';
			boardItemOptions.locked = false;
			boardItemOptions.groupAttribute = '<? echo $group_field; ?>';
			boardItemOptions.boardAttribute = '<? echo $this->getBoardAttribute(); ?>';
			boardItemOptions.boardCreated = '<?=SystemDateTime::date()?>';
			boardItemOptions.droppableAcceptFunction = function ( draggable ) 
			{
				if ( !draggable.is(boardItemOptions.itemCSSPath) ) return false;
				var dropinfo = $(this).is('.board-column') ? $(this).children('.list_cell') : $(this);
				
				if ( draggable.attr('more') == "" )
				{
					return dropinfo.attr('more') == "<?php echo $board_values[0]; ?>" && dropinfo.attr('group') == draggable.attr('group');
				}
				else
				{
					if ( parseInt(dropinfo.attr('order')) >= 0 )
					{
						return dropinfo.attr('group') == draggable.parent().attr('group') 
        					&& dropinfo.attr('more') == draggable.attr('more')
        					&& draggable.attr('order') != dropinfo.attr('order')
        					&& dropinfo.attr('order') != draggable.next().attr('order');
					}

					if ( dropinfo.attr('group') == draggable.parent().attr('group') && dropinfo.attr('more') == draggable.attr('more') ) return false;
					
					return true;
				}
			};
			boardItemOptions.redrawItemUrl = '<? echo $filter_object->getValueParm(); ?>';
			boardItemOptions.sliderTitle = '<?=text(2019)?>';

			$('.board_item').show();
		});
		</script>
		<?		
		
		$plugins = getFactory()->getPluginsManager();
	    
	    $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection(getSession()->getSite()) : array();
	
	    foreach( $plugins_interceptors as $plugin )
	    {
	        $result = $plugin->interceptMethodFormDrawScripts( $this );
	        	
	        if ( is_bool($result) ) return;
	    }
	}
	
	function render( $view, $parms )
	{
		$this->uid_visible = $this->getColumnVisibility('UID');

		$method = new ObjectCreateNewWebMethod($this->getObject());
		$method->setRedirectUrl('donothing');
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

		echo $view->render("core/PageBoard.php",
			array_merge($parms, $this->getRenderParms()) ); 
	}

	function getRenderParms()
	{
		return parent::getRenderParms(); // TODO: Change the autogenerated stub
	}

	function getMaxOnPage() {
		return 9999;
	}

	private $new_action = array();
}