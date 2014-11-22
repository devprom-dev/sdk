<?php

 use Symfony\Component\Templating\PhpEngine;
 use Symfony\Component\Templating\TemplateNameParser;
 use Symfony\Component\Templating\Loader\FilesystemLoader;
 use Symfony\Component\Templating\Helper\SlotsHelper;
 use Devprom\CommonBundle\Service\Widget\ScriptService;
 
 $path = dirname(__FILE__);
 
 include($path.'/PageTable.php');
 include($path.'/PageTableStatic.php');
 include($path.'/PageList.php');
 include($path.'/PageListStatic.php');
 include($path.'/PageBoard.php');
 include($path.'/PageChart.php');
 include($path.'/PageForm.php');
 include($path.'/PageMenu.php');
 include($path.'/PageInfoSection.php');
 include($path.'/PageSectionLastChanges.php');
 include "FullScreenSection.php";

 if ( !class_exists('html2text') ) include( SERVER_ROOT_PATH.'ext/html/html2text.php' );
 
 include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
 include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportHtml.php';
 include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportJSON.php';
 include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';
 include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

 include SERVER_ROOT_PATH.'core/methods/ObjectModifyWebMethod.php';
 
 class Page
 {
 	var $infosections;
 	var $table;
 	var $form;
 	var $notfound;
 	var $injections;
 	private $module = '';
 	
 	private $render_parms = array();
 	
 	function Page() 
 	{
 		global $model_factory, $plugins, $_REQUEST, $_SERVER;

 		// initialize visual items
 		$module = $model_factory->getObject('Module');
 		
 		$module_it = $module->getAll();
 		
 	    $this->form = $this->getForm();
 		
 		if ( is_object($this->form) && is_a($this->form, 'PageForm') )
		{
		    $this->form->setPage( $this );
		}

		if ( is_a($this->form, 'MetaobjectForm') )
		{
		    $decode_parms = $_REQUEST['formonly'] != '' && EnvironmentSettings::getBrowserPostUnicode();
		    
		    if ( $decode_parms && in_array($this->form->getAction(), array('add','modify')) )
		    {
		        array_walk($_REQUEST, function(&$item, $key) 
		        {
		            if ( !is_array($item) )
		            {
		                $item = IteratorBase::utf8towin( $item );
		            }
		        });
		    }
		    
		    $this->form->process();
		}
		
		$this->table = $this->getTable();
		
		if ( is_object($this->table) && is_a($this->table, 'PageTable') )
		{
		    $this->table->setPage( $this );
		}
 		
		$this->notfound = false;
		$this->infosections = array();
		
		if ( is_object($plugins) )
		{
 			$this->infosections = $plugins->getPageInfoSections( $this );
		}
 	}
 	
 	function addInfoSection( $infosection_object ) 
 	{
 		$infosection_object->setPage( $this );
		
 		$this->infosections[strtolower(get_class($infosection_object))] = $infosection_object;
 	}
 	
 	function & getInfoSections()
 	{
 		return $this->infosections;
 	}
 	
 	function getTable() 
 	{
 		return null;
 	}
 	
 	function & getTableRef()
 	{
 		return $this->table;
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function & getFormRef()
 	{
 		return $this->form;
 	}

 	function needDisplayForm() 
 	{
 		global $_REQUEST;
 		return $_REQUEST['entity'] != '' || $_REQUEST['action_mode'] == 'form';
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
 		global $_REQUEST;
 		
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
 	
 		$table =& $this->getTableRef();
 		
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
			$it =& $table->getListIterator();
			
			$it->moveFirst();
 		}
 		else
 		{
 			$object = $_REQUEST['entity'] == '' 
 					? $table->getObject() : $model_factory->getObject($_REQUEST['entity']);

 			if ( is_object($table) && is_a($table, 'PageTable') )
 			{
 				$list =& $table->getListRef();
 				
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
				
			$list =& $table->getListRef();
			
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
			
			if ( $_REQUEST['caption'] == '' ) $_REQUEST['caption'] = IteratorBase::wintoutf8($table->getCaption());
		}

		$eit = new $_REQUEST['class']( $it );
			
		$eit->setTable($table);
		
		$eit->setFields( $fields );

		$eit->setName( IteratorBase::utf8towin($_REQUEST['caption']) );

		$eit->export();
			
		return true;
 	}
 	
 	function exportSection()
 	{
 	 	global $model_factory, $_REQUEST;
 	 	
 		if ( $_REQUEST['class'] == '' ) return false;
 		if ( $_REQUEST['section'] == '' ) return false;
 		if ( !class_exists($_REQUEST['section'], false) ) return false;
 		
 		$object = $model_factory->getObject($_REQUEST['class']);
 		
 		$ids = preg_split('/,/', $_REQUEST['id']);
 		
 		$ids = array_filter($ids, function( $val ) {
 		    return is_numeric($val);
 		});
 		
 		$object_it = count($ids) > 0 ? $object->getExact($ids) : $object->getEmptyIterator();

		$section = new $_REQUEST['section'](
		        is_object($object_it) && $object_it->getId() > 0 ? $object_it : $object
		);
		
		$this->addInfoSection( $section );
 		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset=windows-1251');
  	
		$section->render( $this->getRenderView(), $this->getRenderParms() );
 	 	
 	 	return true;
 	}
 	
 	function getApplicationUrl()
 	{
 	    return _getServerUrl();
 	}
 	
 	function getNavigationContext( & $areas, $active_url )
 	{
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
          				new FilterInPredicate(preg_split('/,/', $program_it->get('LinkedProject')))
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

		    if ( $portfolio_it->get('CodeName') != 'all' )
		    {
		        $linked_it = $portfolio_it->get('LinkedProject') != '' 
		                ? getFactory()->getObject('Project')->getRegistry()->Query(
		                		array (
		                				new ProjectStatePredicate('active'),
		                				new FilterInPredicate(preg_split('/,/', $portfolio_it->get('LinkedProject')))
		                		)
		                  )
		                : $model_factory->getObject('Project')->getEmptyIterator();
		        
		        while ( !$linked_it->end() )
		        {
		        	if ( $portfolio_it->getId() == $linked_it->getId() ) 
		        	{
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
		
		foreach( $projects as $key => $dummy )
		{
		    uasort($projects[$key], function( $left, $right ) {
		        return $left['name'] > $right['name'] ? 1 : -1;
		    });
		}
		
		if ( count($programs) > 0 )
		{
		    foreach( $programs as $program_id => $program )
		    {
		    	if ( !is_array($projects[$program_id]) ) continue;
		    	
		        foreach( $projects[$program_id] as $project_id => $project )
		        {
		            unset( $projects['my'][$project_id] );
		        }
		    }
		}

		foreach( $portfolios as $portfolio_id => $portfolio )
		{
		    if ( $portfolio_id == 'my' ) continue;
		    
		    if ( !is_array($projects[$portfolio_id]) ) continue;
		    
		    foreach( $projects[$portfolio_id] as $project_id => $project )
		    {
		        unset( $projects['my'][$project_id] );
		    }
		}
	
		$company_actions = array();
		
		if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) )
		{
			$skip_welcome = getFactory()->getObject('UserSettings')->getSettingsValue('projects-welcome-page');
			
			$company_actions[] = array (
					'icon' => 'icon-plus',
					'url' =>  $skip_welcome != 'off' && !defined('SKIP_WELCOME_PAGE')
									? '/projects/welcome'
									: '/projects/new',
					'name' => translate('������� ������')
			);
		}
		
		return array (
				'programs' => $programs,
				'portfolios' => $portfolios,
				'projects' => $projects,
				'company_actions' => $company_actions
		);
 	}
 	
 	function getRenderParms()
 	{
 		if ( count($this->render_parms) > 0 ) return $this->render_parms;
 		 
 		$this->render_parms = array(
 			'current_version' => $_SERVER['APP_VERSION'],
 			'object_class' => get_class($this->getObject()),
 			'object_id' => is_object($this->getObjectIt()) ? $this->getObjectIt()->getId() : '',
            'license_name' => getFactory()->getObject('LicenseState')->getAll()->getDisplayName(),
            'language_code' => strtolower(getLanguage()->getLanguage()),
 		    'datelanguage' => getLanguage()->getLocaleFormatter()->getDatepickerLanguage(),
            'dateformat' => getLanguage()->getDatepickerFormat(),
 		    'company_name' => getFactory()->getObject('cms_SystemSettings')->getAll()->get('Caption'),
 		    'application_url' => $this->getApplicationUrl(),
 		    'display_form' => $this->needDisplayForm()
 		);
 		
 		return $this->render_parms;
 	}
 	
 	function getFullPageRenderParms()
 	{
		$sections = array();
		
		$infos = $this->getInfoSections();
		
		if ( is_array($infos) )
		{
			foreach ( $infos as $section ) 
			{
				if ( !$section->isActive() ) continue;
			    			
				$sections[$section->getId()] = $section;
			}
		}
		
     	$bottom_sections = array();
    
        foreach( $sections as $key => $section )
        { 
            if ( is_a($section, 'PageSectionComments') )
            {
                $bottom_sections[$section->getId()] = $section;
                
                unset($sections[$key]);
            }
        }
		
        // get active functional area
        
        $areas = $this->getAreas();
 
        $parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);
        
        $active_url = str_replace(getSession()->getApplicationUrl(), '', $parts[0]);
   
        foreach( $areas as $key => $area )
        {
    		if ( $this->getArea() != '' && $area['uid'] == $this->getArea() )
            {
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
                        
        $tab_url = $context['item']['url'];

        if ( $context['item']['module'] != '' ) $this->setModule($context['item']['module']);

        $first_menu = count($areas) > 0 ? array_pop(array_values($areas)) : array();

        $script_service = new ScriptService();
        
        $page_uid = get_class($this);
        
        return array(
 			'inside' => count($first_menu['menus']) > 0,
 			'title' => $this->getTitle() != '' ? $this->getTitle() : $tab_title,
 		    'navigation_title' => $tab_title != '' ? $tab_title : $this->getTitle(),
 		    'navigation_url' => $tab_url,
 			'b_checkpoint_alert' => $this->getCheckpointAlert(),
 			'b_display_sections' => $percent > 0 && !$this->notfound,
 			'percent' => $percent,
 			'menu_template' => $this->getMenuTemplate(),
 			'menus' => $this->getMenus(),
 			'tabs_template' => $this->getTabsTemplate(),
 			'has_horizontal_menu' => count($areas) > 1,
 		    'areas' => $areas,
 		    'tabs_parms' => $this->getTabsParameters(),
 			'sections' => $sections,
 		    'tab_uid' => $tab_uid,
 		    'active_area_uid' => $active_area_uid,
 		    'bottom_sections' => $bottom_sections,
 			'project_navigation_parms' => $this->getProjectNavigationParms($tab_uid),
        	'javascript_paths' => $script_service->getJSPaths(),
        	'hint' => !$this->needDisplayForm() && getFactory()->getObject('UserSettings')->getSettingsValue($page_uid) != 'off' ? $this->getHint() : '',
        	'page_uid' => $page_uid
 		);
 	}
 	
 	function getRenderView()
 	{
 		return new PhpEngine(
 			new TemplateNameParser(), 
 			new FilesystemLoader(SERVER_ROOT_PATH.'/templates/views/%name%'), 
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
 	    global $model_factory;
 	    
 	    $render_parms = $this->getRenderParms();
 	    
 		if ( $_REQUEST['export'] != '' )
		{
			$this->export();
			
			die();
		}

 	    if ( !is_object($view) ) $view = $this->getRenderView();
		
		if ( $_REQUEST['tableonly'] != '' && is_object($this->table) )
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header('Content-type: text/html; charset=windows-1251');
			
			// wait for changes of objects
		    if ( $_REQUEST['wait'] != '' ) 
		    {
				// long living session shouldn't modify cache
				getFactory()->getCacheService()->setReadonly();
		    	
		        $object = $_REQUEST['class'] != '' ? $model_factory->getObject($_REQUEST['class']) : $this->table->getObject();

		        $lock = new LockFileSystem(get_class($object));
		        
		        $lock->LockAndWait(180);
		        
		        getFactory()->resetCache();
		        
		        $ids = $this->getRecentChangedObjectIds( $this->table );

		        if ( count($ids) < 1 ) $ids[] = 0;
		        
		        $_REQUEST['object'] = $_REQUEST[strtolower(get_class($object))] = join(',', $ids); 
		    }
		    
			$this->table->render( $view, array_merge($render_parms , array (
			    'tableonly' => true,
			    'changed_ids' => $ids
			)));
			
			die();
		}

		if ( $_REQUEST['formonly'] != '' && is_object($this->form) )
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header('Content-type: text/html; charset=windows-1251');

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
		
		if ( $redirect_url != '' )
		{
			exit(header('Location: '.$redirect_url));
		}
		
		$render_parms = array_merge( $render_parms, $this->getFullPageRenderParms() );

 	 	if ( !$this->hasAccess() )
		{
		 	exit(header('Location: '.getSession()->getApplicationUrl()));
		}
		
		$display_form = $this->needDisplayForm();

    	if( $display_form && is_object($this->form) ) 
        {
	 		$form = $this->getFormRef();
	
	 		$object_it = $form->getObjectIt();
	 		
	 		if ( !is_object($object_it) || is_object($object_it) && $object_it->getId() != '' )
	 		{
	 		    header('Content-type: text/html; charset=windows-1251');
	 		    			
    	 		$form->render($view, $render_parms);
    	 		
    	 		return;
	 		}
        } 
        
     	if( is_object($this->table) ) 
        {
	 		$table = $this->getTableRef();

	 		header('Content-type: text/html; charset=windows-1251');
	 		
	 		$table->render($view, $render_parms);
	 		
	 		return;
       	}
 	}
 	
 	function getMenus()
 	{
 		return array();
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
 		
 	function getMenuTemplate()
 	{
 		return 'core/PageMenu.php';
 	}
 	
	function getTabsTemplate()
	{
		return 'core/PageTabs.php'; 	
	}
	
	function getTabsParameters()
	{
	    return array();
	}
 	
 	function getCheckpointAlert()
 	{
        $user_it = getSession()->getUserIt();
 		
 		if ( $user_it->getId() < 1 ) return false;
 		
		return $user_it->IsAdministrator() && !getCheckpointFactory()->getCheckpoint( 'CheckpointSystem' )->check();
 	}
 	
 	function getRecentChangedObjectIds( $table )
 	{
 		 $from_date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 seconds', strtotime(SystemDateTime::date())));
 		
         $ids = getFactory()->getObject('AffectedObjects')->getRegistry()->Query(
         		array (
         				new FilterAttributePredicate('ObjectClass', get_class($table->getObject())),
         				new FilterModifiedAfterPredicate($from_date),
         				new FilterVpdPredicate($table->getObject()->getVpds()),
         				new SortRecentClause()
         		)
         )->fieldToArray('ObjectId');

         $mapper = new ModelDataTypeMappingDate();
 	    
		 DAL::Instance()->Query( 
		 		" DELETE FROM co_AffectedObjects WHERE RecordModified <= '".
		 				$mapper->map(
		 						strftime('%Y-%m-%d %H:%M:%S', strtotime('-25 seconds', strtotime(SystemDateTime::date())))
         				)."' "
         );
		 
		 return $ids;
 	}
 	
 	function getHint()
	{
		return '';
	}
}