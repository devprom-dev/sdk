<?php

use Symfony\Component\Templating\PhpEngine;
 use Symfony\Component\Templating\TemplateNameParser;
 use Symfony\Component\Templating\Loader\FilesystemLoader;
 use Symfony\Component\Templating\Helper\SlotsHelper;
 use Devprom\CommonBundle\Service\Widget\ScriptService;
 
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportHtml.php';
include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';
include_once SERVER_ROOT_PATH.'core/classes/system/Coloring.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
include SERVER_ROOT_PATH.'core/methods/ObjectModifyWebMethod.php';

include_once 'PageInfoSection.php';
include 'PageTable.php';
include 'PageTableStatic.php';
include 'PageList.php';
include 'PageListStatic.php';
include 'PageBoard.php';
include 'PageChart.php';
include 'PageForm.php';
include 'PageMenu.php';
include_once 'PageSectionLastChanges.php';
include "FullScreenSection.php";
include "PageSectionAttributes.php";
include "BulkFormBase.php";
 
class Page
{
 	var $infosections = array();
 	var $table;
 	var $form;
 	var $notfound;
 	var $injections;
 	private $module = '';
 	
 	private $render_parms = array();
 	
 	function Page() 
 	{
 		global $plugins;

 	    $this->form = $this->buildForm();
 		
 		if ( is_object($this->form) && is_a($this->form, 'PageForm') ) {
		    $this->form->setPage( $this );
		}

		if ( is_a($this->form, 'MetaobjectForm') && $this->form->getAction() != '' ) {
			if ( $this->needDisplayForm() ) {
				$this->form->process();
			}
		}
		
		$this->table = $this->getTable();
		if ( is_object($this->table) && is_a($this->table, 'PageTable') )
		{
		    $this->table->setPage( $this );
		}
 		
		$this->notfound = false;
		if ( is_object($plugins) )
		{
 			$this->infosections = array_merge($this->infosections, $plugins->getPageInfoSections( $this ));
            foreach( $this->infosections as $key => $section ) {
                $this->infosections[$key]->setPage($this);
            }
		}
 	}
 	
  	function __destruct()
 	{
 		$this->form = null;
 		$this->table = null;
 	}
 	
 	function addInfoSection( $infosection_object ) 
 	{
 		$infosection_object->setPage( $this );
 		$this->infosections[$infosection_object->getId()] = $infosection_object;
 	}
 	
 	function & getInfoSections()
 	{
 		return $this->infosections;
 	}
 	
 	function getTable() 
 	{
 		return null;
 	}
 	
 	function getTableRef()
 	{
 		return $this->table;
 	}
 	
  	function getBulkForm()
 	{
 		return new BulkFormBase($this->getObject());
 	}
 	
 	function buildForm()
 	{
 		if ( $_REQUEST['bulkmode'] != '' ) {
 			return $this->getBulkForm();
 		}
 		return $this->getForm();
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getFormRef()
 	{
 		return $this->form;
 	}

 	function needDisplayForm() 
 	{
 		return $_REQUEST['entity'] != '' || $_REQUEST['action_mode'] == 'form' || $_REQUEST['bulkmode'] != '' || $_REQUEST['formonly'] != '';
 	}
 	
 	function showFullPage()
 	{
 		return $_REQUEST['tableonly'] == '' && $_REQUEST['formonly'] == ''; 
 	}
 	
 	function getObjectIt()
 	{
 		$form = $this->getFormRef();
 		
 		if ( !is_object($form) ) return null;

 		return $this->getFormRef()->getObjectIt();
 	}
 	
 	function getObject()
 	{
 		$table = $this->getTableRef();
 		
 		if ( is_object($table) )
 		{
 			return $table->getObject();
 		}
 		
 		$form = $this->getFormRef();
 		
 		if ( is_object($form) )
 		{
 			return $form->getObject();
 		}
 	}
 	
 	function hasAccess()
 	{
 		if ( is_subclass_of($this->table, 'ViewTable') )
 		{
		 	return getFactory()->getAccessPolicy()->can_read($this->table->getObject());
 		}
 		
 		return true;
 	}
 	
 	function drawFooter()
 	{
 	}
 	
 	function notFound()
 	{
 		$this->notfound = true;
 	}
 	
 	function getTitle()
 	{
 		$object_it = $this->getObjectIt();
 		
 		if ( is_object($object_it) && $object_it->object->getAttributeDbType('Caption') != '' )
 		{
 			return translate($object_it->getDisplayName());
 		}
 		
 		if ( is_object($this->table) )
 		{
 			return $this->table->getCaption();
 		}

 	 	if ( is_object($this->form) )
 		{
 			return $this->form->getCaption();
 		}
 		
		return '';
 	}
 	
 	function authorizationRequired()
 	{
 		return true;
 	}
 	
 	function export()
 	{
		// initialize page object
 		$parms = $this->getFullPageRenderParms();
 		
 		switch ( $_REQUEST['export'] )
 		{
 			case 'section':
 				return $this->exportSection();
 			default:
 				return $this->exportIterator();
 		}
 	}
 	
 	function exportIterator()
 	{
 		global $_REQUEST, $model_factory;

 		$table = $this->getTableRef();
 		
		if ( $_REQUEST['class'] == '' )
		{
		    throw new Exception('Required parameter is missed: "class" should be given');
		}
		    
		if( !class_exists($_REQUEST['class']) || !is_subclass_of($_REQUEST['class'], 'IteratorExport') )
		{
			throw new Exception('Given iterator "'.$_REQUEST['class'].'" cant be instantiated');
		}
 		
		if ( $_REQUEST['objects'] == '' )
 		{
			$it = $table->getListIterator();
			
			$it->moveFirst();
 		}
 		else
 		{
 			$object = $_REQUEST['entity'] == '' 
 					? $table->getObject() : $model_factory->getObject($_REQUEST['entity']);

 			if ( is_object($table) && is_a($table, 'PageTable') )
 			{
 				$list = $table->getListRef();
 				
	 			if ( is_object($list) )
	 			{
					$sorts = $list->getSorts();
					foreach ( $sorts as $sort )
					{
						$object->addSort( $sort );
					}
	 			}
 			}
 				
			$it = $object->getExact( preg_split('/-/', trim($_REQUEST['objects'], '-')) );
 		}

		if ( !is_object($it) ) return false;

		$fields = array();
			
		if ( is_a( $table, 'PageTable' ) )
		{
			$view = $table->getViewFilter();
				
			if ( is_object($view) )
			{
				$view->setFilter( $table->getFiltersName() );
					
				if ( $view->getValue() != '' )
				{
					$table->setList( $table->getList( $view->getValue(), $it ) );
				}
			}
				
			$list = $table->getListRef();
			
			if ( is_object($list) )
			{
    			$list->setupColumns();
    				
    			$columns = $list->getColumnsRef();
    				
    			foreach( $columns as $column )
    			{
    				if ( !$list->getColumnVisibility($column) ) continue;
    
    				if( $column == 'UID' )
    				{
    					$fields[$column] = translate('UID');
    					continue;
    				}
    					
    				$fields[$column] = translate($it->object->getAttributeUserName($column));
    			}
			}
			
			if ( $_REQUEST['caption'] == '' ) $_REQUEST['caption'] = $table->getCaption();
		}

		$eit = new $_REQUEST['class']( $it );
			
		$eit->setTable($table);
		
		$eit->setFields( $fields );

		$eit->setName($_REQUEST['caption']);

		$eit->export();
			
		return true;
 	}
 	
 	function exportSection()
 	{
 		if ( $_REQUEST['class'] == '' ) return false;
 		if ( $_REQUEST['section'] == '' ) return false;
 		$class_name = $_REQUEST['section'];
 		if ( !class_exists($class_name, false) ) return false;
 		
 		$object = getFactory()->getObject($_REQUEST['class']);
 		$ids = array_filter(preg_split('/,/', $_REQUEST['id']), function( $val ) {
 		    return is_numeric($val);
 		});
 		
 		$object_it = count($ids) > 0 ? $object->getExact($ids) : $object->getEmptyIterator();

		$section = new $class_name(
		        is_object($object_it) && $object_it->getId() > 0 ? $object_it : $object
		);
		
		$this->addInfoSection( $section );
 		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset='.APP_ENCODING);
  	
		$section->render( $this->getRenderView(), $this->getRenderParms() );
 	 	
 	 	return true;
 	}
 	
 	function getApplicationUrl()
 	{
 	    return _getServerUrl();
 	}
 	
 	function getNavigationContext( & $areas, $active_url )
 	{
		$active_area_uid = '';
 	    foreach( $areas as $key => $area )
        {
            foreach ( $area['menus'] as $tab_key => $tab )
            {
                foreach( $tab['items'] as $item_key => $item )
                {
                    if ( $item['url'] == '' )
                    {
                        unset($areas[$key]['menus'][$tab_key]['items'][$item_key]);
                        
                        continue;
                    }
                    
                    $parts = preg_split('/\?/', str_replace(getSession()->getApplicationUrl(), '', $item['url']));
        
                    if ( trim($parts[0],'/') == trim($active_url,'/') && $active_area_uid == '' )
                    {
                        $active_area_uid = $area['uid'];
                        
                        $tab_uid = $area['uid'].'/'.$tab['uid'].'/'.$item['uid'];
                        
                        $tab_item = $item;
                    }

                    $areas[$key]['menus'][$tab_key]['items'][$item_key]['url'] .= count($parts) > 1 ? '&area='.$area['uid'] : '?area='.$area['uid'];
                }
            }
        }

        if ( !is_array($tab_item) )
        {
        	$tab_url = getSession()->getApplicationUrl().$active_url;
        	$module_it = getFactory()->getObject('Module')->getByRef('Url', $tab_url);
         	
        	$active_area_uid = 'favs';
        	
        	$tab_item['title'] = $module_it->getDisplayName();
        	$tab_item['module'] = $module_it->getId();
        	$tab_item['url'] = $tab_url; 
        }

        return array(
                'area_uid' => $active_area_uid,
                'item_path' => $tab_uid,
                'item' => $tab_item
        );
 	}
 	
 	function getProjectNavigationParms( $tab_uid )
 	{
		global $model_factory;
		
		$programs = array();
		
		$projects = array();
		
		if ( $model_factory->getObject('User')->getAttributeType('GroupId') != '' )
		{
			$program_it = $model_factory->getObject('Program')->getAll();
		    
		    while ( !$program_it->end() )
		    {
		    	$query_parms = array (
          				new ProjectStatePredicate('active'),
          				new FilterInPredicate(preg_split('/,/', $program_it->get('LinkedProject'))),
						new SortAttributeClause('Importance'),
						new SortAttributeClause('Caption')
           		);
		    	
		   		if ( $program_it->get('IsParticipant') < 1 )
		   		{
		   			$query_parms[] = new ProjectParticipatePredicate();
		   		}		    	
		    	
		        $linked_it = $program_it->get('LinkedProject') != '' 
		                ? getFactory()->getObject('Project')->getRegistry()->Query($query_parms)
		                : $model_factory->getObject('Project')->getEmptyIterator();
		        
		        while ( !$linked_it->end() )
		        {
		        	if ( $program_it->getId() == $linked_it->getId() ) 
		        	{
		        		$linked_it->moveNext();
		        		continue;
		        	}
		        	
		            $projects[$program_it->get('CodeName')][$linked_it->get('CodeName')] = array (
		                'name' => $linked_it->getDisplayName(),
		                'url' => '/pm/'.$linked_it->get('CodeName')
		            ); 
		            
		            $linked_it->moveNext();
		        }
		
		        if ( count($projects[$program_it->get('CodeName')]) > 0 )
		        {
		            $programs[$program_it->get('CodeName')] = array (
		                'name' => $program_it->getDisplayName(),
		                'url' => '/pm/'.$program_it->get('CodeName')
		            );
		        }
		        
		        $program_it->moveNext();
		    }
		}
		
		$portfolios = array();
		
		$portfolio_it = $model_factory->getObject('Portfolio')->getAll();
		while ( !$portfolio_it->end() )
		{
		    if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) )
		    {
		        $portfolio_it->moveNext(); continue;
		    }

		    if ( $portfolio_it->get('CodeName') != 'all' || !defined('PERMISSIONS_ENABLED') )
		    {
		        $linked_it = $portfolio_it->get('LinkedProject') != '' 
		                ? getFactory()->getObject('Project')->getRegistry()->Query(
		                		array (
		                				new ProjectStatePredicate('active'),
		                				new FilterInPredicate(preg_split('/,/', $portfolio_it->get('LinkedProject'))),
										new SortAttributeClause('Importance'),
										new SortAttributeClause('Caption')
		                		)
		                  )
		                : $model_factory->getObject('Project')->getEmptyIterator();

		        while ( !$linked_it->end() ) {
		        	if ( $portfolio_it->getId() == $linked_it->getId() || array_key_exists($linked_it->get('CodeName'), $programs) ) {
		        		$linked_it->moveNext();
		        		continue;
		        	}
		            $projects[$portfolio_it->get('CodeName')][$linked_it->get('CodeName')] = array (
		                'name' => $linked_it->getDisplayName(),
		                'url' => '/pm/'.$linked_it->get('CodeName')
		            ); 
		            $linked_it->moveNext();
		        }
		    }

		    if ( in_array($portfolio_it->get('CodeName'), array('all', 'my')) || count($projects[$portfolio_it->get('CodeName')]) > 0 )
		    {
		        $portfolios[$portfolio_it->get('CodeName')] = array (
		            'name' => $portfolio_it->getDisplayName(),
		            'url' => '/pm/'.$portfolio_it->get('CodeName')
		        );
		    }
		    
		    $portfolio_it->moveNext();
		}

		if ( !defined('PERMISSIONS_ENABLED') ) {
			$linked_it = getFactory()->getObject('Project')->getRegistry()->Query(
				array(
					new ProjectStatePredicate('active'),
					new SortAttributeClause('Importance'),
					new SortAttributeClause('Caption')
				)
			);
		}
		else {
			$linked_it = getFactory()->getObject('Project')->getRegistry()->Query(
				array (
					new ProjectParticipatePredicate(),
					new ProjectStatePredicate('active'),
					new SortAttributeClause('Importance'),
					new SortAttributeClause('Caption')
				)
			);
		}
		while ( !$linked_it->end() )
		{
			$projects[''][$linked_it->get('CodeName')] = array (
				'name' => $linked_it->getDisplayName(),
				'url' => '/pm/'.$linked_it->get('CodeName')
			);
			$linked_it->moveNext();
		}

		foreach( $programs as $program_id => $program ) {
			unset( $projects['my'][$program_id] );
			unset( $projects['all'][$program_id] );
			unset( $projects[''][$program_id] );
			if ( !is_array($projects[$program_id]) ) continue;
			foreach( $projects[$program_id] as $project_id => $project ) {
				unset( $projects['my'][$project_id] );
				unset( $projects['all'][$project_id] );
				unset( $projects[''][$project_id] );
			}
		}
		foreach( $portfolios as $portfolio_id => $portfolio ) {
		    if ( in_array($portfolio_id, array('my','all')) ) continue;
		    if ( !is_array($projects[$portfolio_id]) ) continue;
		    foreach( $projects[$portfolio_id] as $project_id => $project ) {
		        unset( $projects['my'][$project_id] );
				unset( $projects['all'][$project_id] );
				unset( $projects[''][$project_id] );
		    }
		}
		foreach( $portfolios as $portfolio_id => $portfolio ) {
			if ( !in_array($portfolio_id, array('my','all')) ) continue;
			if ( is_array($projects[$portfolio_id]) ) {
				foreach( $projects[$portfolio_id] as $project_id => $project ) {
					unset( $projects[''][$project_id] );
				}
			}
		}

		return array (
			'programs' => $programs,
			'portfolios' => $portfolios,
			'projects' => $projects,
			'company_actions' => $this->getProjectNavigatorActions($projects),
			'portfolio_actions' => $this->getPortfolioActions($projects),
			'admin_actions' => $this->getAdministrationActions()
		);
 	}
 	
 	function getProjectNavigatorActions( $projects )
 	{
 		$company_actions = array();
		
		if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) )
		{
			$skip_welcome = getFactory()->getObject('UserSettings')->getSettingsValue('projects-welcome-page');
			$company_actions[] = array (
					'icon' => 'icon-plus',
					'url' =>  $skip_welcome != 'off' && !defined('SKIP_WELCOME_PAGE')
									? '/projects/welcome'
									: '/projects/new',
					'name' => text('project.new')
			);
		}

		$portfolio = getFactory()->getObject('co_ProjectGroup');
		if ( count($projects) > 0 && getFactory()->getAccessPolicy()->can_create($portfolio) )
		{
			$method = new ObjectCreateNewWebMethod($portfolio);
			$method->setRedirectUrl('function(id){window.location=\'/pm/project-portfolio-\'+id;}');
			$company_actions[] = array (
				'icon' => 'icon-briefcase',
				'url' => $method->getJSCall(),
				'name' => text('portfolio.new')
			);
		}

		return $company_actions;
 	}

	function getPortfolioActions( $projects )
	{
		$actions = array();
		if ( count($projects) < 2 ) return $actions;

		$project_it = getSession()->getProjectIt();
		if ( $project_it->get('ProjectGroupId') < 1 ) return $actions;

		if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) ) {
			$actions[] = array (
				'icon' => 'icon-plus',
				'url' =>  '/projects/welcome?portfolio='.$project_it->get('ProjectGroupId'),
				'name' => text('project.new')
			);
		}

		$portfolio = getFactory()->getObject('co_ProjectGroup');
		if ( getFactory()->getAccessPolicy()->can_modify($portfolio) )
		{
			$method = new ObjectModifyWebMethod(
				$portfolio->getExact($project_it->get('ProjectGroupId'))
			);
			$actions[] = array (
				'icon' => 'icon-briefcase',
				'url' => $method->getJSCall(),
				'name' => translate('Изменить')
			);
		}
		return $actions;
	}
 	
 	function getAdministrationActions()
 	{
 		$actions = array();
 		if ( getSession()->getUserIt()->get('IsAdmin') == 'Y' )
 		{
			$actions[] = array (
					'icon' => 'icon-wrench', 
			        'name' => translate('Администрирование'),
					'url' => '/admin/'
		    );
 		}
 		return array_merge($this->getAddParticipantActions(),$actions);
 	}
 	
 	function getAddParticipantActions()
 	{
 		$actions = array();
		if ( !defined('INVITE_USERS_ANYBODY') || INVITE_USERS_ANYBODY !== false )
		{
		 	$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
			$actions[] = array (
					'icon' => 'icon-user',
					'name' => text(2001),
					'url' => $method->getJSCall(array(), text(2001))
			);
		}
		return $actions;
 	}
 	
 	function getRenderParms()
 	{
 		if ( count($this->render_parms) > 0 ) return $this->render_parms;
 		 
 		$sections = array();
		$infos = $this->getInfoSections();

		if ( is_array($infos) )	{
			foreach ( $infos as $section ) {
				if ( !$section->isActive() ) continue;
				if ( $section instanceof PageSectionAttributes ) {
					if ( $_REQUEST['formonly'] == '' ) continue;
					if ( count($section->getAttributes()) < 1 ) continue;
				}
				$sections[$section->getId()] = $section;
			}
		}

     	$bottom_sections = array();
		$last_sections = array();
        foreach( $sections as $key => $section ) { 
            if ( $_REQUEST['formonly'] == '' && $section->getPlacement() == 'bottom' ) {
				if ( $section instanceof NetworkSection ) {
					$last_sections[] = $section;
				}
				else if ( $section instanceof PageSectionComments ) {
					$bottom_sections = array_merge($bottom_sections, array($section->getId() => $section));
				}
				else {
					$bottom_sections[$section->getId()] = $section;
				}
                unset($sections[$key]);
            }
        }
		$bottom_sections = array_merge($bottom_sections, $last_sections);

 		$this->render_parms = array(
 			'current_version' => $_SERVER['APP_VERSION'],
 			'object_class' => get_class($this->getObject()),
 			'object_id' => is_object($this->getObjectIt()) ? $this->getObjectIt()->getId() : '',
            'license_name' => getFactory()->getObject('LicenseState')->getAll()->getDisplayName(),
            'language_code' => strtolower(getSession()->getLanguageUid()),
 		    'datelanguage' => getLanguage()->getLocaleFormatter()->getDatepickerLanguage(),
            'dateformat' => getLanguage()->getDatepickerFormat(),
			'datejsformat' => getLanguage()->getLocaleFormatter()->getDateJSFormat(),
 		    'company_name' => getFactory()->getObject('cms_SystemSettings')->getAll()->get('Caption'),
 		    'application_url' => $this->getApplicationUrl(),
 		    'display_form' => $this->needDisplayForm(),
 			'sections' => $sections,
        	'bottom_sections' => $bottom_sections
 		);
 		
 		return $this->render_parms;
 	}
 	
 	function getFullPageRenderParms()
 	{
        // get active functional area
        
        $areas = $this->getAreas();
        $active_url = str_replace(getSession()->getApplicationUrl(), '', array_shift(preg_split('/\?/', $this->getPageUrl())));

        foreach( $areas as $key => $area )
        {
    		if ( $this->getArea() != '' && $area['uid'] == $this->getArea() ) {
            	$active_area_uid = $area['uid'];
			}

			$values = is_array($area['menus']) ? array_values($area['menus']) : array();
			
			$first_menu = count($values) > 0 ? array_shift($values) : array();

			if ( in_array($area['uid'], array('main','favs')) ) continue;

			// remove area if there are no items in the first vertical subsection
			if ( is_array($first_menu['items']) )
			{
				$items = array_filter( $first_menu['items'], function($value) {
						return $value['uid'] != '' && $value['uid'] != 'navigation-settings' ;
				});
			}
			else
			{
				$items = array();	
			}
			
			if ( count($items) < 1 ) unset($areas[$key]);
		}
   
        $context = $this->getNavigationContext( $areas, $active_url );

        $active_area_uid = $active_area_uid != '' && array_key_exists($active_area_uid, $areas)
            ? $active_area_uid : ($context['area_uid'] != '' && array_key_exists($context['area_uid'], $areas) 
                    ? $context['area_uid'] : array_shift(array_keys($areas)));
         
        $tab_uid = $context['item_path'];
        
        getSession()->setActiveTab( $tab_uid );
        
        $tab_title = $context['item']['title'] != '' ? $context['item']['title'] : $context['item']['name'];

        $tab_url = $context['item']['uid'] != '' ? $context['item']['url'] : '';
		$active_url = array_shift(preg_split('/\?/',$context['item']['url']));

        if ( $context['item']['module'] != '' ) $this->setModule($context['item']['module']);

        $first_menu = count($areas) > 0 ? array_pop(array_values($areas)) : array();

        $script_service = new ScriptService();
        
        $page_uid = $this->getPageUid();

		list($alerts, $alerts_url) = $this->getCheckpointAlerts();

		return array(
 			'inside' => count($first_menu['menus']) > 0,
 			'title' => $this->getTitle() != '' ? $this->getTitle() : $tab_title,
 		    'navigation_title' => $tab_title != '' ? $tab_title : $this->getTitle(),
 		    'navigation_url' => $tab_url,
			'active_url' => $active_url,
 			'checkpoint_alerts' => $alerts,
			'checkpoint_url' => $alerts_url,
 			'menus' => $this->getMenus(),
 			'tabs_template' => $this->getTabsTemplate(),
 			'has_horizontal_menu' => count($areas) > 1,
 		    'areas' => $areas,
 		    'tabs_parms' => $this->getTabsParameters(),
 		    'tab_uid' => $tab_uid,
 		    'active_area_uid' => $active_area_uid,
 			'project_navigation_parms' => $this->getProjectNavigationParms($tab_uid),
        	'javascript_paths' => $script_service->getJSPaths(),
        	'hint' => !$this->needDisplayForm() && getFactory()->getObject('UserSettings')->getSettingsValue($page_uid) != 'off' ? $this->getHint() : '',
        	'page_uid' => $page_uid,
        	'module' => $this->getModule(),
        	'public_iid' => md5(INSTALLATION_UID.CUSTOMER_UID),
            'user_id' => getSession()->getUserIt()->getId()
 		);
 	}
 	
 	function getRenderView()
 	{
 		$plugins_paths = array();
		foreach( getFactory()->getPluginsManager()->getNamespaces() as $plugin )
		{
			$path = realpath(SERVER_ROOT_PATH.'plugins/'.$plugin->getNamespace().'/templates');
			if ( is_dir($path) ) $plugins_paths[] = $path.'/%name%';
		}
 		
 		return new PhpEngine(
 			new TemplateNameParser(), 
 			new FilesystemLoader(
 					array_merge(
 							$plugins_paths,
		 					array (
		 							SERVER_ROOT_PATH.'/templates/views/%name%',
		 							SERVER_ROOT_PATH.'/templates/views/core/%name%',
		 							SERVER_ROOT_PATH.'/plugins/%name%'
		 					)
 					)
 			), 
 			array (
				new SlotsHelper()
			)
		);
 	}
 	
 	function getRedirect()
 	{
 		return '';
 	}
 	
 	function render( $view = null )
 	{
		$render_parms = $this->getRenderParms();
 	    if ( !is_object($view) ) $view = $this->getRenderView();

 		if ( $_REQUEST['export'] != '' ) {
			$this->export();
			die();
		}

		if ( $_REQUEST['tableonly'] != '' && is_object($this->table) )
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header('Content-type: text/html; charset='.APP_ENCODING);
			
			// wait for changes of objects
		    if ( $_REQUEST['wait'] != '' ) 
		    {
				$object = $this->getObject();
                if ( !is_object($object) ) return;

		        $classes = $this->getWatchedObjects();
				$entityFilters = array (
					new FilterAttributePredicate('ObjectClass', $classes),
					new FilterVpdPredicate($object->getVpds()),
					new SortRecentClause()
				);
				$ids = array_filter(preg_split('/[\-,]/', $_REQUEST[strtolower(get_class($object))]), function( $value ) {
					return is_numeric($value) && $value >= 0;
				});
				if ( count($ids) > 0 ) {
					$entityFilters[] = new FilterAttributePredicate('ObjectId', $ids);
				}

				$filters = array_merge(
					$entityFilters,
					array (
						new FilterModifiedAfterPredicate(
							SystemDateTime::convertToClientTime(strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 seconds', strtotime(SystemDateTime::date()))))
						)
					)
				);

		        // wait for entity-level lock has been released or new modifications has appeared
				$waitSeconds = defined('PAGE_WAIT_SECONDS') ? PAGE_WAIT_SECONDS : 60;
				$affected = getFactory()->getObject('AffectedObjects');
		        $lock = new LockFileSystem(array_shift(array_values($classes)));
		        $lock->LockAndWait($waitSeconds, function() use ($affected, $filters)
		        {
		        	 getFactory()->resetCachedIterator($affected);
        	         return $affected->getRegistry()->Count($filters) > 0;
		        });
		        
		        getFactory()->resetCache();
		        
		        $ids = $this->getRecentChangedObjectIds($entityFilters);
		        if ( count($ids) < 1 ) $ids[] = 0;
		        
		        $_REQUEST['object'] = $_REQUEST[strtolower(get_class($object))] = join(',', $ids); 
		    }

		    $render_parms['tableonly'] = true;
		    $render_parms['changed_ids'] = $ids;

 			$this->table->render($view, $render_parms);

			// long living session shouldn't modify cache
			getFactory()->getCacheService()->setReadonly();

			exit();
		}

		if ( $_REQUEST['formonly'] != '' && is_object($this->form) )
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate, max-age=0, no-store, post-check=0, pre-check=0"); // HTTP/1.1
			header("Pragma: no-cache "); // HTTP/1.0
			header('Content-type: text/html; charset='.APP_ENCODING);

			if ( is_a($this->form, 'PMPageForm') )
			{
				$this->form->hasButtons( false );
				$this->form->showTitle( false );
			}

			$this->form->render( $view, array_merge( $render_parms, array (
			    'formonly' => true    
			)));

			die();
		}
		
		$redirect_url = $this->getRedirect();
		if ( $redirect_url != '' ) {
			if ( $_REQUEST['tour'] != '' && preg_match('/[a-zA-Z0-9]+/i', $_REQUEST['tour']) ) {
				setcookie($_REQUEST['tour'].'Skip', "1", mktime(0, 0, 0, 1, 1, date('Y') + 1), '/');
			}
			exit(header('Location: '.$redirect_url));
		}

		$render_parms = array_merge( $render_parms, $this->getFullPageRenderParms() );

 	 	if ( !$this->hasAccess() ) {
			if ( $_REQUEST['tour'] != '' && preg_match('/[a-zA-Z0-9]+/i', $_REQUEST['tour']) ) {
				setcookie($_REQUEST['tour'].'Skip', "1", mktime(0, 0, 0, 1, 1, date('Y') + 1), '/');
			}
		 	exit(header('Location: '.getSession()->getApplicationUrl()));
		}

    	if( $this->needDisplayForm() && is_object($this->form) )
        {
	 		$object_it = $this->getFormRef()->getObjectIt();
	 		if ( !is_object($object_it) || is_object($object_it) && $object_it->getId() != '' ) {
	 		    header('Content-type: text/html; charset='.APP_ENCODING);
				$this->getFormRef()->render($view, $render_parms);
    	 		return;
	 		}
			else {
				exit(header('Location: '.getSession()->getApplicationUrl()));
			}
        } 
        
     	if( is_object($this->table) ) 
        {
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate, max-age=0, no-store, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
			header("Pragma: no-cache "); // HTTP/1.0
	 		header('Content-type: text/html; charset='.APP_ENCODING);
	 		
	 		$this->table->render($view, $render_parms);
	 		
	 		return;
       	}
 	}
 	
 	function getMenus()
 	{
 		return array();
 	}

	function getHelpActions()
	{
		$support_url = defined('HELP_SUPPORT_URL') ? HELP_SUPPORT_URL : 'http://support.devprom.ru/issue/new';
		if ( $support_url == '' ) return array();
		return array(
			array(),
			array (
				'name' => text('guide.support'),
				'url' => $support_url,
				'target' => '_blank'
			)
		);
	}
 	
 	function getTabs()
 	{
 		return array();
 	}
 	
 	function getAreas()
 	{
 	    $areas['main'] = array(
            'name' => 'default',
            'uid' => 'main',
            'menus' => $this->getTabs()
        );
 	    
 	    return $areas;
 	}
 	
 	function getArea()
 	{
 	    return $_REQUEST['area'];
 	}
 	
 	function getModule()
 	{
 		return $this->module;
 	}
 	
 	function setModule( $uid )
 	{
 		$this->module = $uid;
 	}
 	
 	function getPageUid()
 	{
 		return get_class($this);
 	}

	function getPageUrl()
	{
		return str_replace(EnvironmentSettings::getServerUrl(), '', $_SERVER['REQUEST_URI']);
	}

	function getTabsTemplate()
	{
		return 'core/PageTabs.php'; 	
	}
	
	function getTabsParameters()
	{
	    return array();
	}
 	
 	function getCheckpointAlerts()
 	{
        $user_it = getSession()->getUserIt();
 		if ( $user_it->getId() < 1 || !$user_it->IsAdministrator() ) return array();

		$details = array();
		$urls = array();
		foreach( getCheckpointFactory()->getCheckpoint('CheckpointSystem')->getEntries() as $entry )
		{
			if ( $entry->enabled() && $entry->notificationRequired() && !$entry->check() )
			{
				$details[] = $entry->getTitle();
				$urls[] = $entry->getUrl();
			}
		}
		return array($details, array_pop($urls));
 	}

	function getWatchedObjects() {
		return array(
			get_class($this->getTableRef()->getObject())
		);
	}

 	function getRecentChangedObjectIds( $filters )
 	{
         return getFactory()->getObject('AffectedObjects')->getRegistry()->Query(
			array_merge(
				$filters,
				array (
					new FilterModifiedAfterPredicate(
						SystemDateTime::convertToClientTime(strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 seconds', strtotime(SystemDateTime::date()))))
					)
				)
			)
		 )->fieldToArray('ObjectId');
 	}
 	
 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');
		
		$resource_it = $resource->getExact($this->getModule());
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');

		return '';
	}
}