<?php

use Symfony\Component\Templating\PhpEngine;
 use Symfony\Component\Templating\TemplateNameParser;
 use Symfony\Component\Templating\Loader\FilesystemLoader;
 use Symfony\Component\Templating\Helper\SlotsHelper;
 use Devprom\CommonBundle\Service\Widget\ScriptService;

include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportHtml.php';
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportXml.php';
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
include "PageNavigation.php";
 
class Page
{
 	var $infosections = array();
 	var $table;
 	var $form;
 	var $notfound;
 	var $injections;
 	private $module = '';
    private $navigation_parms = null;
 	
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
                \FeatureTouch::Instance()->touch(strtolower(get_class($this->form)));
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
 		$table = $this->getTableRef();
        $table->getRenderParms(array());
 		
		if ( $_REQUEST['class'] == '' ) {
		    throw new Exception('Required parameter is missed: "class" should be given');
		}
		    
		if( !class_exists($_REQUEST['class']) || !is_subclass_of($_REQUEST['class'], 'IteratorExport') ) {
			throw new Exception('Given iterator "'.$_REQUEST['class'].'" cant be instantiated');
		}

        $_REQUEST['rows'] = 'all';
        $object = $_REQUEST['entity'] == ''
            ? $table->getObject() : getFactory()->getObject($_REQUEST['entity']);

		if ( $_REQUEST['objects'] == '' ) {
            $it = $table->getListIterator();
			$it->moveFirst();
 		}
        elseif ( $_REQUEST['objects'] == '0' ) {
            $it = $this->getDemoDataIt($object);
        }
 		else {
 			if ( is_object($table) && is_a($table, 'PageTable') )
 			{
                $table->getListIterator();
                $list = $table->getListRef();

                $sorts = $list->getSorts();
                foreach ( $sorts as $sort ) {
                    $object->addSort( $sort );
                }
 			}
			$it = $this->buildExportIterator(
			    $object,
                preg_split('/-/', trim($_REQUEST['objects'], '-')),
                $_REQUEST['class']
            );
 		}

		if ( !is_object($it) ) return false;

		$fields = array();
			
		if ( is_a( $table, 'PageTable' ) )
		{
			$list = $table->getListRef();
			if ( is_object($list) && !$list instanceof \PageChart )
			{
    			$list->setupColumns();
    				
    			$columns = $list->getColumnsRef();
                if ( $object instanceof MetaobjectStatable ) {
                    $columns[] = 'State';
                }

                if ( array_key_exists('prepare-import', $_REQUEST) ) {
                    $skip_fields = array_merge(
                        $object->getAttributesByGroup('trace'),
                        $object->getAttributesByGroup('system'),
                        $object->getAttributesByGroup('dates'),
                        $object->getAttributesByGroup('astronomic-time'),
                        $object->getAttributesByGroup('working-time'),
                        array(
                            'Project',
                            'UID',
                            'RecordCreated',
                            'RecordModified'
                        )
                    );
                    foreach( array_keys($object->getAttributes()) as $attribute ) {
                        if ( $object->IsAttributeStored($attribute) ) continue;
                        $skip_fields[] = $attribute;
                    }
                }
                else {
                    $skip_fields = array();
                }

    			foreach( $columns as $column )
    			{
    				if ( $column != 'Content' && !$list->getColumnVisibility($column) && $_REQUEST['show'] != 'all' ) continue;
					if ( trim($column) == '' ) continue;
					if ( in_array($column, $skip_fields) ) continue;
    
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
        $eit->setOptions(preg_split('/-/', $_REQUEST['options']));
		$eit->setTable($table);
		$eit->setFields($fields);
		$eit->setName($_REQUEST['caption']);
		$eit->export();
			
		return true;
 	}

 	function buildExportIterator( $object, $ids, $iteratorClassName )
    {
        $ids = array_filter($ids, function($value) {
            return $value != '';
        });
        if ( count($ids) < 1 ) $ids = array(0);
        return $object->getExact($ids);
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
                    if ( $item['url'] == '' ) {
                        unset($areas[$key]['menus'][$tab_key]['items'][$item_key]);
                        continue;
                    }
                    
                    $parts = preg_split('/\?/', str_replace(getSession()->getApplicationUrl(), '', $item['url']));
        
                    if ( trim($parts[0],'/') == trim($active_url,'/') && $active_area_uid == '' ) {
                        $active_area_uid = $area['uid'];
                        $tab_uid = $area['uid'].'/'.$tab['uid'].'/'.$item['uid'];
                        $tab_item = $item;
                    }
                }
            }
        }

        if ( !is_array($tab_item) )
        {
        	$active_area_uid = 'favs';
        	$tab_item['url'] = getSession()->getApplicationUrl().$active_url;
        }

        return array(
                'area_uid' => $active_area_uid,
                'item_path' => $tab_uid,
                'item' => $tab_item
        );
 	}

 	protected function buildNavigationParms() {
        return new PageNavigation($this);
    }

 	function getNavigationParms()
    {
        if ( is_array($this->navigation_parms) ) return $this->navigation_parms;

        $cacheId = 'page-navigation-'.getSession()->getId();
        $navigation = getFactory()->getCacheService()->get($cacheId, 'sessions');
        if ( !is_object($navigation) ) {
            $navigation = $this->buildNavigationParms();
            if ( count($navigation->getParms()) > 0 ) {
                getFactory()->getCacheService()->set($cacheId, $navigation, 'sessions');
            }
        }

        $projectSortData = array();
        $data_it = getFactory()->getObject('pm_Participant')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
                new SortRecentModifiedClause(),
                new SortProjectCaptionClause(),
                new EntityProjectPersister()
            )
        );
        while( !$data_it->end() ) {
            $projectSortData[] = $data_it->get('ProjectCodeName');
            $data_it->moveNext();
        }

        return $this->navigation_parms =
            array_merge(
                $navigation->getParms(),
                array (
                    'projectSortData' => array_flip($projectSortData)
                )
            );
    }

 	function getRenderParms()
 	{
 		if ( count($this->render_parms) > 0 ) return $this->render_parms;
 		 
 		$sections = array();
		$infos = $this->getInfoSections();

		if ( is_array($infos) )	{
			foreach ( $infos as $section ) {
			    if ( !$section->hasAccess() ) continue;
				if ( $section instanceof PageSectionAttributes ) {
					if ( $_REQUEST['formonly'] == '' ) continue;
				}
				$sections[$section->getId()] = $section;
			}
		}

     	$bottom_sections = array();
		$last_sections = array();
        foreach( $sections as $key => $section ) { 
            if ( $_REQUEST['formonly'] == '' && $section->getPlacement() == 'bottom' ) {
				if ( $section instanceof PageSectionComments ) {
					$bottom_sections = array_merge($bottom_sections, array($section->getId() => $section));
				}
				else {
					$bottom_sections[$section->getId()] = $section;
				}
                unset($sections[$key]);
            }
        }
		$bottom_sections = array_merge($bottom_sections, $last_sections);

        if ( $_REQUEST['formonly'] == '' ) {
            $active_url = str_replace(getSession()->getApplicationUrl(), '', array_shift(preg_split('/\?/', $this->getPageUrl())));
            $tab_url = getSession()->getApplicationUrl().$active_url;
        }
        $module_it = getFactory()->getObject('Module')->getByRef('Url', $tab_url);
        $this->setModule($module_it->getId());

 		$this->render_parms = array(
 			'current_version' => $_SERVER['APP_VERSION'],
 			'object_class' => get_class($this->getObject()),
 			'object_id' => is_object($this->getObjectIt()) ? $this->getObjectIt()->getId() : '',
            'license_name' => $_SERVER['LICENSE'],
            'language_code' => strtolower(getSession()->getLanguageUid()),
 		    'datelanguage' => getLanguage()->getLocaleFormatter()->getDatepickerLanguage(),
            'dateformat' => getLanguage()->getDatepickerFormat(),
			'datejsformat' => getLanguage()->getLocaleFormatter()->getDateJSFormat(),
 		    'application_url' => $this->getApplicationUrl(),
 		    'display_form' => $this->needDisplayForm(),
 			'sections' => $sections,
        	'bottom_sections' => $bottom_sections,
            'module' => $this->getModule(),
            'uid' => $this->getModule()
        );
 		
 		return $this->render_parms;
 	}
 	
 	function getFullPageRenderParms()
 	{
        // get active functional area
        $navigation_parms = $this->getNavigationParms();
        $areas = $navigation_parms['areas'];

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

        $active_url = str_replace(getSession()->getApplicationUrl(), '', array_shift(preg_split('/\?/', $this->getPageUrl())));
        $context = $this->getNavigationContext( $areas, $active_url );

        $active_area_uid = $active_area_uid != '' && array_key_exists($active_area_uid, $areas)
            ? $active_area_uid : ($context['area_uid'] != '' && array_key_exists($context['area_uid'], $areas) 
                    ? $context['area_uid'] : array_shift(array_keys($areas)));
         
        $tab_uid = $context['item_path'];
        getSession()->setActiveTab( $tab_uid );

        $tab_title = $context['item']['title'] != '' ? $context['item']['title'] : $context['item']['name'];
        $tab_url = $context['item']['uid'] != '' ? $context['item']['url'] : '';
        if ( $context['item']['url'] != '' ) {
            $active_url = array_shift(preg_split('/\?/',$context['item']['url']));
        }

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
 			'tabs_template' => $this->getTabsTemplate(),
 			'has_horizontal_menu' => count($areas) > 1,
 		    'areas' => $areas,
 		    'active_area_uid' => $active_area_uid,
        	'javascript_paths' => $script_service->getJSPaths(),
        	'hint' => $this->getHint(),
            'hint_open' => !$this->needDisplayForm() && getFactory()->getObject('UserSettings')->getSettingsValue($page_uid) != 'off',
        	'page_uid' => $page_uid,
        	'public_iid' => md5(INSTALLATION_UID.CUSTOMER_UID),
            'user_id' => getSession()->getUserIt()->getId(),
            'navigation_parms' => $navigation_parms
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
 	
 	function getRedirect( $renderParms )
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
            header('X-Devprom-UI: tableonly');

			// wait for changes of objects
		    if ( $_REQUEST['wait'] != '' ) 
		    {
                $object = $this->getObject();
                if ( !is_object($object) ) return;

		        $classes = $this->getWatchedObjects();
				$entityFilters = $this->getWaitFilters($classes);

				$filters = array_merge(
					$entityFilters,
					array (
						new FilterModifiedSinceSecondsPredicate(5)
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

		        if ( connection_aborted() ) exit();
                time_nanosleep(0, 500000000);

                getFactory()->resetCachedIterator($affected);
		        $ids = $this->getRecentChangedObjectIds($filters);
		        if ( count($ids) < 1 ) $ids[] = 0;
		        
		        $_REQUEST['object'] = $_REQUEST[strtolower(get_class($object))] = join(',', $ids); 
		    }

		    $render_parms['tableonly'] = true;
		    $render_parms['changed_ids'] = $ids;

            getFactory()->resetCache();
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

        $render_parms = array_merge( $render_parms, $this->getFullPageRenderParms() );

		$redirect_url = $this->getRedirect($render_parms);
		if ( $redirect_url != '' ) {
			if ( $_REQUEST['tour'] != '' && preg_match('/[a-zA-Z0-9]+/i', $_REQUEST['tour']) ) {
				setcookie($_REQUEST['tour'].'Skip', "1", mktime(0, 0, 0, 1, 1, date('Y') + 1), '/');
			}
			exit(header('Location: '.$redirect_url));
		}

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
	
 	function getCheckpointAlerts()
 	{
        $user_it = getSession()->getUserIt();
 		if ( $user_it->getId() < 1 || !$user_it->IsAdministrator() ) return array();

		$details = array();
		$urls = array();
		foreach( getCheckpointFactory()->getCheckpoint('CheckpointSystem')->getEntries() as $entry ) {
			if ( $entry->enabled() && $entry->notificationRequired() && !$entry->check() ) {
				$details[] = $entry->getWarning();
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

	function getWaitFilters( $classes )
    {
        $entityFilters = array (
            new FilterAttributePredicate('ObjectClass', $classes),
            new FilterVpdPredicate($this->getObject()->getVpds()),
            new SortRecentClause()
        );

        $ids = TextUtils::parseIds($_REQUEST[strtolower(get_class($this->getObject()))]);
        if ( count($ids) > 0 ) {
            $entityFilters[] = new FilterAttributePredicate('ObjectId', $ids);
        }
        return $entityFilters;
    }

 	function getRecentChangedObjectIds( $filters )
 	{
         return getFactory()->getObject('AffectedObjects')->getRegistry()->Query($filters)->fieldToArray('ObjectId');
 	}
 	
 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');
		
		$resource_it = $resource->getExact($this->getModule());
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');

		return '';
	}

	function getDemoDataIt( $object ) {
        return $object->getEmptyIterator();
    }
}