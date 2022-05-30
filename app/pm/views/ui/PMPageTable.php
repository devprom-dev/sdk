<?php
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

include_once SERVER_ROOT_PATH . "pm/methods/ViewCustomDictionaryWebMethod.php";
include_once SERVER_ROOT_PATH . 'pm/methods/StateExFilterWebMethod.php';
include_once SERVER_ROOT_PATH . "pm/methods/ViewModifiedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH . "pm/methods/ViewModifiedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH . "pm/methods/ViewFinishDateWebMethod.php";
include_once SERVER_ROOT_PATH . "pm/methods/ViewStartDateWebMethod.php";
include SERVER_ROOT_PATH . "pm/methods/FilterTraceWebMethod.php";
include_once SERVER_ROOT_PATH . "core/classes/versioning/VersionedObject.php";
include_once SERVER_ROOT_PATH . "pm/classes/watchers/predicates/WatcherUserPredicate.php";
include_once SERVER_ROOT_PATH . "pm/classes/model/predicates/AttributeTracePredicate.php";
include_once SERVER_ROOT_PATH . "pm/classes/participants/predicates/UserParticipanceRolePredicate.php";
include "PMPageList.php";
include "PMStaticPageList.php";
include "PMPageChart.php";
include "PMPageBoard.php";

class PMPageTable extends PageTable
{
	private $traces = array();

  	function getSection() {
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
        if ( $_REQUEST['view'] == 'board' && count(\TextUtils::parseFilterItems($values['target'])) > 0 ) {
            return $values['target'] != getSession()->getProjectIt()->getId();
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
    	if ( count($v_actions) > 0 ) {
    		$actions = array_merge($actions, $v_actions);
            $actions[] = array();
    	}

    	if ( !getSession()->getProjectIt()->IsPortfolio() ) {
            $importActions = $this->getImportActions();
            if ( count($importActions) > 0 ) {
                $actions[] = array();
                $actions = array_merge($actions, $importActions);
            }
        }

    	return $actions;
    }

    function getImportActions()
    {
        $actions = array();

        if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) ) {
            $actions['import-excel'] = array(
                'name' => text(2280),
                'url' => '?view=import&object=' . strtolower(get_class($this->getObject())),
                'uid' => 'import-excel'
            );
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

        $save_actions = parent::getFilterMoreActions();

        $this->buildSaveAsAction($save_actions);
        if ( count($save_actions) > 0 ) {
            $actions = array_merge($actions, array(array()), $save_actions);
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
        $method = new PrintPDFExportWebMethod();
        $actions[] = array(
            'uid' => 'export-pdf-p',
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

	    foreach( array($idKey, 'ids', 'offset1') as $parmKey ) {
	        if ( $_REQUEST[$parmKey] != '' ) {
                $queryParms[$parmKey] = $_REQUEST[$parmKey];
            }
        }

		$action_url = $this->getWidgetIt()->getUrl(http_build_query(array_merge($this->getShareUrlParms(), $queryParms)));
		if ( $action_url == '' ) return $action_url;

		return EnvironmentSettings::getServerUrl().$action_url;
	}

	function getFilterUsers( $selectedValue, $allValues )
	{
		$parms = array();
		$additionalValues = array();

		if ( !in_array($allValues['usergroup'],PageTable::FILTER_OPTIONS) ) {
			$parms[] = new EEUserGroupPredicate($allValues['usergroup']);
			if ( strpos($allValues['usergroup'],'none') !== false ) {
				$additionalValues[] = 'none';
			}
		}

		if ( !in_array($allValues['userrole'],PageTable::FILTER_OPTIONS) ) {
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

		$userIds = getFactory()->getObject('User')->getRegistry()->Query($parms)->idsToArray();
        return join(',',array_merge($additionalValues, $userIds, \TextUtils::parseItems($selectedValue)));
	}

	function getSaveActions($actions)
    {
        $roles = getSession()->getRoles();
        if ( $roles['lead'] ) {
            $items = array_filter($actions, function($value) {
                return $value['uid'] == 'personal-persist' && !$value['persisted'];
            });
            if ( count($items) > 0 ) {
                $actions['common-persist'] = array(
                    'name' => text(977),
                    'uid' => 'common-persist',
                    'click' => $this->getPersistentFilter()->urlCommon("function() {hidePersistButton();} ")
                );
            }
        }

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
			            'Url' => http_build_query($this->getSaveAsFilterValues(), '', '&', PHP_QUERY_RFC3986),
                        'Category' => 'favs',
                        'ReportBase' => $this->getPage()->getReportBase(),
                        'Module' => $this->getPage()->getModule()
                     )),
			'uid' => 'save-report'
        );
        
        return $actions;
    }
        
    function buildSaveAsAction( & $actions )
    {
        $actions = $this->getSaveActions($actions);

		if ( is_numeric($this->getReport()) )
		{
			$custom_it = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
				array (
					new FilterInPredicate($this->getReport())
				)
			);
			if ( $custom_it->getId() > 0 ) {
				$store = new ReportModifyWebMethod( $custom_it );
				if ( $store->hasAccess() )
				{
				    unset($actions['personal-persist']);
                    $actions[] = array();

					$store->setRedirectUrl("function() {hidePersistButton();}");
                    $actions['common-persist'] =
						array (
							'uid' => 'common-persist',
							'name' => text(2684),
							'url' => $store->getJSCall($this->getFilterValues())
						);

					$storeReport = new ObjectModifyWebMethod($custom_it);
					if ( $storeReport->hasAccess() ) {
                        $actions[] =
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
                        $actions[] = array (
							'name' => translate('Удалить'),
							'url' => $method->getJsCall()
						);
					}
				}
			}
		}
    }

    function getSaveAsFilterValues()
    {
        return $this->getFilterValues();
    }

    function getWidgetShareUrl() {
        $url = getSession()->getApplicationUrl().'widget/share?URL='.urlencode(urlencode($this->getFiltersUrl()));
        return "javascript: workflowModify({'form_url':'".$url."','class_name':'cms_Language','entity_ref':'cms_Language','object_id':'1','can_delete':'false','can_modify':'true','delete_reason':null,'modifyButtonText':'".translate('Отправить')."'}, donothing);";
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
        $urlParmsString = http_build_query($this->getQuickReportsParms());
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
                'url' => $report_it->getUrl($urlParmsString),
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
                'url' => $report_it->getUrl($urlParmsString),
                'uid' => $report_it->getId()
            );
            $report_it->moveNext();
        }

        if ( $this->getReport() == '' ) {
            array_shift($modules); // skip currently displayed module
        }

        $module_it = getFactory()->getObject('Module')->getExact($modules);
        while( !$module_it->end() ) {
            if ( !getFactory()->getAccessPolicy()->can_read($module_it) ) {
                $module_it->moveNext();
                continue;
            }
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
                    'url' => $module_it->getUrl($urlParmsString),
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
        $urlParmsString = http_build_query($this->getQuickReportsParms());
        $familyModules = $this->getChartModules($this->getPage()->getModule());
        if ( count($familyModules) < 1 ) return $reports;

        $report = getFactory()->getObject('PMReport');
        $report_it = $report->getByRef('Module', $familyModules);

        while( !$report_it->end() ) {
            if ( $report_it->get('Type') == 'chart' ) {
                $reports[$report_it->getId()] = array (
                    'name' => $report_it->getDisplayName(),
                    'url' => $report_it->getUrl($urlParmsString),
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
                    'url' => $report_it->getUrl($urlParmsString),
                    'uid' => $report_it->getId()
                );
                continue;
            }
            $module_it = $module->getExact($moduleId);
            if ( $module_it->getId() != '' ) {
                $reports[$module_it->getId()] = array (
                    'name' => $module_it->getDisplayName(),
                    'url' => $module_it->getUrl($urlParmsString),
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

        $reportIt = $report->getExact('charts');
        if ( count($reports) > 0 ) {
            $reports[] = array();
        }
        $reports[] = array(
            'name' => $reportIt->getDisplayName(),
            'url' => $reportIt->getUrl()
        );

        $moreReports = array();
        $chartsModule = $this->getChartsModuleName();
        if ( $chartsModule != '' ) {
            $moreReports[] = array(
                'name' => text(2628),
                'url' => $module->getExact($chartsModule)->getUrl()
            );
        }

        if ( count($moreReports) > 0 ) {
            $reports[] = array();
            $reports = array_merge($reports, $moreReports);
        }

        return $reports;
    }

    protected function getChartsModuleName()
    {
    }

    protected function getQuickReportsParms()
    {
        return array();
    }

	protected function buildQuickReports(& $base_actions)
	{
	    $items = array();

        $reports = $this->buildFamilyFilterItems();
		if ( count($reports) > 0 ) {
            $items['modules'] = array (
				'name' => text(2136),
				'items' => $reports,
                'uid' => 'modules'
			);
		}

        $reports = $this->buildChartFilterItems();
        if ( count($reports) > 0 ) {
            $items['charts'] = array (
                'name' => translate('Графики'),
                'items' => $reports,
                'uid' => 'charts'
            );
        }

        $parentActions = $this->buildOpenInOtherMenu();
        if ( count($parentActions) > 0 ) {
            $items['projects'] = array(
                'name' => text(2513),
                'items' => $parentActions,
                'uid' => 'projects'
            );
        }

        $widgetActions = array();
        $service = new WorkspaceService();

        $widget_it = $this->getWidgetIt();
        if ( count($service->getItemOnFavoritesWorkspace(array($widget_it->getId()))) < 1 ) {
            $info = $widget_it->buildMenuItem();
            $widgetActions[] =
                array(
                    'uid' => 'add-favorites',
                    'name' => text(1327),
                    'url' => "javascript:addToFavorites('".$widget_it->getId()."','".urlencode($info['url'])."', '".($this->getReport() != '' ? 'report' : 'module')."');"
                );
        }

        $widgetIt = $this->getWidgetIt();
        if ( !$this instanceof DashboardTable && $widgetIt->getId() != '' ) {
            $list = $this->getListRef();
            if ( !$list instanceof PageBoard && $list->getTemplate() != "core/PageTreeGrid.php" ) {
                $method = new ObjectCreateNewWebMethod(getFactory()->getObject('DashboardItem'));
                $method->doSelectProject(false);
                $widgetActions[] = array(
                    'name' => text(2926),
                    'url' => $method->getJSCall(
                        array(
                            'Caption' => $widgetIt->getDisplayName(),
                            'WidgetUID' => $widgetIt->getId(),
                            'Width' => $list instanceof PageChart && $list->getChartWidget() instanceof FlotChartPieWidget ? '300' : ''
                        )
                    ),
                    'uid' => 'add-dashboard'
                );
            }
        }

        $widgetActions[] = array(
            'name' => text(2481),
            'url' => $this->getWidgetShareUrl(),
            'uid' => 'share'
        );
        $items['actions'] = $widgetActions;

        if ( count($items) > 0 ) {
            $base_actions = array_merge($items, array(array()), $base_actions);
        }
	}

	function buildOpenInOtherMenu()
    {
        $parentActions = array();
        $selfUrl = $widgetIt = $this->getWidgetIt()->getUrl(\SanitizeUrl::getSelfUrl());
        $selfIt = getSession()->getProjectIt();

        $linked_it = getFactory()->getObject('ProjectLinkedActive')->getRegistry()->Query(
            array(
                new ProjectStatePredicate('active')
            )
        );
        while ( !$linked_it->end() ) {
            $parentActions[$linked_it->get('CodeName')] = array(
                'name' => $linked_it->getDisplayName(),
                'url' => str_replace(
                    '/pm/'.$selfIt->get('CodeName'),
                    '/pm/'.$linked_it->get('CodeName'),
                    $selfUrl)
            );
            $linked_it->moveNext();
        }
        if ( count($parentActions) > 0 ) {
            $parentActions[] = array();
        }

        $project_it = getFactory()->getObject('Project')->getRegistry()->Query(
            array(
                new ProjectAccessibleActiveVpdPredicate(),
            )
        );
        while ( !$project_it->end() ) {
            $parentActions[$project_it->get('CodeName')] = array(
                'name' => $project_it->getDisplayName(),
                'url' => str_replace(
                    '/pm/'.$selfIt->get('CodeName'),
                    '/pm/'.$project_it->get('CodeName'),
                    $selfUrl)
            );
            $project_it->moveNext();
        }

        $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
        while ( !$portfolio_it->end() ) {
            $project_ids = \TextUtils::parseIds($portfolio_it->get('LinkedProject'));
            if ( in_array($selfIt->getId(), $project_ids) || in_array($portfolio_it->get('CodeName'), array('my','all')) ) {
                array_unshift($parentActions,
                    array(
                        'name' => $portfolio_it->getDisplayName(),
                        'url' => str_replace(
                            '/pm/'.$selfIt->get('CodeName'),
                            '/pm/'.$portfolio_it->get('CodeName'),
                            $selfUrl)
                    )
                );
            }
            $portfolio_it->moveNext();
        }
        return $parentActions;
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

	function getFilterPredicates( $values )
	{
        $predicates = $this->buildCustomPredicates($values);

        if ( $values['target'] == 'accessible' ) {
            $predicates[] = new ProjectAccessibleActiveVpdPredicate();
        }
        else {
            $projects = \TextUtils::parseFilterItems($values['target']);
            if ( count($projects) > 0 ) {
                $predicates[] = new ProjectVpdPredicate($projects);
            }
            else {
                $predicates[] = new FilterVpdPredicate();
            }
        }

        $predicates[] = new WatcherUserPredicate($values['watcher']);
        $predicates[] = new TransitionWasPredicate($values['was-transition']);
        $predicates[] = new TransitionObjectPredicate($this->getObject(), $values['transition']);
        $predicates[] = new AttributeTracePredicate($values['coverage']);
        $predicates[] = new CommentStateFilter($values['commentstate']);
        $predicates[] = new StateNotInPredicate($values['hiddencolumns']);

		return array_merge(
            $predicates,
            parent::getFilterPredicates( $values ),
            $this->buildDateFilterPredicates($values)
        );
	}

    function buildDateFilterPredicates( $values )
    {
        return array(
            new FilterSubmittedAfterPredicate($values['submittedon']),
            new FilterSubmittedBeforePredicate($values['submittedbefore']),
            new FilterModifiedAfterPredicate($values['modifiedafter']),
            new FilterModifiedBeforePredicate($values['modifiedbefore'])
        );
    }

    function buildStatePredicate( $value )
    {
        if ( !$this->hasCommonStates() && $this->getListRef() instanceof PageBoard ) {
            return new StateCommonPredicate( $value );
        }
        else {
            return new StatePredicate( $value );
        }
    }

    function buildCustomPredicates( $values )
	{
		$predicates = array();

        $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
        while( !$attr_it->end() )
        {
        	$type = $attr_it->getRef('AttributeType')->get('ReferenceName');
        	$value = $attr_it->get('ReferenceName');
        	
            if ( in_array($type, array('dictionary','reference','char')) ) {
            	$predicates[$attr_it->get('ReferenceName')] =
                    new CustomAttributeValuePredicate($value, $values[$value]);
            }

            if ( in_array($type, array('date')) ) {
                $predicates[$attr_it->get('ReferenceName')] =
                    new CustomAttributeDateIntervalPredicate($value, $values[$value.'-from'], $values[$value.'-till']);
            }

            $attr_it->moveNext();
        }
        
        return $predicates;
	}
    
	public function buildFilterValuesByDefault( & $filters )
	{
        $values = parent::buildFilterValuesByDefault($filters);

        $filterNames = array();
        foreach( $this->filters as $filter ) {
            if ( ! $filter instanceof WebMethod ) continue;
            $filterNames[] = $filter->getValueParm();
        }

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
		    if ( is_numeric($this->getReport()) ) {
		        $reportIt = getFactory()->getObject('pm_CustomReport')->getExact($this->getReport());
		        $reportValues = array();
                parse_str($reportIt->getHtmlDecoded('Url'), $reportValues);

                foreach( array_merge($filterNames, $this->getFilterParms()) as $key ) {
                    if ( array_key_exists($key, $reportValues) ) {
                        $values[$key] = $reportValues[$key];
                    }
                }
            }
		    else {
                $reportIt = getFactory()->getObject('PMReport')->getExact($this->getReport());
                if ( $reportIt->getId() != '' ) {
                    $reportValues = array();
                    parse_str($reportIt->getHtmlDecoded('QueryString'), $reportValues);

                    foreach( array_merge($filterNames, $this->getFilterParms()) as $key ) {
                        if ( array_key_exists($key, $reportValues) ) {
                            $values[$key] = $reportValues[$key];
                        }
                    }
                }

                $values = array_merge(
                    $values,
                    $this->buildFilterValuesBySettings(
                        $values,
                        $this->getPage()->getSettingsBuilder()->getByReport($this->getReportBase())
                    )
                );
            }
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

            $groupSort = $alt_setting->getGroupSort();
            if ( $groupSort != '' ) {
                $values['sortgroup'] = $groupSort;
            }

            $sections = $alt_setting->getSections();
 	    	if ( count($sections) > 0 ) $values['infosections'] = join(',',$sections);
 	    }

		return $values;
	}

    protected function getPersistentFilter()
    {
        if ( is_numeric($this->getReport()) ) {
            $reportIt = getFactory()->getObject('pm_CustomReport')->getExact($this->getReport());
            if ( $reportIt->getId() != '' && getFactory()->getAccessPolicy()->can_modify($reportIt) ) {
                return new ReportModifyWebMethod($reportIt);
            }
        }
        return parent::getPersistentFilter();
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

	    if ( $this->getObject() instanceof MetaobjectStatable && $this->getObject()->getStateClassName() != '' ) {
	        $filters[] = $this->buildFilterTransition();
            $filters[] = $this->buildFilterLastTransition();
        }

	    if ( count($this->getObject()->getAttributesByGroup('trace')) > 0 ) {
            $filters[] = $this->buildTraceFilter();
        }

        if ( $this->getObject()->hasAttribute('RecentComment') ) {
            $filters[] = $this->buildFilterComments();
        }

        return $filters;
    }

    protected function buildFilterComments()
    {
        $filter = new FilterObjectMethod(getFactory()->getObject('CommentState'),
            translate('Комментарии'), 'commentstate');
        $filter->setIdFieldName('ReferenceName');
        $filter->setHasAny(false);
        return $filter;
    }

    protected function buildTraceFilter()
    {
        $method = new FilterTraceWebMethod($this->getObject());
        return $method;
    }

    protected function buildProjectFilter()
    {
   		$project = getFactory()->getObject('pm_Project');
		if ( getSession()->getProjectIt()->get('LinkedProject') != '' )
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
            switch( $attr_it->getRef('AttributeType')->get('ReferenceName') ) {
                case 'dictionary':
                    $filter = new ViewCustomDictionaryWebMethod(
                        $this->getObject(),
                        $attr_it->get('ReferenceName')
                    );
                    if ( !getSession()->getProjectIt()->IsPortfolio() ) {
                        $filter->setDefaultValue('all');
                    }
                    $filters[$attr_it->get('ReferenceName')] = $filter;
                    break;

                case 'reference':
                    $className = getFactory()->getClass($attr_it->get('AttributeTypeClassName'));
                    if ( class_exists($className) ) {
                        $filter = new FilterObjectMethod(
                            getFactory()->getObject($className),
                            $attr_it->getDisplayName(),
                            $attr_it->get('ReferenceName')
                        );
                        $filters[$attr_it->get('ReferenceName')] = $filter;
                    }
                    break;

                case 'char':
                    $filter = new FilterCheckMethod(
                        $attr_it->getDisplayName(),
                        $attr_it->get('ReferenceName')
                    );
                    $filters[$attr_it->get('ReferenceName')] = $filter;
                    break;

                case 'date':
                    $filters[] = new FilterDateIntervalWebMethod($attr_it->getDisplayName(),
                        $attr_it->get('ReferenceName') . '-from');
                    $filters[] = new FilterDateIntervalWebMethod($attr_it->getDisplayName(),
                        $attr_it->get('ReferenceName') . '-till');
                    break;
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
        $filter->setLazyLoad(true);
		return $filter;
	}

	protected function buildFilterTransition()
    {
        $transitionIt = getFactory()->getObject('Transition')->getRegistry()->Query(
            array(
                new FilterVpdPredicate($this->getObject()->getVpds()),
                new TransitionStateClassPredicate($this->object->getStatableClassName())
            )
        );
        $filter = new FilterObjectMethod( $transitionIt, text(3119), 'was-transition' );
        $filter->setHasNone(false);
        return $filter;
    }

    protected function buildFilterLastTransition()
    {
        $transitionIt = getFactory()->getObject('Transition')->getRegistry()->Query(
            array(
                new FilterVpdPredicate($this->getObject()->getVpds()),
                new TransitionStateClassPredicate($this->object->getStatableClassName())
            )
        );
        $filter = new FilterObjectMethod( $transitionIt, text(1867), 'transition' );
        $filter->setHasNone(false);
        return $filter;
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
		return new FilterObjectMethod( $object, $title, 'userrole' );
	}

    protected function buildFilterState( $filterValues = array() )
    {
        $resolvedAmount = $this->getObject()->getRegistry()->Count(
            array (
                new FilterVpdPredicate(),
                new StatePredicate('terminal')
            )
        );
        if ( !$this->hasCommonStates($filterValues) )
        {
            if ( $this->getListRef() instanceof PageBoard ) {
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
        else {
            $projectIt = getFactory()->getObject('Project')->getExact(
                array_shift(\TextUtils::parseIds($filterValues['target']))
            );
            if ( $projectIt->getId() != '' ) {
                $state_it = getFactory()->getObject($this->getObject()->getStateClassName())->getRegistry()->Query(
                    array(
                        new FilterVpdPredicate($projectIt->get('VPD'))
                    )
                );
            }
            else {
                $state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
            }
            return new StateExFilterWebMethod($state_it, 'state', $resolvedAmount < 30 ? "all" : "");
        }
    }

	function getSortAttributeClause( $field )
	{
		$parts = preg_split('/\./', $field);
		if ( $parts[0] == 'Project' ) {
			return new SortProjectImportanceClause();
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

	function getRenderParms($parms)
    {
        $it = getFactory()->getObject('ObjectsListWidget')->getAll();
        while( !$it->end() )
        {
            $this->reference_widgets[$it->get('Caption')] = $it->getWidgetIt();
            $it->moveNext();
        }

        return parent::getRenderParms($parms); // TODO: Change the autogenerated stub
    }

    function getFullPageRenderParms( $parms )
	{
	    $parms = parent::getFullPageRenderParms( $parms );
	    if ( is_object($this->getListRef()) ) {
            $it = $this->getListRef()->getIteratorRef();
        }
		return array_merge( $parms,
            array(
                'title' => $this->getCaption(),
                'details' => $this->getPage()->getReportChart() ? array() : $this->getDetails(),
                'details_parms' => is_object($it) && $it->count() > 0
                                        ? $this->getDetailsParms()
                                        : array(),
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
			$this->traces[$class] = class_exists($class)
                ? getFactory()->getObject($class)->getExact($ids)
                : $this->getObject()->getEmptyIterator();
		}
	}

    function getDetails()
    {
        return array(
            'form' => array (
                'image' => 'icon-list-alt',
                'title' => text(2167),
                'url' => getSession()->getApplicationUrl().'form/%class%/%id%'
            ),
            'props' => array (
                'image' => 'icon-zoom-in',
                'title' => text(2167),
                'url' => getSession()->getApplicationUrl().'tooltip/%class%/%id%?extended'
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
        return array ();
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

    function getReferencesListProjectIt( $vpds )
    {
  	    if ( count($vpds) < 2 ) return getSession()->getProjectIt();
  	    if ( !is_object($this->referencesProjectIt) ) {
  	        $portfolio = getFactory()->getObject('Portfolio');
            $this->referencesProjectIt = $portfolio->getByRef('CodeName', 'my');
            if ( $this->referencesProjectIt->getId() == '' ) {
                $this->referencesProjectIt = $portfolio->getByRef('CodeName', 'all');
            }
        }
  	    return $this->referencesProjectIt;
    }

    function touch() {
        $report = $this->getReportBase();
        $report != "" ? \FeatureTouch::Instance()->touch($report) : parent::touch();
    }

    function hasCommonStates( $filterValues = array() )
    {
        if ( count(\TextUtils::parseIds($filterValues['target'])) == 1 ) return true;

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

    function renderFilter( &$filter, $filterValues )
    {
        if ( $filter instanceof FilterWebMethod and $filter->getName() == 'state' && $this->getObject() instanceof MetaobjectStatable ) {
            $filter = $this->buildFilterState($filterValues);
        }
    }
}