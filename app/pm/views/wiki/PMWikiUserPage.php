<?php
include_once SERVER_ROOT_PATH."pm/methods/CloneWikiPageWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/DocNewChildWebMethod.php";
include SERVER_ROOT_PATH."pm/methods/WikiRemoveStyleWebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelBaselineBuilder.php";
include "PMWikiTable.php";
include "PMWikiDocument.php";
include "WikiTreeSectionNew.php";
include "WikiDocumentSettingBuilder.php";
include "WikiPageSettingBuilder.php";
include "history/WikiHistoryTable.php";
include "history/WikiVersionTable.php";
include "history/WikiHistorySettingBuilder.php";
include "import/ImportDocumentForm.php";
include "WikiIncludeForm.php";
include "WikiChangeForm.php";

class PMWikiUserPage extends PMPage
{
    private $object = null;

 	function __construct()
 	{
 		parent::__construct();

        getSession()->addBuilder( new WikiDocumentSettingBuilder($this->getObject()) );
        getSession()->addBuilder( new WikiPageSettingBuilder() );
        getSession()->addBuilder( new WikiHistorySettingBuilder($this->getObjectIt()) );

        if( $_REQUEST['view'] == 'importdoc' )
        {
        }
        elseif ( $this->needDisplayForm() )
        {
            if (  $this->getFormRef() instanceof PMWikiForm ) {
                $this->addInfoSection(
                    new PageSectionAttributes($this->getObject(), 'efforts', translate('Трудозатраты'))
                );
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
        else if ( in_array($_REQUEST['view'],array('docs','list','tree')) ) {
            $table = $this->getTableRef();
            if ( $table instanceof PMWikiDocument ) {
                $this->addInfoSection(new WikiTreeSectionNew($table,$_REQUEST['baseline']));
            }
        }
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
 			case 'history':
 				return new WikiHistoryTable($this->getObject());

            case 'compare':
                return new WikiVersionTable($this->getObject());

 			case 'docs':
	 	        if ( $_REQUEST['document'] < 1 ) return $this->getTableBase();
            case 'tree':
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
 				echo \JsonWrapper::encode($this->exportWikiTree());
 				break;

 			default:
 				parent::export();
 		}
 	}

 	function exportForm( $object_it ) {
 	    Page::export();
    }

	function getExportPersisters() {
 	    $values = array (
            new WikiPageDetailsPersister(),
            new EntityProjectPersister(),
            new CommentRecentPersister()
        );
		return $values;
	}
 	
    function exportWikiTree()
 	{
		$object = $this->getObject();
        $rootIds = TextUtils::parseIds($_REQUEST['root']);

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
 			$registry = new WikiPageRegistryVersion($object);
			$registry->setDocumentIt($object_it);
			$registry->setSnapshotIt($snapshot_it);
            $registry->setPersisters(
                array_merge(
                    $registry->getPersisters(),
                    $this->getExportPersisters()
                    )
                );
 			$this->getFormRef()->setReadonly();
 		}
 		else
 		{
 			$registry = $object->getRegistry();
            $registry->setPersisters($this->getExportPersisters());
 		}

 		$filterValues = $this->getTableRef()->getFilterValues();
        $predicates = $this->getTableRef()->getFilterPredicates($filterValues);
        if ( $_REQUEST['treeoptions'] != '' ) $filterValues['treeoptions'] = $_REQUEST['treeoptions'];
 		$displayOptions = preg_split('/-/', $filterValues['treeoptions']);

 		$filteredRegistry = new ObjectRegistrySQL($object);
 		$filteredIds = $filteredRegistry->Query(
 		    array_merge(
 		        $predicates,
                array(
                    new FilterAttributePredicate('DocumentId', $object_it->fieldToArray('DocumentId'))
                )
            )
        )->idsToArray();

        while( !$object_it->end() ) {
            $children_it = $registry->Query(
                array(
                    new WikiDocumentFilter($object_it),
                    new SortDocumentClause()
                )
            );
            while ( !$children_it->end() ) {
                if ( $children_it->get('DocumentId') < 1 ) {
                    // not existed page (eg. there is no page in the snapshot)
                    $children_it->moveNext();
                    continue;
                }
                $json[] = $this->exportWikiNodeNew(
                    $children_it, $open_path, count($children_it->getParentsArray()), $displayOptions, $filteredIds );
                $children_it->moveNext();
            }
            $object_it->moveNext();
        }

        return \JSONWrapper::buildJSONTree($json, '');
 	}
 	
    function exportWikiNodeNew( $object_it, $open_path, $level, $options, &$filteredIds )
    {
        // display version (revision) number for the root only
        $caption = $object_it->get('ParentPage') == ''
            ? ($object_it->get('DocumentVersion') != '' ? '['.$object_it->get('DocumentVersion').'] ' : '') . $object_it->getTreeDisplayName($options)
            : $object_it->getTreeDisplayName($options);

        $extraClasses = '';
        if ( $object_it->get('TotalCount') > 0 )
        {
            if ( $object_it->get('ContentPresents') == 'Y' ) {
                $extraClasses .= ' folder_page';
            }
            else {
                $extraClasses .= ' folder';
            }
        }
        else {
            $extraClasses .= ' wiki_document';
        }

        if ( !in_array($object_it->getId(), $filteredIds) ) {
            $extraClasses .= ' filtered';
        }

        return array (
            'title' => $caption,
            'active' => $object_it->getId() == array_pop($open_path),
            'folder' => $object_it->get('TotalCount') > 0,
            'key' => $object_it->getId(),
            'expanded' => in_array($object_it->getId(), $open_path) || $level < 2,
            'extraClasses' => $extraClasses,
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

    function buildExportIterator( $object, $ids, $iteratorClassName, $queryParms )
    {
        if ( !$object instanceof WikiPage ) {
            return parent::buildExportIterator($object, $ids, $iteratorClassName, $queryParms);
        }

        $registry = $object->getRegistry();
        $version_it = getFactory()->getObject('Snapshot')->getExact($_REQUEST['baseline']);

        if ( $version_it->getId() != '' ) {
            $documentIt = $registry->getObject()->getExact(
                array_unique(
                    $registry->Query(
                        array(
                            new FilterInPredicate($ids),
                            new FilterVpdPredicate()
                        )
                    )->fieldToArray('DocumentId')
                )
            );
            $registry = new WikiPageRegistryVersion($registry->getObject());
            $registry->setDocumentIt($documentIt);
            $registry->setSnapshotIt($version_it);
        }

        $exportOptions = preg_split('/-/', $_REQUEST['options']);

        return $registry->Query(
            array_merge(
                array(
                    $_REQUEST['options'] == '' || in_array('children', $exportOptions)
                        ? new ParentTransitiveFilter($ids)
                        : new FilterInPredicate($ids),
                    new SortDocumentClause()
                ),
                $queryParms
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

    function getRecentChangedObjectIds( $filters )
    {
        $table = $this->getTableRef();

        if ( $table instanceof WikiHistoryTable ) {
            return $table->getObject()->getRegistry()->Query(
                array_merge(
                    $table->getFilterPredicates($table->getFilterValues()),
                    array (
                        new FilterModifiedSinceSecondsPredicate(5 * 60),
                        new FilterVpdPredicate(),
                        new SortChangeLogRecentClause()
                    )
                )
            )->idsToArray();
        }

        return parent::getRecentChangedObjectIds( $filters );
    }

    function getDemoDataIt( $object )
    {
        return $object->createCachedIterator(
            array(
                array(
                    'Caption' => translate('Раздел'),
                    'Content' => text(2500),
                    'OrderNum' => '1',
                    'Author' => getSession()->getUserIt()->getId()
                ),
                array(
                    'Caption' => translate('Подраздел'),
                    'Content' => text(2500),
                    'OrderNum' => '1.1',
                    'Author' => getSession()->getUserIt()->getId()
                )
            )
        );
    }
}