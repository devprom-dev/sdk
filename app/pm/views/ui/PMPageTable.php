<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

include_once SERVER_ROOT_PATH."pm/methods/c_common_methods.php";

include "PMPageList.php";
include "PMStaticPageList.php";
include "PMPageChart.php";
include "PMPageBoard.php";

include_once SERVER_ROOT_PATH."core/classes/versioning/VersionedObject.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/predicates/WatcherUserPredicate.php";

class PMPageTable extends PageTable
{
    function PMPageTable( $object )
    {
        parent::PageTable( $object );
    }
    
  	function getSection()
 	{
 	    return 'pm';
 	}
 	
	function getDescription()
	{
	    global $_REQUEST, $model_factory;
	    
	    if ( $this->getReport() == '' ) return parent::getDescription();
	    
        $report = $model_factory->getObject('PMReport');
        
        $report_it = $report->getExact( $this->getReport() );
         
        return $report_it->get('Description');
	}
    
    function getCaption()
    {
        if ( $this->getReport() == '' ) return '';

        $title = getFactory()->getObject('PMReport')->getExact( $this->getReport() )->getDisplayName();
        
 		if ( !in_array($_REQUEST['baseline'], array('', 'none', 'all')) )
 		{
 			$title .= ' [rev: '.getFactory()->getObject('Snapshot')->getExact($_REQUEST['baseline'])->getDisplayName().']'; 
 		}
        
        return $title;
    }

    function getReport()
    {
        return $this->getPage()->getReport();
    }

    function getReportBase()
    {
        return $this->getPage()->getReportBase();
    }
    
    function IsFilterPersisted()
    {
        if ( is_numeric($this->getReport()) )
        {
            return true; // persistence of the report will be checked other way 
        }
        
        return parent::IsFilterPersisted();
    }
    
    function hasCrossProjectFilter()
    {
    	$board = $this->getListRef();
		
		if ( $board instanceof PageBoard )
		{
			return false;
		}
		else
		{
	    	return getSession()->getProjectIt()->get('LinkedProject') != ''  
	    				&& getFactory()->getObject('SharedObjectSet')
	    						->sharedInProject($this->getObject(), getSession()->getProjectIt());
		}
    }
    
    function getActions()
    {
    	$actions = parent::getActions();
    	
    	$v_actions = $this->getVersioningActions();
    	
    	if ( count($v_actions) > 0 )
    	{
    		$actions[] = array();
    		$actions = array_merge($actions, $v_actions);
    	}
    	
    	return $actions;
    }
    
    function getVersioningActions()
    {
    	return array();
    }

    function getPersistentFilter()
    {
    	if ( is_numeric($_REQUEST['report']) ) return null;
    	
    	return parent::getPersistentFilter();
    }
    
    function getFilterActions()
    {
        global $model_factory;
        
        $base_actions = parent::getFilterActions();
        	
        if ( count($base_actions) < 1 ) return $base_actions;
        
        $this->buildSaveAsAction($base_actions);
        
        $this->buildAddToFavoritesAction($base_actions); 
        	
        return $base_actions;
    }

    function getSaveActions()
    {
        $actions = array();

        $values = $this->getFilterValues();
        $url = '';

        foreach ( $values as $key => $value )
        {
            if ( $value != '' )
            {
                $url .= $key.'='.$value.'&';
            }
        }

        $report = getFactory()->getObject('PMReport');

        if ( !getFactory()->getAccessPolicy()->can_create($report) ) return;
        
        if ( $this->getReport() != '' )
        {
            $action_url = getFactory()->getObject('pm_CustomReport')->getPageNameObject(
            		'', $report->getExact( $this->getReport() )
        	);
        }
        else if ( $this->getPage()->getModule() != '' )
        {
            $action_url = getFactory()->getObject('pm_CustomReport')->getPageNameObject(
            		'', getFactory()->getObject('Module')->getExact( $this->getPage()->getModule() )
        	);
        }
        
        if ( $action_url == '' ) return;
        
        $actions[] = array();
             
		$actions[] = array ( 
			'name' => text(1829),
			'title' => text(1830),
			'url' => "javascript: window.location = '".$action_url."&Url='+encodeURIComponent('".trim($url, '&')."');"
        );
        
        return $actions;
    }
        
    function buildSaveAsAction( & $base_actions )
    {
    	$save_action_key = '';
    	
        foreach( $base_actions as $key => $menuitem )
        {
            if ( $menuitem['id'] == 'save' )
            {
            	$save_action_key = $key; break;
            }
        }
        
        if ( $save_action_key == '' ) return;
    	
        $save_actions = $this->getSaveActions();
        
		$base_actions[$save_action_key]['items'] = array_merge(
        		is_array($base_actions[$save_action_key]['items']) ? $base_actions[$save_action_key]['items'] : array(), 
				is_array($save_actions) ? $save_actions : array()
		);

		// additional actions for custom report
    	if ( !is_numeric($this->getReport()) ) return;
    	
        $custom_it = getFactory()->getObject('pm_CustomReport')->getExact($this->getReport());

        if ( $custom_it->getId() < 1 ) return;
        
        $store = new ReportModifyWebMethod( $custom_it );

        if ( !$store->hasAccess() ) return;
        
       	$base_actions[$save_action_key]['name'] = $store->getCaption();
                        
       	$base_actions[$save_action_key]['url'] = $store->getJSCall($this->getFilterValues(), 
           	"function() { $('.alert-filter').hide(); }");
                        
		$base_actions[$save_action_key]['uid'] = 'personal-persist';
                        
		unset($base_actions[$save_action_key]['items']);
                        
		$base_actions[] = array();
                        
		$base_actions[] = array (
           		'name' => translate('Редактировать'),
				'url' => $custom_it->getEditUrl()
		);
                        
		$report_it = getFactory()->getObject('PMReport')->getExact($custom_it->get('ReportBase'));
                        
		$method = new DeleteObjectWebMethod($custom_it);

		if ( $method->hasAccess() )
		{
			$item = $report_it->buildMenuItem();
	                        
			$method->setRedirectUrl( $item['url'] );

			$base_actions[] = array();
			$base_actions[] = array (
					'name' => translate('Удалить'),
					'url' => $method->getJsCall()
			);
		}
    }
    
    function buildAddToFavoritesAction( & $base_actions )
    {
    	$service = new WorkspaceService();
    	
    	$report_id = $this->getPage()->getReport();
    	
    	$module_id = $this->getPage()->getModule();
    	
    	if ( $report_id == '' && $module_id == '' ) return; 
    	
    	$widget_id = $report_id != '' ? $report_id : $module_id;
    	
    	$favs_it = $service->getItemOnFavoritesWorkspace($widget_id);

    	if ( $favs_it->getId() > 0 ) return;
    	
    	$save_action_key = '';
    	
        foreach( $base_actions as $key => $menuitem )
        {
            if ( $menuitem['id'] == 'save' )
            {
            	$save_action_key = $key; break;
            }
        }
        
        if ( $save_action_key == '' ) return;
    	
        $save_action_pos = array_search($save_action_key, array_keys($base_actions));
        
        $widget_it = getFactory()->getObject($report_id != '' ? 'PMReport' : 'Module')->getExact($widget_id);
        
        $info = $widget_it->buildMenuItem();
        
        array_splice($base_actions, $save_action_pos - 1, 0, 
        		array (
        			array(),
        			array(
        					'uid' => 'add-favorites',
        					'name' => text(1327),
        					'url' => "javascript:addToFavorites('".$widget_it->getId()."','".urlencode($info['url'])."', '".($report_id != '' ? 'report' : 'module')."');"
        			)
        		)
        );
    }
    
	function getFiltersName()
	{
		return md5($_REQUEST['report'].parent::getFiltersName());
	}
	
	function getFilterPredicates()
	{
		$predicates = array();

        $values = $this->getFilterValues();
        
        $predicates = array_merge($predicates, $this->buildCustomPredicates($values));

        $predicates[] = new ProjectVpdPredicate($values['target']);
        $predicates[] = new WatcherUserPredicate($values['watcher']);  
		
		if ( $values['baseline'] != '' )
		{
			// snapshot items predicate
			getFactory()->getObject('Snapshot');
			
			$predicates[] = new SnapshotObjectPredicate($values['baseline']);
		}
		
		return array_merge($predicates, parent::getFilterPredicates());
	}
	
	function buildCustomPredicates( $values )
	{
		$predicates = array();

		if ( !getFactory()->getObject('CustomizableObjectSet')->checkObject($this->getObject()) ) return $predicates;
		
        $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
        
        while( !$attr_it->end() )
        {
        	$type = $attr_it->getRef('AttributeType')->get('ReferenceName');
        	
        	$value = $attr_it->get('ReferenceName');
        	
            if ( in_array($type, array('dictionary','reference')) )
            {
            	$predicates[] = new CustomAttributeValuePredicate($value, $values[$value]);
            } 

            $attr_it->moveNext();
        }
        
        return $predicates;
	}
    
	public function buildFilterValuesByDefault( & $filters )
	{
		$values = parent::buildFilterValuesByDefault($filters);

		// overrride defaults with custom settings
		$values = array_merge(
				$values, 
				$this->buildFilterValuesBySettings(
						$this->getPage()->getSettingsBuilder()->getByPageTable($this)
        		)
        );

		// override default filter values with specific ones for the given report
		if ( $this->getReport() != '' )
		{
			$values = array_merge(
					$values, 
					$this->buildFilterValuesBySettings(
							$this->getPage()->getSettingsBuilder()->getByReport($this->getReportBase())
        			)
			);
			
			$query_string = getFactory()->getObject('PMReport')->getExact($this->getReport())->get('QueryString');
		}
		
		if ( $query_string == '' ) return $values;
		
		foreach( preg_split('/\&/', $query_string) as $query )
		{
			list($query_parm, $query_value) = preg_split('/\=/' ,$query);

			if ( $query_parm == 'hide' )
			{
				$values[$query_parm] = join('-', array($values[$query_parm], $query_value));
			}
			else
			{
				$values[$query_parm] = $query_value;
			}
		}
		
		$values['hide'] = join('-',array_diff(preg_split('/-/',$values['hide']), preg_split('/-/',$values['show'])));

		return $values;
	}
	
	public function buildFilterValuesBySettings( & $setting )
	{
		$values = array();

	 	if ( is_object($setting) )
	 	{
	    	$sorts = $setting->getSorts();
	    	
	    	if ( is_array($sorts) )
	    	{
		    	foreach( $sorts as $sort_key => $sort_value )
		    	{
		    		if ( $sort_value == '' ) continue;
		    	
		    		$values[$sort_key] = $sort_value;
		    	}
	    	}
	 	    	
	    	if ( is_array($setting->getFilters()) )
	    	{
		    	foreach( $setting->getFilters() as $filter )
		    	{
		    		$values[$filter] = 'all';
		    	}
	    	}
	 	    	
	    	if ( $setting->getRowsNumber() != '' ) $values['rows'] = $setting->getRowsNumber();
	 	}

		$alt_setting = $setting instanceof ReportSetting 
			? $setting 
			: $this->getPage()->getSettingsBuilder()->getByPageList($this->getListRef());
    	
 	    if ( is_object($alt_setting) )
 	    {
 	    	if ( $alt_setting->getGroup() != '' ) $values['group'] = $alt_setting->getGroup();
 	    	
 	    	$columns = $alt_setting->getVisibleColumns();
 	    	
 	    	if ( count($columns) > 0 )
 	    	{
 	    		$values['show'] = join('-',$columns);
 	    		$values['hide'] = join('-',array_diff(array_keys($this->getObject()->getAttributes()), $columns));
 	    	}
 	    	
 	    	$sections = $alt_setting->getSections();
 	    	
 	    	if ( count($sections) > 0 ) $values['infosections'] = join(',',$sections); 
 	    }
		
		return $values;
	}	
	
    function getFilters()
    {
        global $model_factory;
        	
        $filters = parent::getFilters();
        
        if( !is_object($this->getObject()) ) return $filters;
        
        // filters driven by custom attributes
        $filters = array_merge($filters, $this->buildCustomFilters());

    	if ( $this->hasCrossProjectFilter() && getSession()->getProjectIt()->get('LinkedProject') != '' )
	    {
	    	$filters[] = $this->buildProjectFilter();
	    }
        
	    switch ( $this->getObject()->getEntityRefName() )
	    {
	        case 'pm_ChangeRequest':
	        case 'pm_Task':
	        case 'WikiPage':
	        	$filters[] = $this->buildFilterWatcher();
	    }
	    
        return $filters;
    }
    
    protected function buildProjectFilter()
    {
   		$project = getFactory()->getObject('pm_Project');
  		$ids = getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('pm_ProjectId');

		if ( !getSession()->getProjectIt()->IsPortfolio() ) $ids[] = getSession()->getProjectIt()->getId();
   		$project->addFilter( new FilterInPredicate($ids) );
        
   		if ( count($ids) > 20 ) {
			$filter = new FilterAutocompleteWebMethod( $project, translate('Проект'), 'target' );
   		}
   		else {
			$filter = new FilterObjectMethod( $project, translate('Проект'), 'target' );
	        $filter->setUseUid(false);
   		}
        		
   		return $filter;
    }
    
    protected function buildCustomFilters()
    {
    	$filters = array();
    	
        $attr = getFactory()->getObject('pm_CustomAttribute');
        
        $attr_it = $attr->getByEntity( $this->getObject() );

        $dictionaries = array();

        while( !$attr_it->end() )
        {
            if ( $attr_it->getRef('AttributeType')->get('ReferenceName') == 'dictionary' )
            {
            	$filters[] = new ViewCustomDictionaryWebMethod( 
            			$this->getObject(), 
            			$attr_it->get('ReferenceName')
        		);
            }

            if ( $attr_it->getRef('AttributeType')->get('ReferenceName') == 'reference' )
            {
            	$filter = new FilterObjectMethod( 
            			getFactory()->getObject($attr_it->get('AttributeTypeClassName')),
            			$attr_it->getDisplayName(),
            			$attr_it->get('ReferenceName')
        		);
            	
            	$filters[] = $filter;
            }
            
            $attr_it->moveNext();
        }
    	
        return $filters;
    }
    
    protected function buildSnapshotFilter()
    {
    	$versioned = new VersionedObject();
    	
    	$versioned_it = $versioned->getExact(get_class($this->getObject()));
    	
    	if ( $versioned_it->getId() == '' ) return null;
    	
		$snapshot = getFactory()->getObject('SnapshotFilter');

		$snapshot->addFilter( new FilterAttributePredicate('ListName', $this->getId()) );
		
	    $filter = new FilterObjectMethod( $snapshot, '', 'baseline' );
	     
	    $filter->setHasNone( false );
	     
	    $filter->setHasAll( false );
	     
	    $filter->setType( 'singlevalue' );

	    return $filter;
    }
    
	protected function buildFilterWatcher()
	{
		$filter = new FilterObjectMethod( getFactory()->getObject('WatcherUser'), translate('Наблюдатели'), 'watcher' );
		$filter->setHasNone(false);
		return $filter;
	}
    
	function getSortFields()
	{
		$fields = parent::getSortFields();

		$fields = array_diff($fields, $this->getObject()->getAttributesByGroup('trace')); 
		
	    $system_attributes = $this->getObject()->getAttributesByGroup('system');
	    
	    if ( in_array('State', $system_attributes) ) unset( $system_attributes[array_search('State', $system_attributes)] );

		$fields = array_diff($fields, $system_attributes); 
		
		return $fields;
	}
}