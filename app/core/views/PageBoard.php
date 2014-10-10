<?php

define ('GROUP_STYLE_ROW', 'row');
define ('GROUP_STYLE_COLUMN', 'column');

class PageBoard extends PageList
{
    var $view;
    
    private $board_attribute_iterator = null;

 	private $state_names = array();
    
 	function PageBoard( $object ) 
	{
		parent::__construct( $object );
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
 		
 		return $this->board_attribute_iterator = $this->buildBoardAttributeIterator();
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
 		global $model_factory;
 		
 		if ( $this->object->IsReference($field_name) )
 		{
 			return $this->object->getAttributeObject( $field_name );
 		}
 		else
 		{
 		}
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
	    
	    $transition_actions = $form->getTransitionActions( $object_it );
	    
	    if ( count($transition_actions) < 1 ) return $actions;
	    
	    return array_merge(
	    		$actions,
	    		array(array()),
	    		$transition_actions		
	    );
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

		echo '<input type="checkbox" class="checkbox" name="to_delete_'.$object_it->getId().'" style="float:left;height:15px;">';
	}

	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'Caption':
				
			    echo '<div  style="clear:both;padding:3px 0 0 0;height:5.0em;line-height: 16px;word-wrap:none;overflow:hidden;">';
					echo $object_it->getWordsOnly('Caption', 16);
				echo '</div>';
				
				break;

			case 'UID':
			    
			    parent::drawCell( $object_it, $attr );
			    
			    break;
			    
			case 'State':
				
				echo $this->state_names[$object_it->get('State')];
				
				break;
				
			default:
				
			    if ( $object_it->get($attr) == '' ) return;

		        switch ( $object_it->object->getAttributeType($attr) )
		        {
		            case 'date':
		            case 'datetime':
		                
    					echo '<div style="padding:0 0 0 0;word-wrap:none;overflow:hidden;" title="'.translate($this->object->getAttributeUserName($attr)).'">';
    						echo '<img src="/images/date.png" style="float:left;margin:2px 3px 0 0;"> ';
    						echo '<div style="float:left;">';
    						    parent::drawCell( $object_it, $attr );
    						echo '</div>';
    					echo '</div>';
		                
    					break;
		                
		            default:
    					echo '<div style="clear:both;padding:0 0 0 0;word-wrap:none;overflow:hidden;">';
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
			$checked = !in_array($column, $active);
			
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
	
	function draw( & $view )
	{
		global $_REQUEST, $offset;

		$this->view = $view;
		
		$this->offset = $_REQUEST[$this->getOffsetName()] != '' ? $_REQUEST[$this->getOffsetName()] : 0;
		
		$this->it = $this->getIteratorRef()->copyAll();

		$it = $this->it;
    	
		$it->moveFirst();
    	
		$filter_values = $this->getFilterValues();
		
    	$columns_amount = 0;
    	
    	$sort_values = preg_split('/\./', $this->getTable()->getSort());
    	
    	$entity_ref_name = $it->object->getEntityRefName();
    	
    	$references = array();
    	
    	foreach( $this->getObject()->getAttributes() as $key => $attribute )
    	{
    		$references[$key] = $this->object->IsReference($key); 
    	}
    	
    	$modifiable = getFactory()->getAccessPolicy()->can_modify($this->getObject());
    	
		?>
		<table id="<?php echo $this->getId() ?>" class="table board-table" cellspacing="0" cellpadding="0" border="0">
		    <tbody>
			<tr>
			<?
			// в случае группировки группирующее поле отображается первым
			$group_field = $this->getGroup();
			
			// получим все названия опорного атрибута
			$board_names = $this->getBoardNames();
			
			// получим все значения опорного атрибута
			$board_values = $this->getBoardValues();
			
			foreach( preg_split('/,/',$filter_values['hiddencolumns']) as $ref_name )
			{
			    unset($board_names[$ref_name]);
			    unset($board_values[$ref_name]);
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
				echo '<th align=center class=list_header width="'.round(80 / (count($board_names)), 100).'%">';
					echo $title; 
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
			
			$board_cells = array();
			$rows_keys = array();
			$column_keys = array_flip($board_values);

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
					
					if ( !is_array($board_cells[$group_key]) )
					{
						$board_cells[$group_key] = array_pad(array(), count($board_values), array());
						$rows_keys[$group_key] = $it->getId();
					}
				}

				$board_cells[$group_key][$column][] = $it->copy();
				
				$it->moveNext();
			}			

			$columns_number = count($board_values) 
				+ ($group_field != '' && $this->getGroupStyle() == GROUP_STYLE_COLUMN ? 1 : 0);
			
			foreach( $board_cells as $group_key => $row )
			{
				if ( count(max($row)) < 1 || count(max($row)) > 0 && max(max($row)) < 1 ) continue;
				
				if ( $group_field != '' && $this->getGroupStyle() == GROUP_STYLE_ROW )
				{
					$it->moveToId( $rows_keys[$group_key] );
					
					echo '<tr class="info">';
						echo '<td class="board-group" colspan="'.$columns_number.'" style="background:'.$this->getGroupBackground($group_field, $it).'">';
							$this->drawGroup($group_field, $it);
						echo '</td>';
					echo '</tr>'; 
				}
				
				echo '<tr>';
				
				if ( $group_field != '' && $this->getGroupStyle() == GROUP_STYLE_COLUMN )
				{
					$it->moveToId( $rows_keys[$group_key] );
					
					echo '<td class="list_cell board-column" style="background:'.$this->getGroupBackground($group_field, $it).'">';
						$this->drawGroup($group_field, $it);
					echo '</td>'; 
				}
				
				foreach( $row as $prev_board_index => $columns )
				{
				    echo '<td class="board-column">';
				    
				    echo '<div class="list-left-cell">&nbsp;</div>';
					echo '<div class="list_cell board-column" more="'.$board_values[$prev_board_index].'" group="'.$group_key.'" sort="'.$sort_values[0].'">';
					
					foreach( $columns as $column_it )
					{
						$uid = htmlentities($this->getUidService()->getUidOnly($column_it), ENT_COMPAT | ENT_HTML401, 'windows-1251');
						
						$style = $this->getItemStyle( $column_it );
						
						$spinner = $this->getCardColor( $column_it );
						
						if ( $spinner != '' ) $spinner = 'background-color:'.$spinner.';border:1px solid '.$spinner;
		
						$order_num = $column_it->get('OrderNum') < 1 ? ($i + 1) : $column_it->get('OrderNum'); 
							
						echo '<div class="board_item" data-toggle="context" data-target="#context-menu-'.$column_it->getId().'" style="margin: 0 8px 0 0" project="'.ObjectUID::getProject($column_it).'" object="'.$column_it->getId().'" group="'.$group_key.'" more="'.$board_values[$prev_board_index].'" order="'.$order_num.'" modifiable="'.$modifiable.'" entity="'.$entity_ref_name.'" modified="'.$column_it->get('AffectedDate').'" uid="'.$uid.'">';
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

							$this->drawItemMenu($column_it);
							
							echo '</div>';
							
						echo '</div>';
						echo '</div>';
					}
					echo '</div>'; 
					echo '</td>';
				}
				echo '</tr>';
			}

			if ( $this->it->count() < 1 )
			{
				echo '<tr><td class="list_cell board-column" colspan="'.($columns_number).'" style="padding:6px;">';
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
				var columnWidth = Math.round(($('.board-table').width() - <? echo $xoffset ?>) / <? echo $columns ?>);

				for( var i = 1; i < 100; i++ ) {
					if ( (Math.floor(columnWidth / i) < 130) ) {
						itemsInColumn = i - 1;
						break;
					}
				}
				
				return itemsInColumn <= 1 
					? Math.max(columnWidth + 25, 115) 
					: Math.max((columnWidth / itemsInColumn), 130);
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
				var dropinfo = $(this).is('.board-column') ? $(this).children('.list_cell') : $(this);
				
				if ( draggable.attr('more') == "" )
				{
					return dropinfo.attr('more') == "<?php echo $board_values[0]; ?>" && dropinfo.attr('group') == draggable.attr('group');
				}
				else
				{
					if ( dropinfo.attr('more') != draggable.attr('more') && dropinfo.attr('group') != draggable.parent().attr('group') ) return false;

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
	
	function getWorkflowSettingsModule()
	{
		return null;
	}
	
	function getBoardHint()
	{
		$module_it = $this->getWorkflowSettingsModule();
		
		if ( !is_object($module_it) ) return '';
		
		if ( !getFactory()->getObject('UserSettings')->getSettingsValue('board-hint') != 'off' ) return '';
		
		$workflow_ref = '<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>';
			
		return preg_replace('/%1/', $workflow_ref, text(1836));
	}
	
	function getRenderParms()
	{
		return array_merge(
				parent::getRenderParms(),
				array (
						'hint' => $this->getBoardHint() 
				)
		);
	}
	
	function render( &$view, $parms )
	{
		echo $view->render("core/PageBoard.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}
}