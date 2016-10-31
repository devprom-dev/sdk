<?php

include SERVER_ROOT_PATH.'/cms/c_view.php';
 
include_once SERVER_ROOT_PATH.'core/methods/FilterFreezeWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/BulkDeleteWebMethod.php';
include_once SERVER_ROOT_PATH."core/methods/DeleteObjectWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/AutoSaveFieldWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterObjectMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterTextWebMethod.php";
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class PageTable extends ViewTable
{
 	var $rows, $filters, $filter_values, $infosections, $page, $view;
 	
 	private $system_attributes = array();
 	private $persistent_filter = null;
 	private $filter_defaults = array();
 	
 	function PageTable( $object )
 	{
		parent::ViewTable( $object );

 		$this->system_attributes = $this->buildSystemAttributes();
 	}
 	
 	function buildSystemAttributes()
 	{
	    $system_attributes = $this->getObject()->getAttributesByGroup('system');

	    if ( in_array('State', $system_attributes) ) unset( $system_attributes[array_search('State', $system_attributes)] );
 		
	    return $system_attributes;
 	}
 	
 	function getId()
 	{
 	    return md5(get_class($this));
 	}

 	function resetData()
 	{
 	    $this->getListRef()->setIterator(null);
 	}

	function getListIterator()
	{
		$it = parent::getListIterator();
		if ( $this->getMode() == 'chart' ) {
			return $this->getListRef()->buildDataIterator();
		}
		return $it;
	}

 	function setPage( & $page )
 	{
 	    $this->page = $page;
 	}
 	
 	function getPage()
 	{
 	    return $this->page;
 	}
 	
 	function getView()
 	{
 	    return $this->view;
 	}

	function getMode()
	{
		return $_REQUEST['view'];
	}

  	function getSection()
 	{
 	    return 'co';
 	}

 	function getSectionsDefault()
 	{
 		return array_keys($this->getPage()->getInfoSections());
 	}
 	
 	protected function getPersistentFilter()
 	{
 		if ( is_object($this->persistent_filter) ) return $this->persistent_filter;
 		
		$filter = new FilterFreezeWebMethod();
		
		$filter->setFilter( $this->getFiltersName() );
		
		return $this->persistent_filter = $filter;
 	}
 	
 	function getFilters()
 	{
 		return array();
 	}

	function getFiltersDefault()
	{
		if ( count($this->filters) < 5 )  return array('any');
		
		$values = $this->getFilterValues();
		foreach( $this->getFilterParms() as $parm ) unset($values[$parm]);
		
		$filters = array_filter($values, function($value) {
				return $value != '';
		});

		return count($filters) > 0 ? $filters : array('any');
	}
	
	function getFiltersName()
	{
		return md5(strtolower(get_class($this)));
	}
	
 	function getFilterPredicates()
 	{
 		return array();
 	}
 	
	function buildFilters()
	{
	    if ( count($this->filters) > 0 ) return $this->filters;
	
	    $this->filters = $this->getFilters();
	
	    $plugins = getFactory()->getPluginsManager();
	    
	    $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSection()) : array();
	
	    foreach( $plugins_interceptors as $plugin )
	    {
	        $plugin->interceptMethodTableGetFilters( $this, $this->filters );
	    }

	    return $this->filters;
	}
 	
	function getFilterValues()
	{
		$this->buildFilters();
		
		if ( is_array($this->filter_values) ) return $this->filter_values;

		// filter parms driven by filters
		$this->filter_values = $this->buildFilterValuesByDefault($this->filters);

		// apply persisted filters settings
		$persistent_filter = $this->getPersistentFilter();
		$filter_keys = array();
		foreach ( $this->filters as $filter )
		{
			$filter_name = $filter->getValueParm();
			$filter_keys[] = $filter_name;
			$filter->setFreezeMethod($persistent_filter);
			$filter->setFilter($this->getFiltersName());
			$value = $filter->getPersistedValue();
			if ( !is_null($value) && $value != '' ) {
				$this->filter_values[$filter_name] = $value;
				continue;	
			}
			$default_value = $filter->getValue();
			if ( $default_value == '' || array_key_exists($filter_name,$this->filter_values) ) continue; 
			$this->filter_values[$filter_name] = $default_value;
		}

		if ( is_object($persistent_filter) ) {
			// backward compatiibility to old settings
			foreach( $this->getFilterParms() as $parm )
			{
				if ( $parm == 'infosections' ) continue;
			    $filter_value = $persistent_filter->getValue($parm);
			    if ( $filter_value == '' ) continue;
			    if ( $parm == 'hide' )
			    {
			    	// backward compatibility
	 	    		$columns = preg_split('/-/', $persistent_filter->getValue('show'));
	 	    		$filter_value = join('-',array_diff(array_keys($this->getObject()->getAttributes()), $columns));
			    }
				$this->filter_values[$parm] = $filter_value;
			}
		}
		foreach( $this->filter_values as $key => $value ) {
            if ( $value == 'user-id' ) {
                $this->filter_values[$key] = getSession()->getUserIt()->getId();
            }
        }
		$this->filter_defaults = $this->filter_values;

		// apply web-session based filters settings
		foreach( array_merge($filter_keys, $this->getFilterParms()) as $parm )
		{
		    if ( !array_key_exists($parm, $_REQUEST) ) continue;
			$this->filter_values[$parm] = $_REQUEST[$parm];
		}

		return $this->filter_values;
	}
	
	public function buildFilterValuesByDefault( & $filters )
	{
		$values = array();
		
		foreach( array('sort', 'sort2', 'sort3', 'sort4') as $parm )
		{
		    $values[$parm] = $this->getSortDefault($parm);
		}
		$values['color'] = $this->getDefaultColorScheme();
		$values['infosections'] = join(',', $this->getSectionsDefault());

		foreach( $this->filters as $filter ) {
			if ( $filter instanceof FilterWebMethod && $filter->getDefaultValue() != '' ) {
				$values[$filter->getValueParm()] = $filter->getDefaultValue();
			}
		}

		return $values;
	}
	
	function resetFilterValues()
	{
		$this->filter_values = null;
	}
	
	function setFilterValue( $filter, $value )
	{
	    $this->filter_values[$filter] = $value;
	}
	
	function getFilterParms()
	{
		return array( 'rows', 'group', 'sort', 'sort2', 
			'sort3', 'sort4', 'show', 'hide', 'aggby', 
			'aggregator', 'infosections', 'hiddencolumns',
			'chartlegend', 'chartdata', 'addobjects', 'color',
			'groupfunc' );
	}
	
	function IsFilterPersisted()
	{
	    if ( !array_key_exists('filterlocation', $_REQUEST) ) return true;

	    foreach( array_keys($this->filter_values) as $parm )
	    {
	        if ( $parm == 'infosections' && $this->filter_defaults[$parm] == '' ) continue;
	        if ( $this->filter_values[$parm] != $this->filter_defaults[$parm] )
	        {
	            return false;
	        }
	    }
	    foreach ( $this->filters as $filter )
	    {
	        if ( !in_array($_REQUEST[$filter->getValueParm()], array('hide','all')) ) continue;
	        if ( $_REQUEST[$filter->getValueParm()] != $this->filter_defaults[$filter->getValueParm()] )
	        {
	            return false;
	        }
	    }

	    return true;
	}
	
	function IsNeedLinks()
	{	
		return false;
	}
	
	function IsNeedNavigator()
	{
		if ( $_REQUEST['rows'] == 'all' ) {
			return false;
		}
		if ( $_REQUEST['tableonly'] != '' ) return false;

		$list =& $this->getListRef();
		if ( is_object($list) )
		{		
			return $list->IsNeedNavigator();
		}
		
		return parent::IsNeedNavigator();
	}

	function IsFilterVisible( $filter )
	{
		$defaults = $this->getFiltersDefault();
		
		if ( $this->filter_values[$filter] == 'hide' ) return false;
		if ( $this->filter_values[$filter] != '' ) return true;
		
		if ( count($defaults) > 0 )
		{
			return in_array('any', $defaults) || in_array($filter, $defaults);
		}
		
		return count($this->filters) < 11;
	}

	function getActions()
	{
		return array();
	}

	function getExportActions()
	{
	}

	function getDeleteActions()
	{
		if( !$this->IsNeedToDelete() ) return array();
		if( !is_object($this->getListRef()) ) return array();
		if( !$this->getListRef() instanceof PageBoard ) return array();
		
		return array (
			array( 
					'name' => translate('Выбрать все'),
					'url' => 'javascript: checkRowsTrue(\''.$this->getListRef()->getId().'\');',
					'title' => text(969)
			)
		);
	}
	
	function getNewActions()
	{
		$actions = array();

		if( !$this->IsNeedToAdd() ) return $actions;

		$method = new ObjectCreateNewWebMethod($this->getObject());

		$method->setRedirectUrl('donothing');
		
		if ( $method->hasAccess() )
		{
			$uid = strtolower('new-'.get_class($this->getObject()));
			
			$actions[$uid] = array ( 
					'name' => translate('Добавить'),
					'uid' => $uid,
					'url' => $method->getJSCall(
									array( 
											'area' => $this->getPage()->getArea()
									)
							 ) 
			);
		}

		return $actions;
	}
	
 	function getFilterActions()
	{
		$actions = array();
		
		$list = $this->getListRef();
		if ( !is_object($list) ) return $actions;

		$list->setupColumns();

		// filters
		$filters = array();
		foreach ( $this->filters as $filter )
		{
			if ( !$filter->hasAccess() ) continue;
			
			$parm = $filter->getValueParm();
			
			if ( $parm == 'view' ) continue;
			
			$checked = $this->IsFilterVisible($parm) ? true : false;
			
			$script = "javascript: filterLocation.setup( '".$parm."=' + ($(this).hasClass('checked') ? 'all' : 'hide'), 0 ); ";
			$filters[$filter->getCaption()] = array ( 'url' => $script, 'checked' => $checked );
		}
		
		ksort($filters);
		$filter_actions = array();
		
		foreach ( $filters as $caption => $filter )
		{
			array_push( $filter_actions, 
				array ( 'url' => $filter['url'], 'name' => $caption, 
						'checked' => $filter['checked'], 'multiselect' => true )
			);
		}
		
		if ( count($filter_actions) > 0 )
		{
			array_push($actions, array ( 'name' => translate('Фильтры'), 
				'items' => $filter_actions , 'title' => '' ) );
		}

		$save_actions = array();

		$filter = $this->getPersistentFilter();
		if ( is_object($filter) )
		{
		    $persisted = $filter->compareStored($this->filter_values);
			$parms = array (
				'url' => $filter->url(
					"li[uid=personal-persist]>a",
					$persisted,
					"function() { $('.alert-filter').hide(); ".($persisted ? "filterLocation.restoreFilter();" : "")." }"
				),
				'name' => $persisted ? text(2112) : $filter->getCaption(),
				'title' => !$persisted ? $filter->getDescription() : '',
				'uid' => 'personal-persist'
			);
			if ( !$persisted ) {
				$parms['multiselect'] = true;
			}
			$save_actions = array( 'personal-persist' => $parms );
		}
		array_push($actions, array());

		array_push($actions, array (
			'name' => translate('Настройки'),
			'items' => $save_actions,
			'id' => 'save'
		));
		
		$list->buildFilterActions( $actions );
				    	
		return $actions;
	}
	
	function getBulkActions()
	{
		$action = new BulkAction($this->getObject());
		$action_it = $action->getAll();
		
		$workflow_actions = array();
		$delete_actions = array();
		$modify_actions = array();
		$custom_actions = array();
		
		$url = '?formonly=true';
		
		while( !$action_it->end() )
		{
			$action_url = "javascript:processBulk('".$action_it->get('Caption')."','".$url.'&operation='.$action_it->getId()."');";
			switch( $action_it->get('package') )
			{
			    case 'workflow':
			    	$workflow_actions[$action_it->get('ReferenceName')][] = array (
			    		'name' => $action_it->get('Caption'),
			    		'url' => $action_url
			    	);
			    	break;
			    case 'delete':
			    	$delete_actions[] = array (
			    		'uid' => 'bulk-delete',
			    		'name' => $action_it->get('Caption'),
			    		'url' => $action_url
			    	);
			    	break;
			    case 'modify':
			    	$modify_actions[] = array (
			    		'name' => $action_it->get('Caption'),
			    		'url' => $action_url
			    	);
			    	break;
				case 'action':
					$custom_actions[] = array (
						'name' => $action_it->get('Caption'),
						'url' => $action_url
					);
					break;
				case 'url':
					$custom_actions[] = array (
						'name' => $action_it->get('Caption'),
						'url' => $action_it->getId()
					);
					break;
			}
			
			$action_it->moveNext();
		}
		return array (
				'workflow' => $workflow_actions,
				'delete' => $delete_actions,
				'modify' => $modify_actions,
				'action' => $custom_actions
		);
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
	
	function getList( $mode = '' )
	{
		return null;
	}
	
	function setList( $list )
	{
		if ( is_subclass_of($list, 'PageList') )
		{
			$list->setTable( $this );
		}
		
		parent::setList( $list );
	}
	
	function getRowsOnPage()
	{
	    return $_REQUEST['rows'] == 'all'
				? 9999 : (
					is_numeric($_REQUEST['rows'])
							? $_REQUEST['rows']
							: $this->getDefaultRowsOnPage()
				);
	}

 	function getDefaultRowsOnPage() {
		return 100;
	}
	
 	function getSortFields()
	{
		$list = $this->getListRef();
		
		if ( !is_object($list) ) return array();
		
		if ( is_a($list, 'PageChart') ) return array();
	    
		$object = $this->getObject();
	    
		$attributes = array_keys($object->getAttributes());
		
	    $fields = array();
		
		foreach( $attributes as $key => $field )
		{
		    if ( $field == 'OrderNum' ) continue;
		    
		    $db_type = $object->getAttributeDbType($field);
		    
			if ( $db_type == '' ) continue;
			
			$type = $object->getAttributeType($field);

			if ( in_array($type, array('text','wysiwyg','password','file')) ) continue;
			
			if ( $object->getAttributeUserName($field) == '' ) continue;
			
			$fields[] = $field;
		}
		
		$fields = array_diff($fields, $this->system_attributes);		

		return $fields;
	}

 	function getSortAttributeClause( $field )
	{
	    $parts = preg_split('/\./', $field);

	    if ( !$this->getObject()->hasAttribute($parts[0]) ) return null;

		return new SortAttributeClause( $field );
	}
	
 	function getSortDefault( $sort_parm = 'sort' )
	{
		$sort = array_shift($this->getSortFields());
		
		return $sort != '' ? $sort : 'none';
	}
	
	function getSort( $sort_parm = 'sort' )
	{
	    $values = $this->getFilterValues();

		if ( $values[$sort_parm] == '' )
		{
			return $this->getSortDefault( $sort_parm );
		}
		else
		{
			return $values[$sort_parm];
		}
	}
	
	function getDefaultColorScheme()
	{
		return 'priority';
	}
	
	function getViewFilter()
	{
		return null;
	}

	function draw( $view = null )
	{
	}
	
	function drawScripts()
	{
		$values = $this->getFilterValues();
		
		?>
		<script type="text/javascript">
			filterLocation.visibleColumns = ['<? echo join(preg_split('/-/', trim($values['show'], '-')),"','") ?>'];
			filterLocation.hiddenColumns = ['<? echo join(preg_split('/-/', trim($values['hide'], '-')),"','") ?>'];

			<?php foreach( $values as $key => $value ) { ?>
			
			filterLocation.parms['<?=$key?>'] = '<?=$value?>';
			
			<?php } ?>
		</script>
		<?php				
	}
	
	function drawFooter()
	{
	}
	
	function getDescription()
	{
	    return "";
	}
	
	function getRenderParms( $parms )
	{
		$this->getListIterator();

		$parms = array_merge($parms, array(
			'table' => $this,
			'title' => $parms['navigation_title'] == $parms['title'] ? '' : $parms['title'],
            'list' => $this->getListRef()
		));

		if ( $this->getPage()->showFullPage() ) {
			$parms = array_merge($parms, $this->getFullPageRenderParms($parms));
		}
		
		return $parms;
	}
	
	function getFullPageRenderParms( $parms )
	{
	    $filter_values = $this->getFilterValues();
	     
	    $filter_items = array();

	    foreach ( $this->filters as $filter )
	    {
	        if ( !$filter->hasAccess() ) continue;
	    
	        $filter->setFilter( $this->getFiltersName() );
	        if ( is_object($filter->getFreezeMethod()) ) $filter->getFreezeMethod()->setValues($filter_values);
	    
	        if ( !$this->IsFilterVisible($filter->getValueParm()) ) continue;
	        
	        if ( !is_a($filter, 'FilterWebMethod') )
	        {
                ob_start();
                
                $filter->drawSelect();
                
                $html = ob_get_contents();
                
                ob_end_clean();
                
                $filter_items[] = array ( 
					'html' => $html
				);
                
                continue;
	        }
	        
	        $filter_value = $filter_values[$filter->getName()];
	        $title_items = array();
	        $actions = array();
	        
	        if ( $filter->getType() == 'singlevalue' )
	        {
    	        foreach( $filter->getValues() as $key => $value )
    	        {
    	            $script = "javascript: filterLocation.setup('".$filter->getName()."=".urlencode(trim($key))."', 1); ";
    	            
    	            $checked_item = in_array(trim($key), preg_split('/,/', $filter_value));
    	            
    	            $checked_all = in_array(trim($key), array('', 'all')) && in_array($filter_value, array('', 'all'));
    	            
    	            $actions[] = array(
	                    'name' => $value,
	                    'url' => $script,
                        'checked' => $checked_item || $checked_all
    	            );

                    if ( $checked_item && !$checked_all ) $title_items[] = $value;
    	        }
	        }
	        else
	        {
    	        $filter_options = $filter->getValues();
    	        
    	        $group_of_values_selected = false;
	        	foreach( $filter_options as $key => $value )
    	        {
    	            if ( count(preg_split('/,/', $key)) > 1 )
    	            {
    	            	$diff_items = 
    	            		count(
									array_diff(
											preg_split('/,/', trim($key)),
											preg_split('/,/', trim($filter_value)) 
   	                				)
	                		) + count(
									array_diff(
											preg_split('/,/', trim($filter_value)),
											preg_split('/,/', trim($key))
   	                				)
	                		);

    	            	$group_of_values_selected = $diff_items < 1;
    	            	
    	            	break; 
    	            }
    	        }
    	        
	            foreach( $filter_options as $key => $value )
    	        {
					if ( $key == 'search' ) {
						$actions[] = array(
							'uid' => $key
						);
						continue;
					}

    	            $script = "javscript: $(this).hasClass('checked') ? filterLocation.turnOn('".$filter->getName()."', '".trim($key)."', 0) : filterLocation.turnOff('".$filter->getName()."', '".trim($key)."', 0);";
    	            
    	            $group_of_values = count(preg_split('/,/', $key)) > 1; 
    	             
    	            if ( $group_of_values )
    	            {
    	            	// if group of values then append a separator
   	                	$actions[] = array();
    	            }

    	            $checked_item = 
    	            		count(
									array_diff(
											preg_split('/,/', trim($key)),
											preg_split('/,/', trim($filter_value))
   	                				)
   	                		) + count(
									array_diff(
											preg_split('/,/', trim($filter_value)),
											preg_split('/,/', trim($key))
   	                				)
   	                		) < 1
							|| !$group_of_values_selected && in_array(trim($key), preg_split('/,/', $filter_value));
    	             
                    $checked_all = in_array(trim($key), array('', 'all')) && in_array($filter_value, array('', 'all'));
    	            
    	            $actions[] = array(
                        'name' => $value,
                        'url' => $script,
                        'checked' => $checked_item || $checked_all,
						'uid' => in_array($key,array('none','')) ? $key : ''
    	            );

                    if ( count($filter_options) > 1 && ($key == '' || $key == 'all') )
                    {
                        // reset filter to nothing
                        $actions[count($actions)-1]['radio'] = '';
                        $actions[] = array();
                    }
                    else
                    {
                        if ( $group_of_values )
                        {
                            // check group of values
                            $actions[count($actions)-1]['radio'] = '';
                        }
                        else
                        {
                            $actions[count($actions)-1]['multiselect'] = '';
                        }                        
                    }             

                    if ( $checked_item && !$checked_all ) $title_items[] = $value;
    	        }
	        }

	        $title = join(',',$title_items);
	        
	        if ( mb_strlen($title) > 12 ) $title = mb_substr(html_entity_decode($title,ENT_QUOTES | ENT_HTML401,APP_ENCODING), 0, 12).'...';

	        $filter_items[] = array (
                'type' => $filter->getType(),
                'name' => $filter->getName(),
                'title' => count($title_items) > 0 ? $filter->getCaption().': '.$title : $filter->getCaption(),
                'actions' => $actions,
                'value' => $filter_value,
            	'description' => $this->getDescription()
	        );
	    }
	    
	    $additional_actions = array();
	    
	    $new_actions = $this->getNewActions();
	    
	    if ( count($new_actions) > 0 )
	    {
	    	$additional_actions[] = array (
				'name' => translate('Добавить'),
				'items' => $new_actions
			); 
	    }
	    
	    $actions = $this->getActions();

		$export_actions = $this->getExportActions();
		if ( count($export_actions) > 1 ) {
			$actions[] = array();
			$actions[] = array(
				'name' => translate('Экспорт'),
				'items' => $export_actions,
				'uid' => 'export'
			);
		}
		if ( count($export_actions) == 1 ) {
			$actions = array_merge($actions, $export_actions);
		}

		$plugins = getFactory()->getPluginsManager();
	    
		$plugins_interceptors = is_object($plugins) 
				? $plugins->getPluginsForSection(getSession()->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodTableGetActions( $this, $actions );
		}
	    
	    $delete_actions = $this->getDeleteActions();
   
	    if ( count($delete_actions) > 0 )
	    {
	    	if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
	    	
	    	$actions = array_merge($actions, $delete_actions);
	    }

	    if ( is_array($parms['sections']) ) {
	        $values = $filter_values;
    	    $sectionnames = preg_split('/,/', $values['infosections']);
    		foreach ( $parms['sections'] as $key => $section ) {
    			if ( !in_array($key, $sectionnames) ) unset($parms['sections'][$key]);
    		}
	    }

		return array_merge($parms, array(
            'filter_items' => $filter_items,
            'filter_modified' => !$this->IsFilterPersisted(),
            'actions' => $actions,
            'additional_actions' => $additional_actions,
			'bulk_actions' => $this->getBulkActions(),
			'save_settings_alert' => $this->buildSaveSettingsAlert()
		));
	}

	function buildSaveSettingsAlert()
	{
		$personal_script = "javascript:saveReportSettings();";
		return str_replace('%1', $personal_script, text(1318));
	}
	
	function getTemplate()
	{
		return "core/PageTable.php";
	}
	
	function render( $view, $parms )
	{
		$parms = $this->getRenderParms($parms);

		$this->view = $view;

		$this->touch();
		echo $view->render( $this->getTemplate(), $parms );

		$this->view = null;
	}

	function touch() {
		FeatureTouch::Instance()->touch($this->getPage()->getModule());
	}
}
