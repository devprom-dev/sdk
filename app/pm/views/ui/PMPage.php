<?php
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Tooltip\TooltipProjectService;

include SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
include SERVER_ROOT_PATH.'core/methods/BoardExportWebMethod.php';
include SERVER_ROOT_PATH.'core/methods/HtmlExportWebMethod.php';
include SERVER_ROOT_PATH.'core/methods/XmlExportWebMethod.php';
include_once SERVER_ROOT_PATH."pm/methods/UndoWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/methods/c_report_methods.php';
include_once SERVER_ROOT_PATH.'pm/methods/WikiExportBaseWebMethod.php';


include 'PMFormEmbedded.php';
include 'PMPageForm.php';
include 'PMPageTable.php';
include 'PageSectionLifecycle.php';
include "FieldHierarchySelectorAppendable.php";
include 'FieldCustomDictionary.php';
include 'FieldWYSIWYG.php';
include 'NetworkSection.php';
include 'PMPageNavigation.php';
include_once 'PMLastChangesSection.php';
include_once "DetailsInfoSection.php";
include_once 'BulkForm.php';
include 'converters/WikiIteratorExportHtml.php';
include 'converters/WikiIteratorExportPdf.php';

include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';
include_once SERVER_ROOT_PATH.'pm/views/comments/PageSectionComments.php';
include SERVER_ROOT_PATH.'pm/views/versioning/IteratorExportSnapshot.php';

class PMPage extends Page
{
    var $tabs, $areas;
    
    protected $settings;
    
    protected $report_uid;
    
    protected $report_base;
	protected $defaultListWidget = null;
    protected $nearestInfo = array();
    
    function PMPage()
 	{
	    getSession()->addBuilder( new WorkflowModelBuilder() );
 		parent::Page();
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
 	
 	function getRenderParms()
 	{
 	    $parms = parent::getRenderParms();

        foreach( getSession()->getBuilders('PageSettingBuilder') as $builder ) {
            $builder->build( $this->getSettingsBuilder() );
        }

		if ( $_REQUEST['report'] != '' )
        {
            $report = getFactory()->getObject('PMReport');
            $report_it = $report->getExact($_REQUEST['report']);

            if ( is_numeric($_REQUEST['report']) && !getFactory()->getAccessPolicy()->can_read($report_it) )
            {
                $custom_it = getFactory()->getObject('pm_CustomReport')->getExact($_REQUEST['report']);
                
                if ( $custom_it->getId() > 0 )
                {
                	$_REQUEST['basereport'] = $custom_it->get('ReportBase');
                }
            	
            	$base_it = $_REQUEST['basereport'] != ''
                    ? $report->getExact($_REQUEST['basereport'])
                    : ($_REQUEST['basemodule'] != ''
                           ? getFactory()->getObject('Module')->getExact($_REQUEST['basemodule'])
                            : $report->getEmptyIterator());

                if ( $base_it->getId() != '' )
                {
                    $item = $base_it->buildMenuItem(preg_replace('/report=[^&]*|project=[^&]*|view=[^&]*/', '', $_SERVER['QUERY_STRING']));

                    exit(header('Location: '._getServerUrl().$item['url'].'&'.$custom_it->getHtmlDecoded('Url')));
                }
            }
            else
            {
	            $this->setReport($_REQUEST['report']);
	            $this->setReportBase(
	            	$report_it->get('Report') != '' ? $report_it->get('Report') : $this->getReport()
	 	        );
	            $this->setModule( $report_it->get('Module') );
            }

            if ( $this->getReport() == '' ) {
                $report_it = $report->getByModule( $this->getModule() );
                $this->setReport($report_it->getId());
                $this->setReportBase($report_it->getId());
            }
        }

        $infos = $this->getInfoSections();
		if ( is_array($infos) )	{
			foreach ( $infos as $section ) {
				if ( $section instanceof DetailsInfoSection ) {
					$section->setActive($this->isDetailsActive());
				}
			}
		}

		return $parms;
 	}

    protected function buildNavigationParms() {
        return new PMPageNavigation($this);
    }

	function getFullPageRenderParms()
	{
		$parms = parent::getFullPageRenderParms();
                
	    if ( $this->getReport() != '' ) {
            $parms['navigation_title'] = getFactory()->getObject('PMReport')->getExact( $this->getReport() )->getDisplayName();
        }

		$bodyExpanded = $_COOKIE['menu-state'] == '' && defined('MENU_STATE_DEFAULT')
							? MENU_STATE_DEFAULT == 'minimized' : ($_COOKIE['menu-state'] == 'minimized');

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

        if ( $parms['navigation_url'] == '' && $this->needDisplayForm() ) {
            $info = $this->getPageWidgetNearestUrl();
            $parms['navigation_url'] = $info['url'];
            $parms['navigation_title'] = $info['name'];
        }

		return array_merge( $parms, 
			array (
				'caption_template' => 'pm/PageTitle.php',
				'project_code' => getSession()->getProjectIt()->get('CodeName'),
				'project_template' => getSession()->getProjectIt()->get('Tools'),
				'has_horizontal_menu' => getSession()->getProjectIt()->IsPortfolio() ? false : $parms['has_horizontal_menu'],
				'report' => $this->getReportBase(),
				'widget_id' => $this->getReport() != '' ? $this->getReport() : $this->getModule(),
				'bodyExpanded' => $bodyExpanded,
				'search_url' => getSession()->getApplicationUrl().'search.php'
			)
		);
	}

    function isDetailsActive() {
        return true;
    }

	function render( $view = null )
	{
		if ( $_REQUEST['attributeonly'] != '' )
		{
			$service = new ModelService(null, null, null);

			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header('Content-type: application/json; charset=utf-8');
			
			echo JsonWrapper::encode($service->get($_REQUEST['entity'], $_REQUEST['object'], 'html'));
			
			die();
		}
		
		parent::render( $view );
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
	        foreach( $areas as $area )
 		    {
 		    	if ( !is_array($area['menus']) ) continue;
 		    	
	            foreach ( $area['menus'] as $menu )
	            {
                    foreach( $menu['items'] as $item )
                    {
                        if ( $item['entry-point'] && $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) )
                        {
							if ( $this->checkWidgetExists($item) ) return $item['url'];
                        }
                    }
	            }
 		    }
 		    
 			foreach( $areas as $area )
 		    {
	            foreach ( $area['menus'] as $menu )
	            {
                    foreach( $menu['items'] as $item )
                    {
                        if ( $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) )
                        {
							if ( $this->checkWidgetExists($item) ) return $item['url'];
                        }
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
				$object_it = $this->getObject()->getExact(preg_split('/[,-]/',$_REQUEST['ids']));
				if ( $object_it->getId() == '' ) return;

				$reference = $this->getObject()->getAttributeObject($_REQUEST['attribute']);
				$ids = join(',',$object_it->fieldToArray($_REQUEST['attribute']));
				if ( $ids == '' ) $ids = '0';

				$it = getFactory()->getObject('ObjectsListWidget')->getAll();
				while( !$it->end() )
				{
					if ( is_a($reference, $it->get('Caption')) ) {
						$widget_it = getFactory()->getObject($it->get('ReferenceName'))->getExact($it->getId());
						exit(header('Location: '.$widget_it->getUrl(strtolower(get_class($reference)).'='.$ids)));
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
 	
 	function getBulkForm()
 	{
 	    return new BulkForm($this->getObject());
 	}
 	
 	function getFormBase()
 	{
 	    return null;
 	}
 	
 	function getForm()
 	{
 		return $this->getFormBase();
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
            if ( is_object($object_it) && $object_it->get('Project') != '' ) {
                return $object_it->get('Project') == getSession()->getProjectIt()->getId();
            }
            return true;
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
 		return true;
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

		if ( $_REQUEST['form'] != '' ) {
			$comment = getFactory()->getObject('Comment');
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

 	function buildCommentForm( $comment_it, $control_uid )
    {
        $form = new CommentForm(
            $comment_it->getId() > 0 ? $comment_it : $comment_it->object
        );
        $form->setControlUID( $control_uid );
        if ( $_REQUEST['dorefresh'] == 1 ) {
            $form->setRedirectUrl( "javascript: refreshCommentsThread(\\'".$control_uid."\\');" );
        }
        return $form;
    }

    function buildCommentList( $object_it ) {
        return new CommentList( $object_it );
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
                    'Content' => $this->buildExportContent($object_it, true),
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
        $extended = true;
        $service = new TooltipProjectService( get_class($object_it->object), $object_it->getId(), $extended );
        $data = $service->getData();
        $traceAttributes = $object_it->object->getAttributesByGroup('trace');

        ob_start();
        if ( $skipTitle ) {
            echo $data['type']['uid'];
            echo '<br/>';
            echo '<br/>';
        }

        foreach($data as $key => $section ) {
            switch( $key ) {
                case 'attributes':
                    foreach( $section as $attribute ) {
                        if ( $skipTitle && $attribute['name'] == 'Caption' ) continue;
                        if ( in_array($attribute['name'], $traceAttributes) ) continue;

                        echo '<b>'.$attribute['title'].'</b>: ';
                        switch( $attribute['type'] ) {
                            case 'wysiwyg':
                                echo $attribute['text'];
                                echo '<br/><br/>';
                                break;
                            default:
                                if ( $attribute['name'] == 'Caption' ) {
                                    echo $data['type']['uid'].' ';
                                }
                                echo $attribute['text'];
                                echo '<br/>';
                        }
                    }
                    break;
                case 'lifecycle':
                    echo '<b>'.$section['name'].'</b>: ';
                    echo $section['data']['state'];
                    echo '<br/>';
                    break;
                case 'type':
                    echo '<b>'.translate('Тип').'</b>: ';
                    echo $section['name'];
                    break;
            }
        }
        $html = ob_get_contents();
        ob_end_clean();

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
}
