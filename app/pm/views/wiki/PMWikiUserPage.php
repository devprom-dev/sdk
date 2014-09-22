<?php

include SERVER_ROOT_PATH."pm/methods/c_wiki_methods_base.php";
include SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_wiki_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_stage_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_task_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include SERVER_ROOT_PATH."pm/methods/WikiRemoveStyleWebMethod.php";
include SERVER_ROOT_PATH."pm/classes/wiki/DocumentMetadataBuilder.php";

include "PMWikiTable.php";
include "PMWikiDocument.php";
include "WikiFilesTable.php";
include "WikiTreeSection.php";
include "WikiDocumentSettingBuilder.php";
include "WikiPageSettingBuilder.php";
include "history/WikiHistoryTable.php";
include "templates/WikiTemplateTable.php";
include "templates/WikiTemplateSettingBuilder.php";
include 'parsers/WikiIteratorExportExcelText.php';
include 'parsers/WikiIteratorExportExcelHtml.php';
include 'parsers/WikiIteratorExportHtml.php';
include 'parsers/WikiIteratorExportPdf.php';
include 'parsers/WikiIteratorExportRtf.php';
include 'parsers/WikiIteratorExportCHM.php';
include "import/ImportWikiPageFromExcelSection.php";
include "import/ImportExcelForm.php";

class PMWikiUserPage extends PMPage
{
 	function PMWikiUserPage()
 	{
 		parent::PMPage();
 		
 		getSession()->addBuilder( new WikiDocumentSettingBuilder() );
 		
 		getSession()->addBuilder( new WikiPageSettingBuilder() );
 		
	    $table = $this->getTableRef();
 		    
	    if ( is_a($table, 'PMWikiDocument') && $table->getDocumentIt()->getId() > 0 )
	    {
			$this->addInfoSection( new WikiTreeSection(
	            		$table->getObjectIt()->getId() > 0 
		            			? $table->getObjectIt() 
		            			: $table->getDocumentIt(),
	            		$_REQUEST['baseline']
			));
	    }
 		
 	 	if( $_REQUEST['view'] == 'import' )
 		{
 			$this->addInfoSection( new ImportWikiPageFromExcelSection($this->getObject()));
 		}
 	}
 	
 	function getObject()
 	{
 		return null;
 	}
 	
 	function getPredicates()
 	{
 		return array();
 	}
 	
 	function getStateObject()
 	{
 		return null;
 	}
 	
 	function getTemplateObject()
 	{
 		return null;
 	}
 	
 	function getTableBase()
 	{
 		return new PMWikiTable($this->getObject(), $this->getStateObject(), $this->getForm());
 	}
 	
 	function getDocumentTableBase( & $object )
 	{
 		return new PMWikiDocument($object, $this->getStateObject(), $this->getForm());
 	}
 	
 	function getTable()
 	{
 		switch( $_REQUEST['view'] )
 		{
 			case 'files':

 				$file = getFactory()->getObject('WikiPageFile');
 		
 				$file->setAttributeType('WikiPage', 'REF_'.get_class($this->getObject()).'Id');
 				
 				$file->addFilter( new WikiFileReferenceFilter($this->getObject()->getReferenceName()) );
 		
 				return new WikiFilesTable($file);
 				
 			case 'templates':
 				
 				getSession()->addBuilder( new WikiTemplateSettingBuilder() );
 				
 				return new WikiTemplateTable($this->getTemplateObject(), $this->getForm());
 			
 			case 'history':

 				return new WikiHistoryTable();

 			case 'docs':
 				
	 	        if ( $_REQUEST['document'] < 1 ) return $this->getTableBase(); 
 				
	 	        $object = $this->getObject();
	 	        
	 	        $object->resetSortClause();
	 	        
	 	        return $this->getDocumentTableBase( $object );
 				
 			default:
 				
 				return $this->getTableBase();
 		}
 	}
 	
 	function needDisplayForm()
 	{
 		return $_REQUEST['view'] == 'import' ? true : parent::needDisplayForm();
 	}
 	
 	function getForm() 
 	{
 		if ( $_REQUEST['view'] == 'import' ) return new ImportExcelForm($this->getObject());
 		
 		return parent::getForm();
 	}
 	

 	function export()
 	{
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['export'] )
 		{
 			case 'tree':
 				$this->exportWikiTree();
 				break;

 			default:
 				parent::export();
 		}
 	}
 	
    function exportWikiTree()
 	{
 		global $_REQUEST, $_COOKIE, $model_factory;

 		$uid = new ObjectUID;
 		
 		$open_path = array();
 		
 		$object = $this->getObject();
 		
		$object_it = $_REQUEST['root'] > 0 ? $object->getExact($_REQUEST['root']) : $object->getEmptyIterator();
		
		if ( $object_it->get('ParentPage') == '' )
		{
			$_REQUEST['root'] = '';

			$root_it = $object->getRootIt();
 			
	 		if ( is_object($root_it) && $root_it->getId() > 0 ) $object_it = $root_it;  
		}

 		if ( $_REQUEST['open'] > 0 )
 		{
 			$open_it = $object->getExact($_REQUEST['open']);
 			
 			$open_path = $open_it->getParentsArray();
 			
 			if ( $object_it->getId() < 1 ) $object_it = $open_it;
 		}
 		else
 		{
 			$open_path[] = $object_it->getId(); 
 		}

 		$json = array();

 		$snapshot_it = $_REQUEST['baseline'] != ''
 				? getFactory()->getObject('Snapshot')->getExact($_REQUEST['baseline'])
 				: getFactory()->getObject('Snapshot')->getEmptyIterator();
 		
 		if ( $snapshot_it->getId() > 0 )
 		{
 			$registry = new WikiPageRegistryVersion();
 			
 			$registry->setObject($object);
			$registry->setDocumentIt($object_it);
			$registry->setSnapshotIt($snapshot_it);
 			
 			$registry->setPersisters( array (
 					new SnapshotItemValuePersister($snapshot_it->getId()),
 					new WikiPageDetailsPersister()
 			));
 			
 			$this->getFormRef()->setReadonly();
 		}
 		else
 		{
 			$registry = $object->getRegistry();
 		}
 		
 		while( !$object_it->end() )
 		{
	 		$children_it = $registry->Query( 
	 				array_merge( 
	 						array(
					 				new FilterAttributePredicate('DocumentId', $object_it->getId()),
					 				new SortDocumentClause()
	 						), 
	 						$this->getPredicates() 
	 				)
	 		);

	 		while ( !$children_it->end() )
	 		{
	 			if ( $children_it->get('DocumentId') < 1 ) 
	 			{
	 				// not existed page (eg. there is no page in the snapshot) 
	 				$children_it->moveNext();
	 				
	 				continue;
	 			}
	 			
	 			$json[] = $this->exportWikiNodeJson($children_it, $open_path, count($children_it->getParentsArray()) );
	 			
	 			$children_it->moveNext();
	 		}
 			
 			$object_it->moveNext();
 		}
 		
 		$json = $this->buildTree($json, '');
 		
 		echo JsonWrapper::encode($json);
 	}
 	
 	private function buildTree( array &$elements, $parentId = '' ) 
 	{
       $branch = array();

       foreach ($elements as $element) {
           if ($element['parent'] == $parentId) {
               $children = $this->buildTree($elements, $element['id']);
               if ($children) {
                   $element['children'] = $children;
               }
               $branch[] = $element;
               unset($elements[$element['id']]);
           }
       }
       
       return $branch;
    }
 	 	
 	function exportWikiNodeJson( $object_it, $open_path, $level )
 	{
 		global $project_it;
 		
 		$uid = new ObjectUID;
 		
		$class_name = $object_it->getId() == array_pop($open_path) ? 'label' : '';
 		
		// display version (revision) number for the root only
		$caption = $object_it->get('ParentPage') == '' ? $object_it->getDisplayName() : $object_it->get('Caption');
		
		if ( $object_it->get('ParentPage') == '' && $object_it->get('Project') != $project_it->getId() )
		{
			$other_it = $object_it->getRef('Project');
			
			$caption = $other_it->getDisplayName().': '.$caption; 
		}

		$url = $uid->getGotoUrl($object_it);

 		if ( $object_it->get('TotalCount') > 0 )
		{
			if ( $object_it->get('ContentPresents') == 'Y' )
			{
				$image = 'folder_page';
			}
			else
			{
				$image = 'folder';
			}
		}
		else
		{
			$image = 'wiki_document';
		}
		
 		$title = '<div class="treeview-label '.$image.'" object-id="'.$object_it->getId().'">';

 		$title .= $this->getExportItemMenu( $caption, $object_it, $class_name );
 		
 		$title .= '</div>';
 			
 		return array (
 				'text' => IteratorBase::wintoutf8($title),
 				'expanded' => in_array($object_it->getId(), $open_path) || $level < 2,
 				'classes' => "folder ".$image,
 				'id' => $object_it->getId(),
 				'children' => array(),
 				'hasChildren' => false,
 				'parent' => $object_it->get('ParentPage')
 		);
 	}
 	
 	function getExportItemMenu( $title, $object_it, $class_name )
 	{
 		$form = $this->getFormRef();

 		$actions = $form->getTreeMenu($object_it);
 		
 		$view = $this->getRenderView();
 		
 		return $view->render('pm/WikiTreeMenu.php', array (
            'url' => $object_it->getViewUrl(),
            'title' => $title, 
            'items' => $actions,
            'class' => $class_name,
 		    'page_id' => $object_it->getId()
        ));
 	}
}