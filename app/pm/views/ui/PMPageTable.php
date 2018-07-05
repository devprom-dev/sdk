<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

include_once SERVER_ROOT_PATH."pm/methods/c_common_methods.php";

include "PMPageList.php";
include "PMStaticPageList.php";
include "PMPageChart.php";
include "PMPageBoard.php";

include_once SERVER_ROOT_PATH."core/classes/versioning/VersionedObject.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/predicates/WatcherUserPredicate.php";
include_once SERVER_ROOT_PATH."pm/classes/participants/predicates/UserParticipanceRolePredicate.php";


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
		$widget_it = $this->getWidgetIt();
		if ( $widget_it->getId() == '' ) return parent::getDescription();
	    return $widget_it->getDescription();
	}
    
    function getCaption()
    {
        $title = $this->getWidgetIt()->getDisplayName();
        
 		if ( !in_array($_REQUEST['baseline'], array('', 'none', 'all')) ) {
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

	function getWidgetIt()
	{
		if ( is_object($this->widget_it) ) return $this->widget_it;
		return $this->widget_it = $this->buildWidgetIt();
	}

	protected function buildWidgetIt()
	{
		$report = getFactory()->getObject('PMReport');
		if ( $this->getReport() == '' ) {
			$module = getFactory()->getObject('Module');
			if ( $this->getPage()->getModule() == '' ) return $module->getEmptyIterator();
			return $module->getExact($this->getPage()->getModule());
		}
		return $report->getExact( $this->getReport() );
	}
    
    function hasCrossProjectFilter()
    {
        $values = $this->getFilterValues();

        if ( $_REQUEST['view'] == 'board' ) {
            return $values['target'] != getSession()->getProjectIt();
        }
		if ( getFactory()->getObject('SharedObjectSet')->sharedInProject($this->getObject(), getSession()->getProjectIt()) ) {
	    	return getSession()->getProjectIt()->get('LinkedProject') != '';
		}
		else {
			return false;
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

    function getFilterMoreActions()
    {
        $actions = array();

        $actions[] = array(
            'name' => text(2481),
            'url' => $this->getWidgetShareUrl(),
        );
        $actions[] = array(
            'name' => text(2088),
            'url' => 'javascript: filterLocation.resetFilter();'
        );

        $parentActions = array();
        $selfUrl = $widgetIt = $this->getWidgetIt()->getUrl();
        $selfIt = getSession()->getProjectIt();

        $linkedIds = TextUtils::parseIds($selfIt->get('LinkedProject'));
        if ( count($linkedIds) > 0 && !in_array($selfIt->get('CodeName'), array('my','all')) ) {
            $project_it = getFactory()->getObject('Project')->getRegistry()->Query(
                array(
                    new FilterInPredicate($linkedIds),
                    new ProjectAccessiblePredicate(getSession()->getUserIt())
                )
            );
            while ( !$project_it->end() ) {
                $parentActions[] = array(
                    'name' => $project_it->getDisplayName(),
                    'url' => str_replace(
                        '/pm/'.$selfIt->get('CodeName'),
                        '/pm/'.$project_it->get('CodeName'),
                        $selfUrl)
                );
                $project_it->moveNext();
            }
        }

        $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
        while ( !$portfolio_it->end() ) {
            $project_ids = preg_split('/,/',$portfolio_it->get('LinkedProject'));
            if ( in_array($selfIt->getId(), $project_ids) || in_array($portfolio_it->get('CodeName'), array('my','all')) ) {
                $parentActions[] = array(
                    'name' => $portfolio_it->getDisplayName(),
                    'url' => str_replace(
                        '/pm/'.$selfIt->get('CodeName'),
                        '/pm/'.$portfolio_it->get('CodeName'),
                        $selfUrl)
                );
            }
            $portfolio_it->moveNext();
        }

        if ( count($parentActions) > 0 ) {
            $actions[] = array();
            $actions[] = array(
                'name' => text(2513),
                'items' => $parentActions
            );
        }

        return $actions;
    }

	function getExportActions()
	{
		$actions = array();

		$method = new ExcelExportWebMethod();
		$actions[] = array(
			'uid' => 'export-excel',
			'name' => $method->getCaption(),
			'url' => $method->url( $this->getCaption() )
		);

		$method = new ExcelExportWebMethod();
		$actions[] = array(
			'uid' => 'export-excel-all',
			'name' => text(2202),
			'url' => $method->url( $this->getCaption(), 'IteratorExportExcel', array('show' => 'all') )
		);
		$method = new HtmlExportWebMethod();
		$actions[] = array(
			'uid' => 'export-html',
			'name' => $method->getCaption(),
			'url' => $method->url()
		);
        $method = new XmlExportWebMethod();
        $actions[] = array(
            'uid' => 'export-xml',
            'name' => $method->getCaption(),
            'url' => $method->url()
        );

		return $actions;
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

    function getShareUrlParms() {
        return $this->getFilterValues();
    }

	function getFiltersUrl()
	{
	    $queryParms = $this->getFilterValues();
	    $idKey = strtolower(get_class($this->getObject()));
	    if ( $_REQUEST[$idKey] != '' ) {
            $queryParms[$idKey] = $_REQUEST[$idKey];
        }

		$action_url = $this->getWidgetIt()->getUrl(http_build_query(array_merge($this->getShareUrlParms(), $queryParms)));
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

	function getSaveActions($actions)
    {
        $roles = getSession()->getRoles();
        if ( $roles['lead'] ) {
            $items = array_filter($actions, function($value) {
                return $value['uid'] == 'personal-persist' && !$value['persisted'];
            });
            if ( count($items) > 0 ) {
                $actions[] = array();
                $actions['common-persist'] = array(
                    'name' => text(977),
                    'uid' => 'common-persist',
                    'click' => $this->getPersistentFilter()->urlCommon("function() { $('.alert-filter').hide();} ")
                );
            }
        }

        $values = $this->getFilterValues();
        $url = http_build_query($values, '', '&', PHP_QUERY_RFC3986);

		$custom = getFactory()->getObject('pm_CustomReport');
        if ( !getFactory()->getAccessPolicy()->can_create($custom) ) return $actions;

        $action_url = $custom->getSaveUrl('', $this->getWidgetIt());
        if ( $action_url == '' ) return $actions;

        $method = new ObjectCreateNewWebMethod($custom);
        $method->setRedirectUrl('function(id, object) { window.location = object.Url; }');
        $actions[] = array();
		$actions[] = array (
			'name' => text(1829),
			'title' => text(1830),
			'url' => $method->getJSCall(array(
			            'Url' => $url,
                        'Category' => 'favs',
                        'ReportBase' => $this->getPage()->getReportBase(),
                        'Module' => $this->getPage()->getModule()
                     )),
			'uid' => 'save-report'
        );
        
        return $actions;
    }
        
    function buildSaveAsAction( & $base_actions )
    {
    	$save_action_key = '';
        foreach( $base_actions as $key => $menuitem ) {
            if ( $menuitem['uid'] == 'view-settings' ) {
            	$save_action_key = $key; break;
            }
        }
        if ( $save_action_key == '' ) return;

        $base_actions[$save_action_key]['items'] = $this->getSaveActions(
            $base_actions[$save_action_key]['items']
        );

		if ( is_numeric($this->getReport()) )
		{
			$custom_it = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
				array (
					new FilterInPredicate($this->getReport())
				)
			);
			if ( $custom_it->getId() > 0 ) {
				$store = new ReportModifyWebMethod( $custom_it );
				if ( $store->hasAccess() ) {
					$store->setRedirectUrl("function() { $('.alert-filter').hide(); }");
					$base_actions[$save_action_key]['items']['common-persist'] =
						array (
							'uid' => 'common-persist',
							'name' => text(977),
							'url' => $store->getJSCall($this->getFilterValues())
						);

					$storeReport = new ObjectModifyWebMethod($custom_it);
					if ( $storeReport->hasAccess() ) {
                        $base_actions[$save_action_key]['items'][] = array();
                        $base_actions[$save_action_key]['items'][] =
                            array (
                                'name' => translate('Редактировать'),
                                'url' => $storeReport->getJSCall()
                            );
                    }

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

		$widget_it = $this->getWidgetIt();
		if ( count($service->getItemOnFavoritesWorkspace(array($widget_it->getId()))) > 0 ) return;

		$info = $widget_it->buildMenuItem();

		$base_actions[$save_action_key]['items'][] = array();
		$base_actions[$save_action_key]['items'][] = array(
			'uid' => 'add-favorites',
			'name' => text(1327),
			'url' => "javascript:addToFavorites('".$widget_it->getId()."','".urlencode($info['url'])."', '".($this->getReport() != '' ? 'report' : 'module')."');"
		);

        $base_actions[$save_action_key]['items'][] = array();
        $base_actions[$save_action_key]['items'][] = array(
            'name' => text(2603),
            'url' => $this->getWidgetShareUrl()
        );
    }

    function getWidgetShareUrl() {
        $url = getSession()->getApplicationUrl().'widget/share?URL='.urlencode(urlencode($this->getFiltersUrl()));
        return "javascript: workflowModify({'form_url':'".$url."','class_name':'cms_Language','entity_ref':'cms_Language','object_id':'1','form_title':'".text(2481)."','can_delete':'false','can_modify':'true','delete_reason':null,'modifyButtonText':'".translate('Отправить')."'}, donothing);";
    }

    protected function getFamilyModules( $module )
    {
        return array();
    }

    protected function getChartModules( $module )
    {
        return array();
    }

    protected function buildFamilyFilterItems()
    {
        $reports = array();
        $report = getFactory()->getObject('PMReport');
        $familyModules = $this->getFamilyModules($this->getPage()->getModule());

        $selfIds = array($this->getReport(), $this->getReportBase());
        $self_it = $report->getExact($selfIds);
        $modules = array_merge(
            array(
                $this->getPage()->getModule()
            ),
            $familyModules
        );

        $report_it = $report->getByRef('Module', $modules);
        while( !$report_it->end() ) {
            if ( $report_it->getUrl() == '' ) {
                $report_it->moveNext();
                continue;
            }
            if ( in_array($report_it->getId(),$selfIds) ) {
                $report_it->moveNext();
                continue;
            }
            if ( $report_it->get('Type') == 'chart' ) {
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

        $report_it = $report->getExact($familyModules);
        while( !$report_it->end() ) {
            if ( $report_it->getUrl() == '' ) {
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
            if ( $self_it->count() > 0 && in_array($title, $self_it->fieldToArray('Caption')) ) {
                $module_it->moveNext();
                continue;
            }
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
        return $reports;
    }

    protected function buildChartFilterItems()
    {
        $reports = array();
        $familyModules = $this->getChartModules($this->getPage()->getModule());
        if ( count($familyModules) < 1 ) return $reports;

        $report = getFactory()->getObject('PMReport');
        $report_it = $report->getByRef('Module', $familyModules);

        while( !$report_it->end() ) {
            if ( $report_it->get('Type') == 'chart' ) {
                $reports[$report_it->getId()] = array (
                    'name' => $report_it->getDisplayName(),
                    'url' => $report_it->getUrl(),
                    'uid' => $report_it->getId()
                );
            }
            $index = array_search($report_it->get('Module'), $familyModules);
            if ( $index !== false ) unset($familyModules[$index]);

            $report_it->moveNext();
        }

        $module = getFactory()->getObject('Module');
        foreach( $familyModules as $moduleId ) {
            $report_it = $report->getExact($moduleId);
            if ( $report_it->getId() != '' ) {
                $reports[$report_it->getId()] = array (
                    'name' => $report_it->getDisplayName(),
                    'url' => $report_it->getUrl(),
                    'uid' => $report_it->getId()
                );
                continue;
            }
            $module_it = $module->getExact($moduleId);
            if ( $module_it->getId() != '' ) {
                $reports[$module_it->getId()] = array (
                    'name' => $module_it->getDisplayName(),
                    'url' => $module_it->getUrl(),
                    'uid' => $module_it->getId()
                );
                continue;
            }
        }

        usort($reports, function( $left, $right ) {
            if ( $left['name'] == $right['name'] ) return 0;
            if ( $left['name'] < $right['name'] ) return -1;
            return 1;
        });
        return $reports;
    }

	protected function buildQuickReports(& $base_actions)
	{
	    $items = array();

        $reports = $this->buildFamilyFilterItems();
		if ( count($reports) > 0 ) {
            $items[] = array (
				'name' => text(2136),
				'items' => $reports
			);
		}

        $reports = $this->buildChartFilterItems();
        if ( count($reports) > 0 ) {
            $items[] = array (
                'name' => translate('Графики'),
                'items' => $reports
            );
        }

        if ( count($items) > 0 ) {
            $base_actions = array_merge($items, array(array()), $base_actions);
        }
	}

	function buildFiltersName()
	{
        $report = $this->getReport();
        if ( is_numeric($report) ) {
            $uid = getFactory()->getObject('pm_CustomReport')->getExact($report)->get('UID');
            if ( $uid != '' ) {
                return $uid;
            }
        }
		return md5($report.parent::buildFiltersName());
	}

	function getFilterPredicates()
	{
		$predicates = array();

        $values = $this->getFilterValues();
        
        $predicates = array_merge($predicates, $this->buildCustomPredicates($values));

        $predicates[] = new ProjectVpdPredicate($values['target']);
        $predicates[] = new WatcherUserPredicate($values['watcher']);  
		
		if ( $values['target'] == getSession()->getProjectIt() ) {
            $predicates[] = new FilterBaseVpdPredicate();
        }

        $predicates[] = new StateNotInPredicate($values['hiddencolumns']);
        return array_merge($predicates, parent::getFilterPredicates());
	}

    function buildStatePredicate( $value )
    {
        if ( !$this->hasCommonStates() ) {
            return new StateCommonPredicate( $value );
        }
        else {
            return new StatePredicate( $value );
        }
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
            	$predicates[$attr_it->get('ReferenceName')] = new CustomAttributeValuePredicate($value, $values[$value]);
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
			$query_string = $this->getWidgetIt()->get('QueryString');
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

    public function parseFilterValues( &$values )
    {
        if ( $values['release'] != '' ) {
            $values['release'] = preg_replace_callback('/notpassed/i', function() {
                return join(',',getFactory()->getObject('ReleaseActual')->getAll()->idsToArray());
            }, $values['release']);
        }
        if ( $values['iteration'] != '' ) {
            $values['iteration'] = preg_replace_callback('/notpassed/i', function() {
                return join(',',getFactory()->getObject('IterationActual')->getAll()->idsToArray());
            }, $values['iteration']);
        }
    }

    protected function getPersistentFilter()
    {
        $filter = parent::getPersistentFilter();
        if ( is_numeric($this->getReport()) ) {
            // skip store common settings for custom reports, modify report's query string instead
            $filter->extendToCommon(false);
        }
        return $filter;
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

		$project_it = getSession()->getProjectIt();
		if ( $project_it->IsPortfolio() || $project_it->IsProgram() )
		{
			$project->addFilter( new ProjectLinkedSelfPredicate() );
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
                $filter = new ViewCustomDictionaryWebMethod(
                    $this->getObject(),
                    $attr_it->get('ReferenceName')
                );
                if ( !getSession()->getProjectIt()->IsPortfolio() ) {
                    $filter->setDefaultValue('all');
                }
                $filters[$attr_it->get('ReferenceName')] = $filter;
            }

            if ( $attr_it->getRef('AttributeType')->get('ReferenceName') == 'reference' )
            {
            	$filter = new FilterObjectMethod( 
            			getFactory()->getObject($attr_it->get('AttributeTypeClassName')),
            			$attr_it->getDisplayName(),
            			$attr_it->get('ReferenceName')
        		);
            	
            	$filters[$attr_it->get('ReferenceName')] = $filter;
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
		if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            return new FilterObjectMethod( getFactory()->getObject('UserGroup'), text('user.group.name'), 'usergroup' );
        }
	}

	protected function buildUserRoleFilter( $title = '' )
	{
		if ( $title == '' ) $title = text(2182);
		if ( defined('PERMISSIONS_ENABLED') && !getSession()->getProjectIt()->IsPortfolio() ) {
			$object = getFactory()->getObject('ProjectRoleInherited');
		}
		else {
			$object = getFactory()->getObject('ProjectRoleBase');
		}
		$filter = new FilterObjectMethod( $object, $title, 'userrole' );
		$filter->setHasNone(false);
		return $filter;
	}

    protected function buildFilterState()
    {
        $resolvedAmount = $this->getObject()->getRegistry()->Count(
            array (
                new FilterVpdPredicate(),
                new StatePredicate('terminal')
            )
        );
        if ( !$this->hasCommonStates() ) {
            $filter = new FilterObjectMethod( getFactory()->getObject('StateCommon'), translate('Состояние'), 'state' );
            $filter->setDefaultValue(StateCommonRegistry::Submitted . ',' . StateCommonRegistry::Progress);
            $filter->setHasNone(false);
            return $filter;
        }
        else {
            $state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
            return new StateExFilterWebMethod($state_it, 'state', $resolvedAmount < 30 ? "all" : "");
        }
    }

	function getSortAttributeClause( $field )
	{
		$parts = preg_split('/\./', $field);
		if ( $parts[0] == 'Project' ) {
			return new SortProjectImportanceClause($field);
		}
		if ( $this->getObject()->getAttributeOrigin($parts[0]) == ORIGIN_CUSTOM ) {
            return new CustomAttributeSortClause($field);
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
        $report = getFactory()->getObject('PMReport');
        $report_it = $report->getAll();
        $module = getFactory()->getObject('Module');
        $module_it = $module->getAll();

        $it = getFactory()->getObject('ObjectsListWidget')->getAll();
        while( !$it->end() )
        {
            switch( $it->get('ReferenceName') ) {
                case 'PMReport':
                    $widget_it = $report_it->moveToId($it->getId());
                    break;
                case 'Module':
                    $widget_it = $module_it->moveToId($it->getId());
                    break;
                default:
                    $it->moveNext();
                    continue;
            }
            $this->reference_widgets[$it->get('Caption')] = $widget_it->copy();
            $it->moveNext();
        }

		return array_merge(
            parent::getFullPageRenderParms( $parms ),
            array(
                'details' => $this->getDetails(),
                'details_parms' => $this->getDetailsParms(),
                'filterMoreActions' => $this->getFilterMoreActions(),
                'list_slider' => count($this->getObject()->getAttributesByGroup('trace')) > 0,
                'sliderClass' => $_COOKIE['list-slider-pos'] > 0 ? 'list-slider-'.$_COOKIE['list-slider-pos'] : 'list-slider-2'
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

    function getDetails()
    {
        return array(
            'props' => array (
                'image' => 'icon-zoom-in',
                'title' => text(2167),
                'url' => getSession()->getApplicationUrl().'tooltip/'.get_class($this->getObject()).'/%id%?extended'
            ),
            'discussions' => array (
                'image' => 'icon-comment',
                'title' => text(980),
                'url' => getSession()->getApplicationUrl().'details/log?action=commented&tableonly=true'
            ),
            'more' => array (
                'image' => 'icon-time',
                'title' => text(2166),
                'url' => getSession()->getApplicationUrl().'details/log?tableonly=true'
            )
        );
    }

    function getDetailsParms() {
        return array (
            'active' => 'props'
        );
    }

    function getReferencesListWidget( $parm, $referenceName )
    {
        if ( $parm instanceof VersionIterator ) {
            $parm = $parm->getObjectIt();
        }
        $object = $parm instanceof OrderedIterator ? $parm->object : $parm;
        foreach( $this->reference_widgets as $key => $widget ) {
            if ( is_a($object, $widget) ) return $widget;
        }
        $widget_it = $this->reference_widgets[get_class($object)];
        if ( is_object($widget_it) ) return $widget_it;

        return parent::getReferencesListWidget( $parm, $referenceName );
    }

    function touch() {
        $report = $this->getReportBase();
        $report != "" ? \FeatureTouch::Instance()->touch($report) : parent::touch();
    }

    function hasCommonStates()
    {
        $value_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
        while( !$value_it->end() ) {
            $values[$value_it->get('VPD')][] = $value_it->get('ReferenceName');
            $value_it->moveNext();
        }

        $example = array_shift($values);
        foreach( $values as $attributes ) {
            if ( count(array_diff($example, $attributes)) > 0 || count(array_diff($attributes, $example)) > 0 ) return false;
        }

        return true;
    }
}