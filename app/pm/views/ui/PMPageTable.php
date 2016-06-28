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
	private $traces = array();

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
    
    function hasCrossProjectFilter()
    {
		if ( getFactory()->getObject('SharedObjectSet')->sharedInProject($this->getObject(), getSession()->getProjectIt()) ) {
	    	return getSession()->getProjectIt()->get('LinkedProject') != '';
		}
		else {
			return true;
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

	function getBulkActions()
	{
		$actions = parent::getBulkActions();

		$object = $this->getObject();
		$visibleReferences = array();
		$attributes = array_diff(
			array_merge(
				array (
					'Tasks'
				),
				$object->getAttributesByGroup('trace')
			),
			$object->getAttributesByGroup('skip-network')
		);
		foreach( $attributes as $attribute ) {
			if ( $object->IsReference($attribute) ) {
				$visibleReferences[$attribute] = $object->getAttributeObject($attribute);
			}
		}

		$widgetActions = array();
		$it = getFactory()->getObject('ObjectsListWidget')->getAll();
		while( !$it->end() )
		{
			foreach( $visibleReferences as $attribute => $reference ) {
				if ( is_a($reference, $it->get('Caption')) ) {
					$widgetActions[] = array (
						'name' => $object->getAttributeUserName($attribute),
						'url' => "javascript: showTraces('".$attribute."')"
					);
				}
			}
			$it->moveNext();
		}
		if ( count($widgetActions) > 0 ) {
			if ( count($actions['action']) > 0 ) {
				$actions['action'][] = array();
			}
			$actions['action'] = array_merge($actions['action'], $widgetActions);
		}

		return $actions;
	}

    function getFilterActions()
    {
		$base_actions = parent::getFilterActions();
        if ( count($base_actions) < 1 ) return $base_actions;

        $this->buildSaveAsAction($base_actions);
		$this->buildQuickReports($base_actions);
        	
        return $base_actions;
    }

	function getFiltersUrl()
	{
		$url = '';
		$values = $this->getFilterValues();
		foreach ( $values as $key => $value ) {
			$url .= $key.'='.$value.'&';
		}

		if ( $this->getReport() != '' ) {
			$action_url = getFactory()->getObject('PMReport')->getExact($this->getReport())->getUrl($url);
		}
		else if ( $this->getPage()->getModule() != '' ) {
			$action_url = getFactory()->getObject('Module')->getExact($this->getPage()->getModule())->getUrl($url);
		}
		if ( $action_url == '' ) return $action_url;

		return EnvironmentSettings::getServerUrl().$action_url;
	}

	function getFilterUsers( $selectedValue, $allValues )
	{
		if ( !in_array($selectedValue,array('all','')) ) return $selectedValue;

		$parms = array();
		$additionalValues = array();
		if ( !in_array($allValues['usergroup'],array('','all')) ) {
			$parms[] = new EEUserGroupPredicate($allValues['usergroup']);
			if ( strpos($allValues['usergroup'],'none') !== false ) {
				$additionalValues[] = 'none';
			}
		}
		if ( !in_array($allValues['userrole'],array('','all')) ) {
			if ( defined('PERMISSIONS_ENABLED') && !getSession()->getProjectIt()->IsPortfolio() ) {
				$parms[] = new UserParticipanceRolePredicate($allValues['userrole']);
			}
			else {
				$parms[] = new UserRolePredicate($allValues['userrole']);
			}
			if ( strpos($allValues['userrole'],'none') !== false ) {
				$additionalValues[] = 'none';
			}
		}
		if ( count($parms) < 1 ) return $selectedValue;

		return join(',',array_merge($additionalValues, getFactory()->getObject('User')->getRegistry()->Query($parms)->idsToArray()));
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

		$custom = getFactory()->getObject('pm_CustomReport');
        if ( !getFactory()->getAccessPolicy()->can_create($custom) ) return $actions;

		$report = getFactory()->getObject('PMReport');
        if ( $this->getReport() != '' ) {
            $action_url = $custom->getSaveUrl('', $report->getExact( $this->getReportBase()));
        }
        else if ( $this->getPage()->getModule() != '' ) {
            $action_url = $custom->getSaveUrl('', getFactory()->getObject('Module')->getExact( $this->getPage()->getModule()));
        }
        if ( $action_url == '' ) return $actions;
        
        $actions[] = array();
             
		$actions[] = array ( 
			'name' => text(1829),
			'title' => text(1830),
			'url' => "javascript: window.location = '".$action_url."&Url='+encodeURIComponent('".trim($url, '&')."');",
			'uid' => 'save-report'
        );
        
        return $actions;
    }
        
    function buildSaveAsAction( & $base_actions )
    {
    	$save_action_key = '';
        foreach( $base_actions as $key => $menuitem ) {
            if ( $menuitem['id'] == 'save' ) {
            	$save_action_key = $key; break;
            }
        }
        if ( $save_action_key == '' ) return;

		$save_actions = $this->getSaveActions();

		$base_actions[$save_action_key]['items'] =
			array_merge($base_actions[$save_action_key]['items'], $save_actions);

		if ( is_numeric($this->getReport()) )
		{
			$custom_it = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
				array (
					new FilterInPredicate($this->getReport()),
					new FilterAttributePredicate('Author', getSession()->getUserIt()->getId())
				)
			);
			if ( $custom_it->getId() > 0 ) {
				$store = new ReportModifyWebMethod( $custom_it );
				if ( $store->hasAccess() ) {
					$store->setRedirectUrl("function() { $('.alert-filter').hide(); }");
					$base_actions[$save_action_key]['items']['personal-persist'] =
						array (
							'uid' => 'personal-persist',
							'name' => $store->getCaption(),
							'url' => $store->getJSCall($this->getFilterValues())
						);

					array_unshift( $base_actions[$save_action_key]['items'], array() );
					array_unshift(
						$base_actions[$save_action_key]['items'],
						array (
							'name' => translate('Редактировать'),
							'url' => $custom_it->getEditUrl()
						)
					);

					$method = new DeleteObjectWebMethod($custom_it);
					if ( $method->hasAccess() )
					{
						$item = getFactory()->getObject('PMReport')->getExact($custom_it->get('ReportBase'))->buildMenuItem();
						$method->setRedirectUrl( $item['url'] );

						$base_actions[$save_action_key]['items'][] = array();
						$base_actions[$save_action_key]['items'][] = array (
							'name' => translate('Удалить'),
							'url' => $method->getJsCall()
						);
					}
				}
			}
		}

		$service = new WorkspaceService();

		$report_id = $this->getPage()->getReport();
		$module_id = $this->getPage()->getModule();
		if ( $report_id == '' && $module_id == '' ) return;

		$widget_id = $report_id != '' ? $report_id : $module_id;
		if ( count($service->getItemOnFavoritesWorkspace(array($widget_id))) > 0 ) return;

		$widget_it = getFactory()->getObject($report_id != '' ? 'PMReport' : 'Module')->getExact($widget_id);
		$info = $widget_it->buildMenuItem();

		$base_actions[$save_action_key]['items'][] = array();
		$base_actions[$save_action_key]['items'][] = array(
			'uid' => 'add-favorites',
			'name' => text(1327),
			'url' => "javascript:addToFavorites('".$widget_it->getId()."','".urlencode($info['url'])."', '".($report_id != '' ? 'report' : 'module')."');"
		);
    }
    
	protected function buildQuickReports(& $base_actions)
	{
		$report = getFactory()->getObject('PMReport');
		$reports = array();
		$self = array($this->getReport(), $this->getReportBase());

		$modules = array(
			$this->getPage()->getModule()
		);
		if( $modules[0] == 'issues-backlog' ) {
			$modules[] = 'issues-board';
			$modules[] = 'kanban/';
		}
		if( $modules[0] == 'issues-board' ) {
			$modules[] = 'issues-backlog';
		}
		if( $modules[0] == 'tasks-list' ) {
			$modules[] = 'tasks-board';
		}
		if( $modules[0] == 'tasks-board' ) {
			$modules[] = 'tasks-list';
		}

		$report_it = $report->getByRef('Module', $modules);
		while( !$report_it->end() ) {
			if ( in_array($report_it->getId(),$self) ) {
				$report_it->moveNext();
				continue;
			}
			$reports[$report_it->getId()] = array (
				'name' => $report_it->getDisplayName(),
				'url' => $report_it->getUrl(),
				'uid' => $report_it->getId()
			);
			$report_it->moveNext();
		}

		if ( $this->getReport() == '' ) {
			array_shift($modules); // skip currently displayed module
		}

		$module_it = getFactory()->getObject('Module')->getExact($modules);
		while( !$module_it->end() ) {
			$title = $module_it->getDisplayName();
			$sameReports = array_filter($reports, function($report) use($title) {
				return $report['name'] == $title;
			});
			if ( count($sameReports) < 1 ) {
				$reports[$module_it->getId()] = array (
					'name' => $title,
					'url' => $module_it->getUrl(),
					'uid' => $module_it->getId()
				);
			}
			$module_it->moveNext();
		}

		usort($reports, function( $left, $right ) {
			if ( $left['name'] == $right['name'] ) return 0;
			if ( $left['name'] < $right['name'] ) return -1;
			return 1;
		});

		if ( count($reports) > 0 ) {
			array_unshift($base_actions, array());
			array_unshift($base_actions, array (
				'name' => text(2136),
				'items' => $reports
			));
		}
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

    	if ( !$this->hasCrossProjectFilter() ) $predicates[] = new FilterBaseVpdPredicate();

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
        	
            if ( in_array($type, array('dictionary','reference')) ) {
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
						$values,
						$this->getPage()->getSettingsBuilder()->getByPageTable($this)
        		)
        );

		// override default filter values with specific ones for the given report
		if ( $this->getReport() != '' )
		{
			$values = array_merge(
					$values,
					$this->buildFilterValuesBySettings(
							$values,
							$this->getPage()->getSettingsBuilder()->getByReport($this->getReportBase())
        			)
			);
			$query_string = getFactory()->getObject('PMReport')->getExact($this->getReport())->get('QueryString');
		}

		if ( $query_string != '' ) {
			foreach( preg_split('/\&/', $query_string) as $query ) {
				list($query_parm, $query_value) = preg_split('/\=/' ,$query);

				if ( $query_parm == 'infosections' ) continue;
				if ( $query_parm == 'hide' ) {
					$values[$query_parm] = join('-', array($values[$query_parm], $query_value));
				}
				else {
					$values[$query_parm] = $query_value;
				}
			}
			$values['hide'] = join('-',array_diff(preg_split('/-/',$values['hide']), preg_split('/-/',$values['show'])));
		}

		return $values;
	}
	
	public function buildFilterValuesBySettings( $default_values, & $setting )
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
		    		if ( $default_values[$filter] != '' ) continue;
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
        $filters = parent::getFilters();
        
        if( !is_object($this->getObject()) ) return $filters;
        
        // filters driven by custom attributes
        $filters = array_merge($filters, $this->buildCustomFilters());

        $filter = $this->buildProjectFilter();
        if ( is_object($filter) ) $filters[] = $filter;

	    switch ( $this->getObject()->getEntityRefName() )
	    {
	        case 'pm_ChangeRequest':
	        case 'pm_Task':
	        case 'WikiPage':
	        	$filter = $this->buildFilterWatcher();
	        	if ( is_object($filter) ) $filters[] = $filter;
	        	break;
	    }
	    
        return $filters;
    }
    
    protected function buildProjectFilter()
    {
   		$project = getFactory()->getObject('pm_Project');

        if ( !$this->hasCrossProjectFilter() ) {
            if ( getSession()->getProjectIt()->IsProgram() ) {
                $project->addFilter(new ProjectCurrentPredicate());
                $filter = new FilterObjectMethod($project, translate('Проект'), 'target');
                $filter->setUseUid(false);
                $filter->setHasAll(false);
                $filter->setHasNone(false);
                $filter->setDefaultValue(getSession()->getProjectIt()->getId());
                return $filter;
            }
        }

		$project_it = getSession()->getProjectIt();
		if ( $project_it->IsPortfolio() || $project_it->IsProgram() )
		{
			$ids = $project_it->getRef('LinkedProject')->fieldToArray('pm_ProjectId');

			if ( !$project_it->IsPortfolio() ) $ids[] = $project_it->getId();
			$project->addFilter( new FilterInPredicate($ids) );

			$filter = new FilterObjectMethod( $project, translate('Проект'), 'target' );
			$filter->setHasNone(false);
			$filter->setUseUid(false);
		}

   		return $filter;
    }
    
    protected function buildCustomFilters()
    {
    	$filters = array();
    	
        $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
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

	protected function buildUserGroupFilter()
	{
		if ( !class_exists('UserGroup') ) return null;
		return new FilterObjectMethod( getFactory()->getObject('UserGroup'), text('user.group.name'), 'usergroup' );
	}

	protected function buildUserRoleFilter( $title = '' )
	{
		if ( $title == '' ) $title = text(2182);
		if ( defined('PERMISSIONS_ENABLED') && !getSession()->getProjectIt()->IsPortfolio() ) {
			$object = getFactory()->getObject('ProjectRole');
			$object->addFilter( new ProjectRoleInheritedFilter() );
		}
		else {
			$object = getFactory()->getObject('ProjectRoleBase');
		}
		$filter = new FilterObjectMethod( $object, $title, 'userrole' );
		$filter->setHasNone(false);
		return $filter;
	}

	function getSortAttributeClause( $field )
	{
		$parts = preg_split('/\./', $field);
		if ( $parts[0] == 'Project' ) {
			return new SortProjectImportanceClause($field);
		}
		return parent::getSortAttributeClause($field);
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

	function getFullPageRenderParms( $parms )
	{
		return array_merge(
				parent::getFullPageRenderParms( $parms ),
				array(
					'module_url' => htmlentities($this->getFiltersUrl())
				)
		);
	}

	public function getTraces( $class = '' ) {
		return $this->traces[$class];
	}

	public function cacheTraces( $attribute )
	{
		$it = $this->getListRef()->getIteratorRef();

		$items = array();
		foreach( preg_split('/,/',join(',',$it->fieldToArray($attribute))) as $trace ) {
			list($class, $id, $baseline) = preg_split('/:/',$trace);
			if ( $class == '' ) continue;
			$items[$class][] = $id;
		}
		foreach( $items as $class => $ids ) {
			$this->traces[$class] = getFactory()->getObject($class)->getExact($ids);
		}
	}
}