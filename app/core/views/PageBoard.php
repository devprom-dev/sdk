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
		
		$plugins = getSession()->getPluginsManager();
		$this->plugins = is_object($plugins) 
				? $plugins->getPluginsForSection(getSession()->getSite()) : array();
	}

	function setTable( & $table )
	{
		parent::setTable( $table );
		
		$table->resetFilterValues();
	}
	
	function getBoardAttribute()
 	{
 		return '';
 	}
 	
 	function getBoardAttributeClassName()
 	{
 		return '';
 	}
 	
 	function buildBoardAttributeIterator()
 	{
 		$classname = $this->getBoardAttributeClassName();

 		if ( $classname == '' ) return $this->getObject()->getEmptyIterator();
 		
 		return getFactory()->getObject($classname)->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate(),
 						new SortAttributeClause('OrderNum')
 				)
 		);
 	}
 	
 	function getBoardAttributeIterator()
 	{
 		if ( is_object($this->board_attribute_iterator) )
 		{
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
 	
	function getFilterValues()
	{
		$filters = parent::getFilterValues();
		
		$state_filter = $this->getStateFilterName();
		
		if ( $filters['hiddencolumns'] != '' )
		{
			if ( in_array($filters[$state_filter], array('all', ''), true) )
			{
			    $display_states = $this->getBoardStates();
			}
			else
			{
			    $display_states = preg_split('/,/', $filters[$state_filter]);
			}
			
			$hidden_states = preg_split('/,/', $filters['hiddencolumns']);
			
			foreach( $display_states as $key => $state )
			{
				if ( in_array($state, $hidden_states) || $state == $filters['hiddencolumns'] )
				{
					unset($display_states[$key]);
				}
			}

			$filters[$state_filter] = join(',', $display_states);
		}

		return $filters;
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
	    
	    $form->show($object_it->copy());
	    
	    $transition_actions = $form->getTransitionActions();
	    if ( count($transition_actions) < 1 ) return $actions;

	    $plugin_actions = array();
    	foreach( $this->plugins as $plugin )
		{
			$plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $object_it ));
		}
		if ( count($plugin_actions) > 0 )
		{
			$transition_actions[] = array();
			$transition_actions = array_merge( $transition_actions, $plugin_actions );
		}
	    
	    $actions = array_merge(
	    		$actions,
	    		array(array()),
	    		$transition_actions		
	    );

		$actions[] = array();
		$actions['create'] = array (
			'name' => translate('Создать'),
			'items' => $form->getNewRelatedActions(),
			'uid' => 'create'
		);

		$plugins = getSession()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($form->getSite()) : array();

		foreach( $plugins_interceptors as $plugin ) {
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
		$actions[] = array();
		$actions[] = array (
				'name' => translate('Спрятать'),
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

		echo '<div id="context-menu-'.$board_value.'" title="'.htmlentities($this->column_descriptions[$board_value]).'">';
			echo $this->view->render('core/TextMenu.php', array (
			        'title' => $board_title,
			        'items' => $actions
			));
		echo '</div>';
	}
	
	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'Caption':
			    echo '<div class="bi-cap '.($this->uid_visible ? '' : 'bi-cap-large').'">';
					echo $object_it->getWordsOnly('Caption', 16);
				echo '</div>';
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
		                
    					echo '<div class="date-attr" style="white-space:nowrap;" title="'.translate($this->object->getAttributeUserName($attr)).'">';
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

		switch ( $object_it->object->getAttributeObject($attr)->getEntityRefName() )
		{
		    case 'pm_Attachment':
				
		    	parent::drawRefCell( $ref_it, $object_it, $attr );
		    	
		    	break;

		    default:

		    	echo '<div style="padding:0 0 0 0;">';
		
				if ( !$this->getUidService()->hasUID($ref_it) )
				{
					echo translate($object_it->object->getAttributeUserName( $attr )).': ';
				}
	
				parent::drawRefCell($ref_it , $object_it, $attr);
				
				echo '</div>';
		}
	}
		
	function buildFilterActions( & $base_actions )
	{
		parent::buildFilterActions( $base_actions );
		
		foreach ( $base_actions as $key => $action )
		{
		    if ( $action['uid'] == 'columns' )
		    {
		        $base_actions[$key]['name'] = translate('Атрибуты');
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
		global $_REQUEST, $offset;

		$this->view = $view;
		
		$this->offset = $_REQUEST[$this->getOffsetName()] != '' ? $_REQUEST[$this->getOffsetName()] : 0;
		
		$this->it = $this->getIteratorRef()->copyAll();
 		$project_cache_it = getFactory()->getObject('ProjectCache')->getAll();
		
		$it = $this->it;
    	
		$it->moveFirst();
    	
		$filter_values = $this->getFilterValues();
    	$sort_values = preg_split('/\./', $this->getTable()->getSort());
    	$entity_ref_name = $it->object->getEntityRefName();
        $new_action = array_shift($this->getTable()->getNewActions());
    	
    	$references = array();
    	
    	foreach( $this->getObject()->getAttributes() as $key => $attribute )
    	{
    		$references[$key] = $this->object->IsReference($key); 
    	}
    	
    	$modifiable = getFactory()->getAccessPolicy()->can_modify($this->getObject());
    	
		?>
		<table id="<?php echo $this->getId() ?>" class="table board-table board-size-xl" cellspacing="0" cellpadding="0" border="0">
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
				echo '<th align=center class=list_header width="'.round(100 / (count($board_names)), 100).'%">';
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
		
			$prev_board_index = 0;

			// отрисовываем строки
			$max_rows_on_page = min($it->count() - $this->offset, $this->getMaxOnPage());

			$column_offset = array();
			$visible_rows_num = array();
			
			foreach( $board_values as $key => $value )
			{
				$visible_rows_num[$key] = 0;
				$column_offset[$key] = $this->offset;
			}
			
			$prev_group = "-1";
			
			$column_keys = array_flip($board_values);
			$board_cells = array();
			$rows_keys = array();
			
			if ( $group_field != '' && !$this->getObject()->IsAttributeRequired($group_field) )
			{
				foreach($column_keys as $key => $value ) {
					$board_cells[''][$value] = array();
				}
				if ( $this->getGroupNullable($group_field) ) {
					$rows_keys = array(
							'' => $this->getObject()->createCachedIterator(
									array (
										array ( $group_field => '' )
									)
							)
					);
				}
				else {
					$rows_keys = array();
				}
			}
			
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

			while( !$it->end() )
			{
			    $column_value = $it->get($this->getBoardAttribute());
			    
				$column = array_pop(
						array_filter(array_keys($column_keys), function($value) use($column_value)
						{
								return in_array($column_value, preg_split('/,/', trim($value)));
						})
				);
			    
				if ( !array_key_exists($column, $column_keys) )
				{
					$it->moveNext(); continue;
				}
				
				$column = $column_keys[$column];
				
				if ( $column_offset[$column] > 0 )
				{
					$column_offset[$column]--;
					
					$it->moveNext();
					continue;
				}

				$visible_rows_num[$column]++;					
				
				if ( $visible_rows_num[$column] > $max_rows_on_page )
				{
					$it->moveNext();
					continue;
				}
				
				$group_key = $group_field != '' ? $it->get($group_field) : '-2';
				
				if ( $group_key != $prev_group ) 
				{
					$prev_board_index = 0;
					$prev_group = $group_key;
					
					if ( count($board_cells[$group_key]) < 1 || count($board_cells[$group_key][$column]) < 1 )
					{
						$board_cells[$group_key] = array_pad(array(), count($board_values), array());
						$rows_keys[$group_key] = $it->copy();
					}
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
							$this->drawGroup($group_field, $row_it);
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
							$project_cache_it->moveToId($group_key);
							$project_attr = ' project="'.$project_cache_it->get('CodeName').'"'; 
					}
					
				    echo '<td class="board-column">';
				    echo '<div class="list-left-cell">&nbsp;</div>';
					echo '<div class="list_cell" more="'.$board_values[$prev_board_index].'" group="'.$group_key.'" sort="'.$sort_values[0].'" '.$project_attr.'>';
					
					foreach( $columns as $column_it )
					{
						$uid = htmlentities($this->getUidService()->getUidOnly($column_it), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
						
						$style = $this->getItemStyle( $column_it );
						
						$spinner = $this->getCardColor( $column_it );
						
						if ( $spinner != '' ) $spinner = 'background-color:'.$spinner.';border:1px solid '.$spinner;
		
						$order_num = $column_it->get('OrderNum') < 1 ? ($i + 1) : $column_it->get('OrderNum'); 
							
						echo '<div class="board_item" data-toggle="context" data-target="#context-menu-'.$column_it->getId().'" style="margin: 0 8px 0 0; width:135px;" project="'.ObjectUID::getProject($column_it).'" object="'.$column_it->getId().'" group="'.$group_key.'" state="'.$column_it->get('State').'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'" modifiable="'.$modifiable.'" entity="'.$entity_ref_name.'" modified="'.$column_it->get('AffectedDate').'" uid="'.$uid.'">';
							echo '<div class="board_item_separator" group="'.$group_key.'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'">&nbsp;</div>';
							echo '<div class="board_item_body" style="'.$style.'">';
							if ( $spinner != '' ) echo '<div class="board_item_spinner" style="'.$spinner.'">&nbsp;</div>';
							echo '<div class="board_item_attributes">';
							echo '<div class="item_attrs" style="display:none;" modified="'.$column_it->get_native('RecordModified').'"></div>';

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

                        $this->drawItemMenu($column_it);
						echo '</div>';
						echo '</div>';
					}
					echo '</div>';
                    echo '</td>';
				}
				echo '</tr>';

                if ( is_array($new_action) )
                {
                    echo '<tr>';
                    foreach( $row as $prev_board_index => $columns ) {
                        $parms = array('State' => $board_values[$prev_board_index]);
                        if ($group_field != '') $parms[$group_field] = $group_key;

                        $url = preg_replace_callback('/({[^}]*})/', function ($matches) use ($parms) {
                            return str_replace('"', "'",
                                json_encode(
                                    array_merge(
                                        json_decode(
                                            str_replace("'", "\"", $matches[1]), true
                                        ), $parms
                                    ), JSON_HEX_APOS)
                            );
                        }, $new_action['url']);

                        echo '<td class="cell-add-btn"><a href="' . $url . '" class="btn btn-mini btn-success"><i class="icon-plus icon-white"></i></a></td>';
                    }
                    echo '</tr>';
                }
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
				var sizes = new Array('xs','s','m','l','xl');
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
			boardItemOptions.saveButtonName = '<? echo translate('Сохранить') ?>';
			boardItemOptions.closeButtonName = '<? echo translate('Отменить') ?>';
			boardItemOptions.transitionTitle = '<? echo text(1011) ?>';
			boardItemOptions.locked = false;
			boardItemOptions.groupAttribute = '<? echo $group_field; ?>';
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
		
		$plugins = getSession()->getPluginsManager();
	    
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

		echo $view->render("core/PageBoard.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}

	function getMaxOnPage()
	{
		$values = $this->getFilterValues();
		return in_array($values['rows'], array('all',''))
				? 9999
				: (is_numeric($values['rows'])
						? $values['rows']
						: 9999
				);
	}
}