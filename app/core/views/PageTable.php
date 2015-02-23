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
 	    $this->getListRef()->setIterator( $dummy );
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
		$values = $this->getFilterValues();
		
		foreach( $this->getFilterParms() as $parm ) unset($values[$parm]);
		
		if ( count($values) < 5 )  return array('any');
		
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
	    if ( count($this->filters) > 0 ) return;
	
	    $this->filters = $this->getFilters();
	
	    $plugins = getSession()->getPluginsManager();
	    
	    $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSection()) : array();
	
	    foreach( $plugins_interceptors as $plugin )
	    {
	        $plugin->interceptMethodTableGetFilters( $this, $this->filters );
	    }
	}
 	
	function getFilterValues()
	{
		$this->buildFilters();
		
		if ( is_array($this->filter_values) ) return $this->filter_values;

		// filter parms driven by filters
	    $values = array();
	    
		$this->filter_values = $this->buildFilterValuesByDefault($this->filters);

		// filter parms driven by other parameters (rows, group, sort)
		$filter = $this->getPersistentFilter();

		foreach( array_merge(array_keys($this->filter_values), $this->getFilterParms()) as $parm )
		{
		    $filter_value = is_object($filter) ? $filter->getValue($parm) : '';

		    if ( $filter_value == '' && !array_key_exists($parm, $_REQUEST) ) continue;

		    if ( is_object($filter) && $parm == 'hide' )
		    {
		    	// backward compatibility
 	    		$columns = preg_split('/-/', $filter->getValue('show'));

 	    		$filter_value = join('-',array_diff(array_keys($this->getObject()->getAttributes()), $columns));
		    }
		    
			$this->filter_values[$parm] = array_key_exists($parm, $_REQUEST) 
					? $_REQUEST[$parm] 
					: ( is_object($filter) ? $filter_value : $this->filter_values[$parm]);
		}

		if ( !in_array($this->filter_values['infosections'], array('', 'none')) )
		{
			$temp = preg_split('/,/', $this->filter_values['infosections']);
			
			$sections = array_intersect($temp, array_keys($this->getPage()->getInfoSections()));
			
			$this->filter_values['infosections'] = count($sections) > 0 ? join(',', $sections) : 'none'; 
		}
		
		return $this->filter_values;
	}
	
	public function buildFilterValuesByDefault( & $filters )
	{
		$values = array();
		
		foreach ( $filters as $filter )
		{
			$filter->setFreezeMethod( $this->getPersistentFilter() );
			
			$value = $filter->getValue();
			    
			$values[$filter->getValueParm()] = $value;	
		}
		
		foreach( array('sort', 'sort2', 'sort3', 'sort4') as $parm )
		{
		    $values[$parm] = $this->getSortDefault($parm);
		}
		
		$values['color'] = $this->getDefaultColorScheme();
		
		$values['infosections'] = join(',', $this->getSectionsDefault());
		
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
			'chartlegend', 'chartdata', 'addobjects', 'color' );
	}
	
	function IsFilterPersisted()
	{
	    $persisted = $this->getPersistentFilter();
	    
	    if ( !array_key_exists('filterlocation', $_REQUEST) ) return true;
	     
	    $parms = $this->getFilterParms();
	    
	    foreach( $parms as $parm )
	    {
	        if ( $parm == 'infosections' && $persisted->getValue($parm) == '' ) continue;
	        
	        if ( $this->filter_values[$parm] != $persisted->getValue($parm) )
	        {
	            return false;
	        }
	    }
	    
	    foreach ( $this->filters as $filter )
	    {
	        if ( !in_array($_REQUEST[$filter->getValueParm()], array('hide','all')) ) continue;
	        
	        if ( $_REQUEST[$filter->getValueParm()] != $persisted->getValue($filter->getValueParm()) )
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
		if ( $this->filter_values['rows'] == 'all' )
		{
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
		
		return count($this->filter_values) < 11;
	}

	function getActions()
	{
		return array();
	}
	
	function getDeleteActions()
	{
		if( !$this->IsNeedToDelete() ) return array(); 
		
		$actions = array();
		
		$method = new BulkDeleteWebMethod();
		
		$actions[] = array ( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall($this->getObject()),
				'title' => $method->getDescription() 
		);
		
		return $actions;
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
		
		$object = $this->getObject();

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

		// info sections
		$sections = array();
		
		$values = $this->getFilterValues();
		
		$active = preg_split('/,/', $values['infosections']);
		
		$infosections = $this->getPage()->getInfoSections();
		
		foreach ( $infosections as $key => $section )
		{
			$infosections[$key]->setClosable();
			
			$checked = in_array($key, $active);
			
			$script = "javascript: $(this).hasClass('checked') ? filterLocation.turnOn('infosections', '".$key."', 0) : filterLocation.turnOff('infosections', '".$key."', 0); ";
			
			$sections[$section->getCaption()] = array ( 'url' => $script, 'checked' => $checked );
		}
		
		ksort($sections);
		$section_actions = array();
		
		foreach ( $sections as $caption => $section )
		{
			array_push( $section_actions, 
				array ( 'url' => $section['url'], 'name' => $caption, 
						'checked' => $section['checked'], 'multiselect' => true )
			);
		}
		
		if ( count($section_actions) > 0 )
		{
			array_push($actions, array ( 'name' => translate('Секции'), 
				'items' => $section_actions , 'title' => '' ) );
		}
		
		$filter = $this->getPersistentFilter();

		if ( is_object($filter) )
		{
		    $persisted = $filter->compareStored($this->filter_values);
		    
			$save_actions = array(
				array ( 'url' => $filter->getJSCall(
				                    "li[uid=personal-persist]>a", 
				                    "function() { $('.alert-filter').hide(); ".($persisted ? "window.location.reload();" : "")." }"
				                 ), 
				        'name' => $filter->getCaption(),
				    	'title' => $filter->getDescription(),
				        'checked' => $persisted,
				        'multiselect' => true,
				        'uid' => 'personal-persist' 
				      )
			);
		}

		array_push($actions, array());
		
		array_push($actions, array ( 
			'name' => translate('Сохранить'), 
			'items' => $save_actions,
			'id' => 'save'
		));
		
		$list->buildFilterActions( $actions );
				    	
		return $actions;
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
	    $values = $this->getFilterValues();
	    
	    return $values['rows'] == 'all' ? 9999 : (is_numeric($values['rows']) ? $values['rows'] : $this->getDefaultRowsOnPage()); 
	}

 	function getDefaultRowsOnPage()
	{
		return 999;
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

	function draw( &$view = null )
	{
		$view_filter = $this->getViewFilter();
		
		if ( !is_object($view_filter) )
		{
		    $this->getListRef()->setupColumns();
		    
			parent::draw($view);
			
			return;
		}
		
		$view_filter->setFilter( $this->getFiltersName() );
		
		if ( $view_filter->getValue() != '' )
		{
			$it = $this->getListIterator();

			$this->setList( $this->getList( $view_filter->getValue(), $it ) );
		}
		
		$this->getListRef()->setupColumns();
		
		parent::draw($view);
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
		$list = $this->getListRef();
		
		if ( !is_object($list) ) return;
		
		if ( is_a($list, 'PageChart') || is_a($list, 'PageBoard') )
		{
			return;
		}
	}
	
	function getDescription()
	{
	    return "";
	}
	
	function getRenderParms( $parms )
	{
		$view_filter = $this->getViewFilter();
	
		if ( is_object($view_filter) )
		{
			$view_filter->setFilter( $this->getFiltersName() );
		}

		if ( $_REQUEST['view'] != '' )
		{
			$list = $this->getList( $_REQUEST['view'] );
            $this->setList( $list );
		}
    
	    $list = $this->getListRef();
    	
        if ( is_object($list) )
        {
            $list->setupColumns();
            
            $list->retrieve();
        }
        
		$parms = array_merge($parms, array(
			'table' => $this,
			'title' => $parms['navigation_title'] == $parms['title'] ? '' : $parms['title'],
            'list' => $list
		));

		if ( $this->getPage()->showFullPage() )
		{
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
    	            $script = "javscript: $(this).hasClass('checked') ? filterLocation.turnOn('".$filter->getName()."', '".urlencode(trim($key))."', 0) : filterLocation.turnOff('".$filter->getName()."', '".urlencode(trim($key))."', 0);";
    	            
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
                        'checked' => $checked_item || $checked_all
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
	        
	        if ( strlen($title) > 20 ) $title = substr($title, 0, 20).'...'; 
	        
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
	    
		$plugins = getSession()->getPluginsManager();
	    
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

	    if ( is_array($parms['sections']) )
	    {
	        $values = $this->getFilterValues();
	        
    		$sectionnames = array_keys($parms['sections']);
    		
    	    $sectionnames = preg_split('/,/', $values['infosections']);
    
    		foreach ( $parms['sections'] as $key => $section ) 
    		{
    			if ( !in_array($key, $sectionnames) ) unset($parms['sections'][$key]);
    		}
	    }

		return array_merge($parms, array(
            'filter_items' => $filter_items,
            'filter_modified' => !$this->IsFilterPersisted(),
            'actions' => $actions,
            'additional_actions' => $additional_actions,
			'save_settings_alert' => $this->buildSaveSettingsAlert()
		));
	}
	
	function buildSaveSettingsAlert()
	{
		$personal_script = "javascript: $('li[uid=personal-persist]>a').addClass('checked'); window.location = $('li[uid=personal-persist]>a[href]').length > 0 ? $('li[uid=personal-persist]>a').attr('href') : $('li[uid=personal-persist]>a').attr('onkeydown')";

		return str_replace('%2', getFactory()->getObject('Module')->getExact('profile')->get('Url'), 
							str_replace('%1', $personal_script, text(1318))
				); 
	}
	
	function getTemplate()
	{
		return "core/PageTable.php";
	}
	
	function render( &$view, $parms )
	{
	    $parms = $this->getRenderParms($parms);
	    
		$this->view = $view;
	    
		echo $view->render( $this->getTemplate(), $parms ); 
	}
}
