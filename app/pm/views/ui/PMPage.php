<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;
use Devprom\ProjectBundle\Service\Model\ModelService;

include SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
include SERVER_ROOT_PATH.'core/methods/BoardExportWebMethod.php';
include SERVER_ROOT_PATH.'core/methods/HtmlExportWebMethod.php';

include_once SERVER_ROOT_PATH.'pm/methods/c_report_methods.php';

include 'PMFormEmbedded.php';
include 'PMPageForm.php';
include 'PMPageTable.php';
include 'PageSectionLifecycle.php';
include 'PMLastChangesSection.php';
include 'FieldCustomDictionary.php';
include 'FieldWYSIWYG.php';

include_once SERVER_ROOT_PATH.'pm/classes/common/ObjectMetadataSharedProjectBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

include_once SERVER_ROOT_PATH.'pm/views/comments/PageSectionComments.php';


include SERVER_ROOT_PATH.'pm/views/versioning/IteratorExportSnapshot.php';

class PMPage extends Page
{
    var $tabs, $areas;
    
    protected $settings;
    
    protected $report_uid;
    
    protected $report_base;
    
    function PMPage()
 	{
 		global $_REQUEST, $model_factory;

 		// extend metadata with the "Project" field for entities shared between projects, it impacts on UI representation
	    getSession()->addBuilder( new ObjectMetadataSharedProjectBuilder() );
	    
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
 		$builders = getSession()->getBuilders('PageSettingBuilder');
        
        foreach( $builders as $builder )
        {
            $builder->build( $this->getSettingsBuilder() );
        }

 		$report = getFactory()->getObject('PMReport');
	    
		if ( $_REQUEST['report'] != '' )
        {
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
	            $this->setReport($report_it->getId());
	                
	            $this->setReportBase(
	            	$report_it->get('Report') != '' 
	                	? $report_it->get('Report') : $this->getReport()
	 	        );
	                
	            $this->setModule( $report_it->get('Module') );
            }
        }
        
 		return parent::getRenderParms();
 	}
 	
	function getFullPageRenderParms()
	{
		$report = getFactory()->getObject('PMReport');
		
        if ( $this->getReport() == '' && $this->getModule() != '' )
        {
			$report_it = $report->getByModule( $module_uid );
                
            $this->setReport($report_it->getId());
                
            $this->setReportBase($report_it->getId());
        }

		$parms = parent::getFullPageRenderParms();
                
	    if ( $this->getReport() != '' )
        {
            $parms['navigation_title'] = $report->getExact( $this->getReport() )->getDisplayName();
        }
	    
		return array_merge( $parms, 
				array (
					'caption_template' => 'pm/PageTitle.php',
				    'project_code' => getSession()->getProjectIt()->get('CodeName'),
					'project_template' => getSession()->getProjectIt()->get('Tools'),
					'has_horizontal_menu' => getSession()->getProjectIt()->IsPortfolio() ? false : $parms['has_horizontal_menu'],
					'menus' => $this->getTopMenus(),
					'report' => $this->getReportBase()
				)
		);
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
			
			echo JsonWrapper::encode($service->get( $_REQUEST['entity'], $_REQUEST['object']));
			
			die();
		}
		
		parent::render( $view );
	}
	
	function getRedirect()
	{
        $service = new WorkspaceService();
        
 		$this->areas = $service->getFunctionalAreas();

 		foreach( $this->areas['favs']['menus'] as $menu => $value )
 		{
	 		foreach( $this->areas['favs']['menus'][$menu]['items'] as $key => $value )
	 		{
	 			$this->areas['favs']['menus'][$menu]['items'][$key]['entry-point'] = true;
	 			
	 			break;
	 		}
	 		
	 		break;
 		}
 		
 		if ( $_REQUEST['tab'] != '' )
 		{
 		    $parts = preg_split('/\//', $_REQUEST['tab']);
 		    
 		    if ( count($parts) == 3 )
 		    {
     		    foreach( $this->areas as $area )
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
 		    // if no tab is specified then use default entry
 		    
	        foreach( $this->areas as $area )
 		    {
 		    	if ( !is_array($area['menus']) ) continue;
 		    	
	            foreach ( $area['menus'] as $menu )
	            {
                    foreach( $menu['items'] as $item )
                    {
                        if ( $item['entry-point'] && $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) )
                        {
                            return $item['url'];
                        }
                    }
	            }
 		    }
 		    
 			foreach( $this->areas as $area )
 		    {
	            foreach ( $area['menus'] as $menu )
	            {
                    foreach( $menu['items'] as $item )
                    {
                        if ( $item['url'] != '' && !in_array($item['uid'], array('navigation-settings')) )
                        {
                            return $item['url'];
                        }
                    }
	            }
 		    }
 		}		
	}
	
	function getTabsTemplate()
	{
		return 'pm/PageTabs.php'; 	
	}
	
	function getTabsParameters()
	{
	    $session = getSession();
	     
	    $project_it = $session->getProjectIt();
	     
	    return array_merge( parent::getTabsParameters(), array (
	        'project_code' => $project_it->get('CodeName')
	    ));
	}
	
	function getTopMenus()
 	{
 		global $plugins, $model_factory;
 		
 		$part_it = getSession()->getParticipantIt();
 		
 		$menus = array();

 		$plugin_menus = $plugins->getHeaderMenus( 'pm' );

		foreach ( $plugin_menus as $menu )
		{
			$menus[] = array (
				'class' => 'header_popup',
				'button_class' => $menu['class'],
				'title' => $menu['caption'],
				'description' => $menu['title'],
				'url' => $menu['url'],
				'items' => $menu['actions']
			);
		}
 		
 		// quick menu actions
		$actions = array();

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_ChangeRequest'));

		if ( !in_array($this->getModule(), array('issues-backlog', 'issues-board', 'kanban/requests')) )
		{
			$info = getFactory()->getObject('PMReport')->getExact('productbacklog')->buildMenuItem();
			
			$method->setRedirectUrl(
					"function() { window.location = '".$info['url']."'; }"
			);
		}
		else
		{
			$method->setRedirectUrl('donothing');
		}
		
		if ( $method->hasAccess() )
		{
			
			$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query( 
					array (
							new FilterBaseVpdPredicate()
					)
				);
			
			while ( !$type_it->end() )
			{
				$actions[] = array ( 
						'name' => translate($type_it->getDisplayName()),
						'url' => $method->getJSCall( 
									array (
										'Type' => $type_it->getId(),
										'area' => $this->getArea()
									),
									translate($type_it->getDisplayName())
								 )
				);
				
				$type_it->moveNext();
			}

			$actions[] = array ( 
					'name' => $method->getObject()->getDisplayName(),
					'url' => $method->getJSCall( 
								array (
									'area' => $this->getArea()
								)
							 )
			);
			
			$template_it = getFactory()->getObject('RequestTemplate')->getAll();
			
			if ( $template_it->count() > 0 && $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
			
			while( !$template_it->end() )
			{
				$actions[] = array ( 
						'name' => $template_it->getDisplayName(),
						'url' => $method->getJSCall( 
									array (
										'template' => $template_it->getId(),
										'area' => $this->getArea()
									)
								 )
				);
				
				$template_it->moveNext();
			}
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Task'));
		
		if ( !in_array($this->getModule(), array('tasks-list', 'tasks-board')) )
		{
			$info = getFactory()->getObject('PMReport')->getExact('iterationplanningboard')->buildMenuItem();
			
			$method->setRedirectUrl(
					"function() { window.location = '".$info['url']."'; }"
			);
		}
		else
		{
			$method->setRedirectUrl('donothing');
		}
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && $method->hasAccess() )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();

			$actions[] = array ( 
					'name' => $method->getObject()->getDisplayName(),
					'url' => $method->getJSCall( 
								array (
									'Assignee' => getSession()->getParticipantIt()->getId(),
									'area' => $this->getArea()
								)
							 )
			);
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Question'));
		
		if ( $this->getReportBase() != 'project-question' )
		{
			$info = getFactory()->getObject('PMReport')->getExact('project-question')->buildMenuItem();
			
			$method->setRedirectUrl(
					"function() { window.location = '".$info['url']."'; }"
			);
		}
 		else
		{
			$method->setRedirectUrl('donothing');
		}
		
		if ( $method->hasAccess() )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();

			$actions[] = array ( 
					'name' => $method->getObject()->getDisplayName(),
					'url' => $method->getJSCall( 
								array (
									'area' => $this->getArea()
								)
							 )
			);
		}
		
		$quick_actions = $plugins->getQuickActions('pm');
		
		if ( count($quick_actions) > 0 )
		{
			foreach ( $quick_actions as $action )
			{
				array_push( $actions, $action );
			}
		}
		
		if ( count($actions) > 0 )
		{
			$menus[] = array (
				'class' => 'header_popup',
				'button_class' => 'btn-warning',
				'title' => translate('Создать'),
				'items' => $actions
			);
		}
							
 		
 		$actions = array();

		// profile actions
		$actions = array();

		$user_name = $part_it->getDisplayName();

		$actions[] = array ( 
		    'name' => translate('Профиль пользователя'),
			'url' => '/profile'
		);
		
		$policy = getFactory()->getAccessPolicy();
		
		if ( !in_array($policy->getRoleReferenceName(array_pop($policy->getRoles())), array('guest','linkedguest')) ) 
		{
		    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		    
			$user_name = $part_it->getDisplayName();

			$actions[] =  array ( 
			    'name' => translate('Профиль участника'),
				'url' => getSession()->getApplicationUrl().'profile.php' 
			);
		}
		
		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		
		$actions[] = array ( 
			    'name' => text(1811),
				'url' => getFactory()->getObject('Module')->getExact('project-reports')->get('Url') 
		);
		
		$auth_factory = getSession()->getAuthenticationFactory();
		 
		if ( is_object($auth_factory) && $auth_factory->tokenRequired() )
		{
    	    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
    	    
    	    array_push( $actions, array ( 
    	        'name' => translate('Выйти'),
    		    'url' => '/logoff' 
    		));
		}
		
		$menus[] = array (
			'class' => 'header_popup',
			'title' => getSession()->getUserIt()->getDisplayName(),
			'items' => $actions
		);
		
 		return $menus;
 	}

 	function getAreas()
 	{
 	    return $this->areas;
 	}
 	
 	function export()
 	{
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['export'] )
 		{
 			case 'commentsthread':
 				return $this->exportCommentsThread();
 				
 			default:
 				return parent::export();
 		}
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
 	
 	function hasAccess()
 	{
 	    if ( !parent::hasAccess() ) return false;

 	    if ( $this->needDisplayForm() ) return true;
 	    
 	    // report based permissions to display the page
        $report_uid = $this->getReport();

        if ( $report_uid != '' )
        {
            return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('PMReport')->getExact($report_uid));
        }

        $module_uid = $this->getModule();

        if ( $module_uid != '' )
        {
        	return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Module')->getExact($module_uid));
        }
 	    
 		return false;
 	}
    
 	function exportCommentsThread()
 	{
 		global $_REQUEST, $model_factory;

		if ( $_REQUEST['object'] < 1 || $_REQUEST['objectclass'] == '' ) return;

	 	$object = $model_factory->getObject($_REQUEST['objectclass']);
	 	
	 	$object_it = $object->getExact($_REQUEST['object']);
	 	
	 	if ( !getFactory()->getAccessPolicy()->can_read($object_it) ) return;

		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset=windows-1251');
	 	
		$control_uid = md5($object->getClassName().$object_it->getId());
		
		if ( $_REQUEST['form'] != '' )
		{
			$comment = $model_factory->getObject('Comment');
			
			$comment->setVpdContext( $object_it );
			
		    $comment_it = $_REQUEST['comment'] > 0 
		        ? $comment->getExact( $_REQUEST['comment'] )
		        : $comment->getEmptyIterator();
			
			$form = new CommentForm( $comment_it->getId() > 0 
				? $comment_it : $comment );
			
			$form->setControlUID( $control_uid );	
			
			$form->setRedirectUrl( 'javascript: refreshCommentsThread(\\\''.$control_uid.'\\\');' ); 
					
			$parms['prevcomment'] = $_REQUEST['prevcomment'];
			
			$form->render( $this->getRenderView(), $parms );
		}
		else
		{
			$comment_list = new CommentList( $object_it );
			
			$comment_list->setControlUID( $control_uid );	
			
			$comment_list->render( $this->getRenderView(), array() );
		}
 	}
 	
 	function getProjectNavigationParms( $tab_uid )
 	{
 		$parms = parent::getProjectNavigationParms( $tab_uid );
 		
		$project_it = getSession()->getProjectIt();

		$parms['current_project'] = $project_it->get('CodeName'); 
		$parms['current_project_title'] = $project_it->getDisplayName();
		
		if ( !$project_it->IsPortfolio() && !$project_it->IsProgram() )
		{
			$project_it = $project_it->getParentIt();
		}
		
		if ( $project_it->IsPortfolio() )
		{
		    $portfolio_it = $project_it;
		    
		    $parms['portfolio_title'] = translate('Группа проектов');
		    $parms['subprojects_title'] = translate('Проекты в группе');
		}
		else
		{
		    $portfolio_it = $project_it;
		    
		    $parms['portfolio_title'] = translate('Программа');
		    $parms['subprojects_title'] = translate('Подпроекты');
		}
		
		$parms['current_portfolio'] = $portfolio_it->get('CodeName');
		$parms['current_portfolio_title'] = $portfolio_it->getDisplayName();
		
	 	if ( $portfolio_it->get('CodeName') == 'my' )
		{
		    $parms['title'] = translate('Мои проекты');
		}
		else
		{
		    $parms['title'] = translate('Подпроекты');
		}
		
 		$portfolio_actions = array();

		if ( $portfolio_it->IsProgram() )
		{
			$url = '/pm/'.$portfolio_it->get('CodeName').'/module/ee/projectlinks'.getFactory()->getObject('ProjectLink')->getPageNameObject();
			
			$portfolio_actions[] = array (
					'icon' => 'icon-plus',
					'url' => $url.ProjectLinkTypeSet::SUBPROJECT_QUERY_STRING,
					'name' => text('ee204')
			);
		}
		
		$project_actions = array();
		
		$module_it = getFactory()->getObject('Module')->getExact('ee/projectlinks');
		
		if ( $module_it->getId() != '' )
		{
			$url = $module_it->get('Url').getFactory()->getObject('ProjectLink')->getPageNameObject();
			
			$project_actions[] = array (
					'icon' => 'icon-plus',
					'url' => $url.ProjectLinkTypeSet::SUBPROJECT_QUERY_STRING,
					'name' => text('ee204')
			);

			$project_actions[] = array (
					'icon' => 'icon-arrow-right',
					'url' => $url.ProjectLinkTypeSet::PROGRAM_QUERY_STRING,
					'name' => text('ee205')
			);
		}

		$parms['portfolio_actions'] = $portfolio_actions;
		$parms['project_actions'] = $project_actions;

		return $parms;
 	}
 	
 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');
		
		$resource_it = $resource->getExact($this->getReportBase());
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');

		return parent::getHint();
	}
}
