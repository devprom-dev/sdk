<?php

include SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_wiki_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_stage_methods.php";
include SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_task_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/CloneWikiPageWebMethod.php";
include SERVER_ROOT_PATH."pm/methods/WikiRemoveStyleWebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";

include "PMWikiTable.php";
include "PMWikiDocument.php";
include "WikiTreeSectionNew.php";
include "DocumentStructureSection.php";
include "WikiDocumentSettingBuilder.php";
include "WikiPageSettingBuilder.php";
include "history/WikiHistoryTable.php";
include "history/WikiVersionTable.php";
include 'parsers/WikiIteratorExportExcelText.php';
include 'parsers/WikiIteratorExportExcelHtml.php';
include "import/ImportExcelForm.php";
include "import/ImportDocumentForm.php";
include "WikiIncludeForm.php";
include "WikiChangeForm.php";
include "templates/DocumentTemplateTable.php";
include "templates/DocumentTemplateForm.php";

class PMWikiUserPage extends PMPage
{
 	function PMWikiUserPage()
 	{
 		parent::PMPage();

        getSession()->addBuilder( new WikiDocumentSettingBuilder($this->getObject()) );
        getSession()->addBuilder( new WikiPageSettingBuilder() );

	    $table = $this->getTableRef();
 		    
	    if ( $table instanceof PMWikiDocument && $table->getDocumentIt()->getId() > 0 )
	    {
			if ( $_COOKIE['toggle-structure-panel-' . $table->getDocumentIt()->getId()] != 'false' ) {
				$this->addInfoSection( new WikiTreeSectionNew(
					$table->getObjectIt()->getId() > 0
						? $table->getObjectIt()
						: $table->getDocumentIt(),
					$_REQUEST['baseline']
				));
			}
	    }
		else {
			if( $_REQUEST['view'] == 'importdoc' )
            {
            }
			elseif ( $this->needDisplayForm() )
			{
				if (  $this->getFormRef() instanceof PMWikiForm ) {
					$this->addInfoSection(
						new PageSectionAttributes($this->getObject(), 'additional', translate('Дополнительно'))
					);
					$this->addInfoSection(
						new PageSectionAttributes(
							$this->getObject(),
							array('trace','source-attribute'),
							translate('Трассировки')
						)
					);
					$object_it = $this->getObjectIt();
					if (is_object($object_it) && $object_it->getId() > 0) {
						$this->addInfoSection(new PageSectionComments($object_it));
                        if ( $object_it->object->getStateClassName() != '' ) {
                            $this->addInfoSection(new StatableLifecycleSection($object_it));
                        }
						$this->addInfoSection(new PMLastChangesSection ($object_it));
                        $this->addInfoSection(new NetworkSection($object_it));
					}
				}
			}
			else if ( in_array($_REQUEST['view'],array('docs','list')) ) {
				$this->addInfoSection(new DetailsInfoSection());
			}
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
			case 'doctemplates':
				return new DocumentTemplateTable(new DocumentTemplate($this->getObject()));

 			case 'history':
 				return new WikiHistoryTable($this->getObject());

            case 'compare':
                return new WikiVersionTable($this->getObject());

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
 		return in_array($_REQUEST['view'], array('import','importdoc')) ? true : parent::needDisplayForm();
 	}
 	
 	function getForm() 
 	{
 	    switch( $_REQUEST['view'] ) {
            case 'doctemplates':
                return new DocumentTemplateForm(new DocumentTemplate($this->getObject()));
            case 'import':
                return new ImportExcelForm($this->getObject());
            case 'importdoc':
                return new ImportDocumentForm($this->getObject());
            default:
                if ( $_REQUEST['revision'] != '' ) return new WikiChangeForm($this->getObject());
                if ( $_REQUEST['Include'] != '' ) return new WikiIncludeForm($this->getObject());
        }
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

 	function exportForm( $object_it ) {
 	    Page::export();
    }

	function getExportPersisters() {
		return array (
			new WikiPageDetailsPersister(),
            new EntityProjectPersister()
		);
	}
 	
    function exportWikiTree()
 	{
		$object = $this->getObject();
		$object->setPersisters($this->getExportPersisters());

        $rootIds = array_filter(
            preg_split('/[,-]/',$_REQUEST['root']),
            function($value) {
                return $value > 0;
            }
        );
        $treeMode = $_REQUEST['tree-mode'] != 'plain';

        $object_it = count($rootIds) > 0
            ? $object->getExact($rootIds)
            : $object->getEmptyIterator();

		$open_path = array();
 		if ( $_REQUEST['open'] > 0 ) {
 			$open_it = $object->getExact($_REQUEST['open']);
 			$open_path = $open_it->getParentsArray();
 			if ( $object_it->getId() < 1 ) $object_it = $open_it;
 		}
 		else {
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

 		if ( $treeMode ) {
            while( !$object_it->end() ) {
                $children_it = $registry->Query(
                    array_merge(
                        array(
                            new FilterAttributePredicate('DocumentId', $object_it->getId()),
                            new SortDocumentClause()
                        ),
                        $this->getPredicates()
                    )
                );
                while ( !$children_it->end() ) {
                    if ( $children_it->get('DocumentId') < 1 ) {
                        // not existed page (eg. there is no page in the snapshot)
                        $children_it->moveNext();
                        continue;
                    }
                    $json[] = $this->exportWikiNodeNew($children_it, $open_path, count($children_it->getParentsArray()) );
                    $children_it->moveNext();
                }
                $object_it->moveNext();
            }
            $json = $this->buildTree($json, '');
        }
        else {
            $children_it = $registry->Query(
                array_merge(
                    array(
                        new FilterInPredicate($object_it->idsToArray()),
                        new SortDocumentClause()
                    ),
                    $this->getPredicates()
                )
            );
            while ( !$children_it->end() ) {
                $json[] = $this->exportWikiNodeNew($children_it, array(), 1);
                $children_it->moveNext();
            }
        }

 		echo JsonWrapper::encode($json);
 	}
 	
 	private function buildTree( array &$elements, $parentId = '' ) 
 	{
       $branch = array();

       foreach ($elements as $key => $element) {
           if ($element['parent'] == $parentId) {
               $children = $this->buildTree($elements, $element['key']);
               if (count($children)>0) {
                   $element['children'] = $children;
               }
               $branch[] = $element;
           }
       }
       
       return $branch;
    }

    function exportWikiNodeNew( $object_it, $open_path, $level )
    {
        // display version (revision) number for the root only
        $caption = $object_it->get('ParentPage') == '' ? $object_it->getDisplayName() : $object_it->getTreeDisplayName();

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

        return array (
            'title' => $caption,
            'active' => $object_it->getId() == array_pop($open_path),
            'folder' => $object_it->get('TotalCount') > 0,
            'key' => $object_it->getId(),
            'expanded' => in_array($object_it->getId(), $open_path) || $level < 2,
            'extraClasses' => $image,
            'children' => array(),
            'parent' => $object_it->get('ParentPage'),
            'data' => array(
                'project' => $object_it->get('ProjectCodeName')
            )
        );
    }

	function getPageUid()
	{
		switch ( $_REQUEST['view'] )
		{
			case 'docs':
				if ( $_REQUEST['document'] > 0 ) return 'doc-mode';
				break;
		}
		return parent::getPageUid();
	}

	function getHint()
 	{
	    switch ( $_REQUEST['view'] )
	    {
	        case 'docs':
	        	if ( $_REQUEST['document'] > 0 ) return text(2090);
	        	break;
	    }
	   	return parent::getHint();
 	}

    function buildExportIterator( $object, $ids )
    {
        if ( !$object instanceof WikiPage ) {
            return parent::buildExportIterator($object, $ids);
        }
        $exportOptions = preg_split('/-/', $_REQUEST['options']);
        return $object->getRegistry()->Query(
            array_merge(
                array(
                    $_REQUEST['options'] == '' || in_array('children', $exportOptions)
                        ? new WikiRootTransitiveFilter($ids)
                        : new FilterInPredicate($ids),
                    new SortDocumentClause()
                )
            )
        );
    }

    function buildCommentList( $object_it )
    {
        $list = parent::buildCommentList($object_it );
        if ( $_REQUEST['document'] != '' ) {
            $list->setAutoRefresh(false);
        }
        return $list;
    }

    function getWaitFilters( $classes )
    {
        $filters = parent::getWaitFilters($classes);

        $table = $this->getTableRef();
        if ( $table instanceof PMWikiDocument && $table->getDocumentIt()->getId() > 0 ) {
            $filters[] = new WikiDocumentWaitFilter($table->getDocumentIt()->getId());
        }

        return $filters;
    }
}