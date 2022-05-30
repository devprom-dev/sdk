<?php
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Tooltip\TooltipProjectService;
include_once SERVER_ROOT_PATH."pm/views/import/ImportXmlForm.php";

include 'PMFormEmbedded.php';
include 'PMPageForm.php';
include 'PMPageTable.php';
include 'PMPageDetailsTable.php';
include 'PageSectionLifecycle.php';
include "FieldHierarchySelectorAppendable.php";
include 'FieldCustomDictionary.php';
include "FieldReferenceCustomAttribute.php";
include_once 'FieldWYSIWYG.php';
include 'FieldTextEditable.php';
include 'FieldEstimation.php';
include 'NetworkSection.php';
include 'PMPageNavigation.php';
include_once 'PMLastChangesSection.php';
include_once 'BulkForm.php';
include 'converters/WikiIteratorExportHtml.php';
include 'converters/WikiIteratorExportPdf.php';
include_once "SettingsFormBase.php";
include "SettingsTableBase.php";
include_once SERVER_ROOT_PATH.'pm/views/comments/PageSectionComments.php';

class PMPage extends Page
{
    var $tabs, $areas;
    protected $settings;
    protected $report_uid;
    protected $report_base;
    protected $report_chart = false;
    protected $nearestInfo = array();
    
    function __construct()
 	{
 		parent::__construct();

        if ( $this->needDisplayForm() && is_object($this->getFormRef()) ) {
            $groupIt = (new PageFormTabGroup())->getAll();
            while( !$groupIt->end() ) {
                $this->addInfoSection(
                    new PageSectionAttributes(
                            $this->getFormRef()->getObject(),
                            explode(',',$groupIt->get('ReferenceName')),
                            $groupIt->get('Caption')
                        )
                    );
                $groupIt->moveNext();
            }
        }
 	}
 	
 	function getSettingsBuilder()
 	{
 	    if ( !is_object($this->settings) )
 	    {
 	        $this->settings = new PageSettingSet();
 	    }

 	    return $this->settings;
 	}
 	
 	function getNavigationContext( & $areas, $active_url )
 	{
 	    $context = parent::getNavigationContext( $areas, $active_url );

 	    if ( $context['area_uid'] == '' )
 	    {
 	        return array (
 	                'area_uid' => FUNC_AREA_MANAGEMENT,
 	                'item_uid' => FUNC_AREA_MANAGEMENT.'/reports/all',
 	                'item' => $areas[FUNC_AREA_MANAGEMENT]['menus']['reports']['items']['all']
 	        );
 	    }
 	    
 	    return $context;
 	}

 	function identifyReport()
    {
        parent::identifyReport();

        if ( $_REQUEST['report'] != '' )
        {
            $this->setReport(\TextUtils::getAlphaNumericPunctuationString($_REQUEST['report']));

            $report = getFactory()->getObject('PMReport');
            $report_it = $report->getExact($_REQUEST['report']);

            $this->setReportBase($report_it->get('Report') != '' ? $report_it->get('Report') : $this->getReport());
            $this->setModule( $report_it->get('Module') );
            $this->setReportChart($report_it->get('Type') == 'chart');

            if ( is_numeric($_REQUEST['report']) )
            {
                $custom_it = getFactory()->getObject('pm_CustomReport')->getExact($_REQUEST['report']);
                if ( $custom_it->getId() > 0 ) {
                    $_REQUEST['basereport'] = $custom_it->get('ReportBase');
                    if ( $custom_it->get('ReportBase') != '' ) {
                        $this->setReportBase($custom_it->get('ReportBase'));
                        $this->setReportChart($custom_it->get('Type') == 'chart');
                    }
                }

                if ( !getFactory()->getAccessPolicy()->can_read($report_it) ) {
                    $base_it = $_REQUEST['basereport'] != ''
                        ? $report->getExact($_REQUEST['basereport'])
                        : ($_REQUEST['basemodule'] != ''
                            ? getFactory()->getObject('Module')->getExact($_REQUEST['basemodule'])
                            : $report->getEmptyIterator());

                    if ( $base_it->getId() != '' ) {
                        $item = $base_it->buildMenuItem(preg_replace('/report=[^&]*|project=[^&]*|view=[^&]*/', '', $_SERVER['QUERY_STRING']));
                        exit(header('Location: '._getServerUrl().$item['url'].'&'.$custom_it->getHtmlDecoded('Url')));
                    }
                }
            }

            if ( $this->getReport() == '' ) {
                $report_it = $report->getByModule( $this->getModule() );
                $this->setReport($report_it->getId());
                $this->setReportBase($report_it->getId());
                $this->setReportChart($report_it->get('Type') == 'chart');
            }
        }
    }

 	function getRenderParms()
 	{
 	    $parms = parent::getRenderParms();

        foreach( getSession()->getBuilders('PageSettingBuilder') as $builder ) {
            $builder->build( $this->getSettingsBuilder() );
        }


		return array_merge(
		    $parms,
            array(
                'context_template' => $_REQUEST['dashboard'] == '' ? 'pm/PageContext.php' : '',
                'context' => $this->getContext(),
            )
        );
 	}

    protected function buildNavigationParms() {
        return new PMPageNavigation($this);
    }

	function getFullPageRenderParms()
	{
		$parms = parent::getFullPageRenderParms();
                
		$bodyExpanded = $_COOKIE['menu-state'] == 'minimized';

		if ( $bodyExpanded ) {
			$isPortfolio = getSession()->getProjectIt()->IsPortfolio();
			if ( is_array($parms['navigation_parms']['areas']['stg']) ) {
                $parms['navigation_parms']['areas']['stg']['menus']['']['items'] =
					array (
						array (
							'name' => text(2197),
							'url' => getSession()->getApplicationUrl().'settings'
						)
					);
			}
			$parms['navigation_parms']['areas']['more'] = array (
				'name' => translate('Дополнительно'),
				'menus' => array (
					array (
						'name' => '',
						'items' => array (
							($isPortfolio ?
								array (
									'name' => text(1292),
									'url' => getSession()->getApplicationUrl().'profile'
								) : array()),
							array (
								'name' => text(2194),
								'url' => getSession()->getApplicationUrl().'project/reports'
							)
						)
					)
				)
			);
		}

        if ( $this->needDisplayForm() ) {
            $info = $this->getPageWidgetNearestUrl();
            $parms['navigation_url'] = $info['url'];
            $parms['nearest_title'] = $info['name'];
        }

        $parentIt = getSession()->getProjectIt()->getParentIt();
        if ( $parms['navigation_url'] != '' && $parentIt->getId() != '' ) {
            $parms['parent_widget_url'] = preg_replace('/\/pm\/[^\/]+\//i', '/pm/'.$parentIt->get('CodeName').'/', $parms['navigation_url']);
            $parms['parent_widget_title'] = $parentIt->getDisplayName();
        }

		return array_merge( $parms, 
			array (
				'caption_template' => 'pm/PageTitle.php',
				'project_code' => getSession()->getProjectIt()->get('CodeName'),
				'project_template' => getSession()->getProjectIt()->get('Tools'),
				'has_horizontal_menu' => getSession()->getProjectIt()->IsPortfolio() ? false : $parms['has_horizontal_menu'],
				'report' => $this->getReportBase(),
                'uid' => $this->getReportBase(),
				'widget_id' => $this->getReport() != '' ? $this->getReport() : $this->getModule(),
				'bodyExpanded' => $bodyExpanded,
				'search_url' => '/pm/' . getSession()->getProjectIt()->get('CodeName') . '/search.php'
			)
		);
	}

    function getDefaultRedirectUrl()
    {
        $info = $this->getPageWidgetNearestUrl();
        if ( $info['url'] != '' ) {
            return $info['url'];
        }

        $widgetIt = getFactory()->getObject('ObjectsListWidget')
            ->getByRef('Caption', get_class($this->getObject()))->getWidgetIt();
        if ( $widgetIt->getUrl() != '' ) {
            return $widgetIt->getUrl();
        }

        return parent::getDefaultRedirectUrl();
    }

	function getRedirect( $renderParms )
	{
        if ( !DeploymentState::Instance()->IsReadyToBeUsed() ) return '/install';
        if ( DeploymentState::IsMaintained() ) return '/503';

	    $navigation_parms = $renderParms['navigation_parms'];
 		$areas = $navigation_parms['areas'];

 		if ( $_REQUEST['tab'] != '' )
 		{
 		    $parts = preg_split('/\//', $_REQUEST['tab']);
 		    
 		    if ( count($parts) == 3 )
 		    {
     		    foreach( $areas as $area )
     		    {
     		    	if ( !is_array($area['menus']) ) continue;
     		    	
     		        if ( $area['uid'] = $parts[0] )
     		        {
     		            foreach ( $area['menus'] as $menu )
     		            {
     		                if ( $menu['uid'] == $parts[1] )
     		                {
     		                    foreach( $menu['items'] as $item )
     		                    {
     		                        if ( $item['uid'] == $parts[2] && $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) )
     		                        {
     		                            return $item['url'];
     		                        }
     		                    }
     		                }
     		            }
     		        }
     		    }
 		    }
 		}

 		$use_entry_point = trim($_SERVER['REQUEST_URI'],'/') == trim(getSession()->getApplicationUrl(),'/')
 		    || $_REQUEST['tab'] != '';

 		if ( $use_entry_point )
 		{
            // mark last visited project
            $projectId = getSession()->getProjectIt()->getId();
            $userId = getSession()->getUserIt()->getId();
            if ( $projectId > 0 && $userId > 0 ) {
                DAL::Instance()->Query("UPDATE pm_Participant SET RecordModified = NOW() WHERE SystemUser = ".$userId." AND Project = ".$projectId);
            }

            // if no tab is specified then use default entry
            $favArea = $areas['favs'];
            foreach ( $favArea['menus'] as $menu ) {
                foreach( $menu['items'] as $item ) {
                    if ( $item['entry-point'] && $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) ) {
                        if ( $this->checkWidgetExists($item) ) return $item['url'];
                    }
                }
            }
 		}

		if ( array_key_exists('fitmenu', $_REQUEST) ) {
			$info = $this->getPageWidgetNearestUrl();
			if ( $info['url'] != self::getPageUrl() ) {
				return $info['url'];
			}
		}

		return parent::getRedirect($renderParms);
	}

	protected function checkWidgetExists( $item ) {
		if ( $item['report'] != '' ) {
			$report_it = getFactory()->getObject('PMReport')->getExact($item['report']);
			if ( $report_it->getId() == '' ) return false;
		}
		if ( $item['module'] != '' ) {
			$module_it = getFactory()->getObject('Module')->getExact($item['module']);
			if ( $module_it->getId() == '' ) return false;
		}
		list($namespace, $module) = preg_split('/\//', $item['module']);
		if ( $namespace != '' && $module != '' ) {
			$module = PluginsFactory::Instance()->getModule( $namespace, 'pm', $module );
			if ( !is_array($module) ) return false;
		}
		return true;
	}
	
	function getTabsTemplate()
	{
		return 'pm/PageTabs.php'; 	
	}
	
 	function export()
 	{
 		switch ( $_REQUEST['export'] )
 		{
 			case 'commentsthread':
 				return $this->exportCommentsThread();

			case 'traces':
				$object_it = $this->getObject()->getExact(TextUtils::parseIds($_REQUEST['ids']));
				if ( $object_it->getId() == '' ) return;

				$reference = $this->getObject()->getAttributeObject($_REQUEST['attribute']);
				$referenceIds = array_filter(
                        array_unique($object_it->fieldToArray($_REQUEST['attribute'])),
                        function($item) {
                            return $item > 0;
                        }
                    );

				$it = getFactory()->getObject('ObjectsListWidget')->getAll();
				while( !$it->end() )
				{
					if ( is_a($reference, $it->get('Caption')) ) {
						$widget_it = $it->getWidgetIt();
                        if ( count($referenceIds) > 0 ) {
                            $referenceIt = $reference->getRegistryBase()->Query(
                                array(
                                    $reference->hasAttribute('ParentPage')
                                        ? new ParentTransitiveFilter($referenceIds)
                                        : new FilterInPredicate($referenceIds)
                                )
                            );
                        }
                        else {
                            $referenceIt = $reference->getEmptyIterator();
                        }

                        $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($referenceIt, 'ids', $widget_it);
                        if ( $url != '' ) {
                            exit(header('Location: '.$url));
                        }
					}
					$it->moveNext();
				}
				return;

 			default:
 			    if ( $_REQUEST['export'] == 'html' && $this->needDisplayForm() ) {
 			        $object_it = $this->getObjectIt();
                    if ( is_object($object_it) ) {
                        $this->exportForm($object_it);
                    }
                    else {
                        return parent::export();
                    }
                }
                else {
                    return parent::export();
                }
 		}
 	}
 	
 	function getBulkForm() {
 	    return new BulkForm($this->getObject());
 	}
 	
 	function getForm()
 	{
        if ($_REQUEST['view'] == 'import') {
            return new ImportXmlForm($this->getObject());
        }
        else {
            return parent::getForm();
        }
 	}
 	
    function getReport()
    {
        return $this->report_uid;
    }

    function setReport( $uid )
    {
    	$this->report_uid = $uid;
    }
    
    function getReportBase()
    {
        return $this->report_base;
    }
    
    function setReportBase( $uid )
    {
    	$this->report_base = $uid;
    }

    function getReportChart() {
        return $this->report_chart;
    }

    function setReportChart( $value ) {
        $this->report_chart = $value;
    }
 	
    function getPageUid()
    {
    	if ( $this->getReport() != '' ) return $this->getReport();
    	if ( $this->getReportBase() != '' ) return $this->getReportBase();
    	if ( $this->getModule() != '' ) return $this->getModule();
    	
    	return parent::getPageUid();
    }
    
 	function hasAccess()
 	{
 	    if ( !parent::hasAccess() ) return false;

 	    if ( $this->needDisplayForm() ) {
            $object_it = $this->getObjectIt();
            if ( is_object($object_it) ) {
                return getFactory()->getAccessPolicy()->can_read($object_it);
            }
        }

 	    // report based permissions to display the page
        $report_uid = $this->getReport();
		if ( $report_uid != '' ) {
            return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('PMReport')->getExact($report_uid));
        }

        $module_uid = $this->getModule();
        if ( $module_uid != '' ) {
        	return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Module')->getExact($module_uid));
        }

 		return getSession()->getUserIt()->getId() != '';
 	}
    
 	function exportCommentsThread()
 	{
		$className = getFactory()->getClass($_REQUEST['objectclass']);
        if ( !class_exists($className) ) return;

	 	$object_it = getFactory()->getObject($className)->getExact($_REQUEST['object']);
	 	if ( $object_it->getId() == '' || !getFactory()->getAccessPolicy()->can_read($object_it) ) return;

		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset='.APP_ENCODING);

        $comment_list = $this->buildCommentList($object_it);
        $comment_list->setControlUID($_REQUEST['control-uid']);

		if ( $_REQUEST['form'] != '' ) {
			$comment = $this->getCommentObject();
			$comment->setVpdContext( $object_it );
			
		    $comment_it = $_REQUEST['comment'] > 0 
		        ? $comment->getExact( $_REQUEST['comment'] )
		        : $comment->getEmptyIterator();

            $form = $this->buildCommentForm( $comment_it, $comment_list->getControlUID() );

			$parms['prevcomment'] = $_REQUEST['prevcomment'];
			
			$form->render( $this->getRenderView(), $parms );
		}
		else {
            $comment_list->render( $this->getRenderView(), array() );
		}
 	}

    function getCommentObject() {
        return getFactory()->getObject('Comment');
    }

 	function buildCommentForm( $comment_it, $control_uid )
    {
        $form = new CommentForm(
            $comment_it->getId() > 0 ? $comment_it : $comment_it->object
        );

        $form->setControlUID( $control_uid );

        if ( $_REQUEST['objectclass'] != '' ) {
            $anchor = getFactory()->getObject($_REQUEST['objectclass']);
            $form->setAnchorIt($anchor->getExact($_REQUEST['object']));
        }

        if ( $_REQUEST['dorefresh'] == 1 ) {
            $form->setRedirectUrl( "javascript: refreshCommentsThread(\"".$control_uid."\");" );
        }

        return $form;
    }

    function buildCommentList( $object_it ) {
        return new CommentsThread( $object_it, $this->getCommentObject() );
    }

 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');
		$template = array_shift(preg_split('/_/', getSession()->getProjectIt()->get('Tools')));
		
		$resource_it = $resource->getExact($this->getReport());
		if ( $resource_it->getId() != '' ) return $this->parseHint($resource_it->get('Caption'));
		
		$resource_it = $resource->getExact($this->getReportBase());
		if ( $resource_it->getId() != '' ) return $this->parseHint($resource_it->get('Caption'));

		$resource_it = $resource->getExact($this->getReportBase().':'.$template);
		if ( $resource_it->getId() != '' ) return $this->parseHint($resource_it->get('Caption'));
		
		$resource_it = $resource->getExact($this->getModule().':'.$template);
		if ( $resource_it->getId() != '' ) return $this->parseHint($resource_it->get('Caption'));
		
		$parent = parent::getHint();
		if ( $parent != '' ) return $this->parseHint($parent);
		
		$report = $this->getReport();
		if ( $report == '' ) $report = $this->getReportBase(); 		
		
		if ( $report != '' )
		{
		    $text = getFactory()->getObject('PMReport')->getExact($report)->get('Description');
		    if ( $text != '' ) return '<p>'.$this->parseHint($text).'</p>';
		}

		if ( $this->getModule() != '' )
		{
			$text = getFactory()->getObject('Module')->getExact($this->getModule())->get('Description');
			if ( $text != '' ) return '<p>'.$this->parseHint($text).'</p>';
		}
		
		return '';
	}

	function parseHint( $text )
	{
		$text = preg_replace('/\%project\%/i', getSession()->getProjectIt()->get('CodeName'), $text);
        $text = preg_replace('/&lt;auth-key&gt;/i', \AuthenticationAPIKeyFactory::getAuthKey(getSession()->getUserIt()), $text);
        $text = preg_replace('/%project-key%/i', getSession()->getProjectIt()->getPublicKey(), $text);

        $docsUrl = \EnvironmentSettings::getHelpDocsUrl();
        if ( $docsUrl != '' ) {
            $text .= str_replace('%1', $docsUrl, text(2700));
        }

		return $text;
	}

	function getPageWidgets()
	{
		return array();
	}

	function getPageWidgetNearestUrl()
	{
	    if ( count($this->nearestInfo) > 0 ) return $this->nearestInfo;

		$report = getFactory()->getObject('PMReport');
		$module = getFactory()->getObject('Module');
		$service = new WorkspaceService();
		$urls = array();

		foreach( $this->getPageWidgets() as $widget )
		{
			$report_it = $report->getExact($widget);
			if ( $report_it->getId() != '' )
			{
				$uids = array ( $report_it->getId() );
				if ( $report_it->get('Report') != '' ) {
					$uids = array_merge(
							$uids,
							array($report_it->get('Report')),
							$report->getByRef('Report', $report_it->get('Report'))->idsToArray()
					);
				}
				if ( $report_it->get('Module') != '' ) {
					$uids = array_merge(
							$uids,
							array($report_it->get('Module')),
							$report->getByRef('Module', $report_it->get('Module'))->idsToArray()
					);
				}
			}
			else {
				$report_it = $module->getExact($widget);
				if ( $report_it->getId() != '' ) {
					$uids = array_merge(
							array($report_it->getId()),
							$report->getByRef('Module', $report_it->getId())->idsToArray()
					);
				}
			}

			if ( count($uids) > 0 )
			{
				$reports = $service->getItemOnFavoritesWorkspace($uids);
				if ( count($reports) > 0 ) {
					$item = $reports[0]['report'];
					if ( $item['type'] == 'report' ) {
					    $report_it = $report->getExact($item['id']);
                        $this->nearestInfo = array (
                            'name' => $report_it->getDisplayName(),
                            'url' => $report_it->getUrl(),
                            'widget' => $report_it->copy()
                        );
						return $this->nearestInfo;
					}
					if ( $item['type'] == 'module' ) {
					    $module_it = $module->getExact($item['id']);
                        $this->nearestInfo = array (
                            'name' => $module_it->getDisplayName(),
                            'url' => $module_it->getUrl(),
                            'widget' => $module_it->copy()
                        );
						return $this->nearestInfo;
					}
				}
                $urls[] = array (
                    'name' => $report_it->getDisplayName(),
                    'url' => $report_it->getUrl(),
                    'widget' => $report_it->copy()
                );
			}
		}
        return $this->nearestInfo = array_shift($urls);
	}

	function getPageUrl()
	{
		if ( !$this->needDisplayForm() ) return parent::getPageUrl();
        $info = $this->getPageWidgetNearestUrl();
		return $info['url'] != '' ? $info['url'] : parent::getPageUrl();
	}

	function exportForm( $object_it )
    {
        if ( $_REQUEST['class'] == '' || !class_exists($_REQUEST['class']) ) {
            throw new Exception('Required parameter is missed: "class" should be given');
        }

        $object_it = getFactory()->getObject('WikiPage')->createCachedIterator(
            array (
                array(
                    'WikiPageId' => 1,
                    'Caption' => $object_it->getDisplayName(),
                    'Content' => htmlentities($this->buildExportContent($object_it, true)),
                    'ContentEditor' => getSession()->getProjectIt()->get('WikiEditorClass')
                )
            )
        );
        $eit = new $_REQUEST['class']( $object_it );
        $eit->setName($object_it->getDisplayName());
        $eit->export();
    }

    protected function buildExportContent( $object_it, $skipTitle = false )
    {
        $service = new TooltipProjectService( get_class($object_it->object), $object_it->getId(), true );
        $traceAttributes = $object_it->object->getAttributesByGroup('trace');

        $html = $service->getHtmlRep($skipTitle, $traceAttributes);
        if ( !$skipTitle ) return $html;

        foreach( $traceAttributes as $attribute ) {
            if ( $object_it->get($attribute) == '' ) continue;
            if ( !$object_it->object->IsReference($attribute) ) continue;

            $html .= '<h4>'.$object_it->object->getAttributeUserName($attribute).'</h4>';
            $trace_it = $object_it->getRef($attribute);
            while( !$trace_it->end() ) {
                $html .= $this->buildExportContent($trace_it);
                $html .= '<br/><br/>';
                $trace_it->moveNext();
            }
        }

        return $html;
    }

    function getContext()
    {
        $actions = array();
        $title = text(2472);
        $uid = new ObjectUID;

        $registry = getFactory()->getObject('WorkItem')->getRegistry();
        $registry->setDescriptionIncluded(false);
        $registry->getObject()->disableVpd();

        $registry->setLimit(1);
        $openIt = $registry->Query(
            array(
                new FilterAttributePredicate('Assignee', getSession()->getUserIt()->getId()),
                new FilterAttributeNullPredicate('FinishDate'),
                new StateObjectSortClause()
            )
        );

        if ( $openIt->getId() != '' && class_exists($openIt->get('ObjectClass')) ) {
            $info = $uid->getUIDInfo($openIt);
            $title = $info['uid'] . ' ' . $openIt->getDisplayNameExt();
            $titleUrl = getFactory()->getObject($openIt->get('ObjectClass'))->getRegistryBase()->Query(
                                array(
                                    new FilterInPredicate($openIt->getId())
                                )
                            )->getViewUrl();
        }

        $sortDueDate = new SortAttributeClause('DueDate');
        $sortDueDate->setNullOnTop(false);

        $registry->setLimit(10);
        $taskIt = $registry->Query(
            array(
                new FilterAttributePredicate('Assignee', getSession()->getUserIt()->getId()),
                new FilterAttributeNullPredicate('FinishDate'),
                $sortDueDate,
                new SortAttributeClause('Priority'),
                new StateObjectSortClause()
            )
        );

        while( !$taskIt->end() ) {
            if ( $taskIt->get('Caption') != '' && $taskIt->get('UID') != $openIt->get('UID') ) {
                $objectIt = $taskIt->getObjectIt();
                $info = $uid->getUIDInfo($objectIt);
                $taskName = $info['uid'] . ' ' . $taskIt->getDisplayNameExt();
                $actions[] = array (
                    'name' => $taskName,
                    'url' => $uid->getGotoUrl($objectIt)
                );
            }
            $taskIt->moveNext();
        }

        $portfolios = getFactory()->getObject('Portfolio')->getAll()->fieldToArray('CodeName');

        $actions[] = array();
        $actions[] = array(
            'name' => text(3112),
            'url' =>
                in_array('my', $portfolios)
                    ? '/pm/my/tasks/list/mytasks'
                    : (in_array('all', $portfolios)
                            ? '/pm/all/tasks/list/mytasks'
                            : '/pm/'.getSession()->getProjectIt()->get('CodeName').'/tasks/list/mytasks' )
        );
        return array(
            'title' => $title,
            'titleUrl' => $titleUrl,
            'actions' => $actions
        );
    }
}
