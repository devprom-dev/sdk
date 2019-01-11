<?php
include SERVER_ROOT_PATH."pm/classes/wiki/WikiPageComparableSnapshot.php";
include_once SERVER_ROOT_PATH."pm/methods/FilterStateMethod.php";

include "PMWikiDocumentList.php";
include "DocumentMode.php";
include "DocumentSectionNumberingMode.php";

class PMWikiDocument extends PMWikiTable
{
 	private $object_it = null;
 	private $document_it = null;
 	private $revision_it = null;
 	private $compareto_it = null;
    private $version_it = null;
 	
    function getDocumentIt()
	{
	    if ( is_object($this->document_it) ) {
	    	return $this->getObject()->createCachedIterator($this->document_it->getRowset());
	    }
	    return $this->document_it = $this->buildDocumentIt();
	}
	
	protected function buildDocumentIt()
	{
        if ( !in_array($_REQUEST['page'], array('', 'all')) )
        {
            $registry = new ObjectRegistrySQL($this->getObject());
            $documentIt = $registry->Query(
                    array(
                        new FilterInPredicate($_REQUEST['page'])
                    )
                )->getRef('DocumentId');
            if ( $documentIt->getId() != '' ) return $documentIt;
        }

		if ( !in_array($_REQUEST['document'], array('', 'all')) )
	    {
			$registry = new ObjectRegistrySQL($this->getObject());
        	$documentIt = $registry->Query(
                array(
                    new DocumentVersionPersister(),
                    new FilterInPredicate($_REQUEST['document']),
                    new WikiRootFilter()
                )
			);
        	if ( $documentIt->getId() != '' ) return $documentIt;
	    }

        return $this->getObject()->getEmptyIterator();
	}

	function buildFilterState($filterValues = array())
    {
        $state_it = getFactory()->getObject($this->getObject()->getStateClassName())->getRegistry()->Query(
            array(
                new FilterVpdPredicate($this->getDocumentIt()->get('VPD'))
            )
        );
        return new FilterStateMethod($this->getObject(), $state_it);
    }

    function getObjectIt()
	{
	    if ( is_object($this->object_it) ) return $this->object_it->copy();
	    
	    $key = 'page';
	    
	    if ( $_REQUEST[$key] != '' ) {
			$registry = new ObjectRegistrySQL($this->getObject());
			$this->object_it = $registry->Query(
					array(new FilterInPredicate($_REQUEST[$key]))
			);
	    }
	    else {
	        $this->object_it = $this->getObject()->getEmptyIterator();
	    }
	    
	    return $this->object_it;
	}
	
	function getPreviewPagesNumber()
	{
        $values = $this->getFilterValues();
		return $values['viewmode'] == 'view' || $_REQUEST['compareto'] != ''
            ? 0
            : (defined('DOC_CACHED_PAGES') ? DOC_CACHED_PAGES : 50);
	}

	function getDefaultRowsOnPage()
	{
		return 9999;
	}

	function getRevisionIt()
	{
	    if ( is_object($this->revision_it) ) return $this->revision_it;

        $values = $this->getFilterValues();
	    $baseline = getFactory()->getObject('Snapshot');
 		if ( in_array($values['baseline'], array('', 'none', 'all')) ) {
 			$this->revision_it = $baseline->getEmptyIterator();
 		}
 		else {
 			$this->revision_it = $baseline->getExact($values['baseline']);
 		}
 		
 		return $this->revision_it;
	}

	function getVersionIt()
    {
        if ( is_object($this->version_it) ) return $this->version_it;

        $revision_it = $this->getRevisionIt();
        if ( is_object($revision_it) ) {
            $version = $revision_it->getDisplayName();
        }
        if ( $version == "" ) {
            $version = $this->getDocumentIt()->get('DocumentVersion');
        }
        if ( $version == "" ) {
            return $this->version_it = getFactory()->getObject('Baseline')->getEmptyIterator();
        }
        return $this->version_it = getFactory()->getObject('Baseline')->getByRef('Caption', $version);
    }

	function buildFiltersName() {
        return parent::buildFiltersName().'-'.$this->getDocumentIt()->getId();
	}
	
	function getFilterParms() {
		return array_merge( parent::getFilterParms(), array( 'baseline' ));
	}

    function getShareUrlParms() {
        return array_merge(
            array(
                'viewmode' => 'recon',
                'page' => $this->getDocumentIt()->getId(),
                'document' => $this->getDocumentIt()->getId()
            ),
            parent::getShareUrlParms()
        );
    }

    function getSaveActions( $actions ) {
        return $actions;
    }

    public function buildFilterValuesByDefault( & $filters )
    {
        $values = parent::buildFilterValuesByDefault($filters);

        // special case for reviewmode-filter
        $persistent_filter = $this->getPersistentFilter();
        foreach ( $this->filters as $filter )
        {
            $filter_name = $filter->getValueParm();
            if ( $filter_name != 'viewmode' ) continue;

            $filter->setFreezeMethod($persistent_filter);
            $filter->setFilter($this->getFiltersName());
            $value = $filter->getPersistedValue();
            if ( !is_null($value) && $value != '' ) {
                $values[$filter_name] = $value;
                continue;
            }
        }

        if ( !in_array($values['bydate'], array('','all')) || !in_array($_REQUEST['revision'], array('','all')) ) {
            $_REQUEST['viewmode'] = 'view';
        }
        return $values;
    }

	function getFilters()
	{
		$parent_filters = $this->getDataFilters( parent::getFilters() );
		return array_merge(
            $parent_filters,
            array (
                $this->buildViewModeFilter(),
            )
        );
	}

	function getFiltersDefault()
	{
		return array('state','linkstate','search');
	}

	protected function buildCustomFilters()
    {
        $filters = parent::buildCustomFilters();
        foreach( $filters as $filter ) {
            $filter->setDefaultValue('');
        }
        return $filters;
    }

    function getDataFilters( $parent_filters )
	{
		foreach( $parent_filters as $key => $filter ) {
			if ( is_a($filter, 'FilterStateMethod') ) $filter->setDefaultValue('all');
			if ( $filter->getValueParm() == 'document' ) {
				unset($parent_filters[$key]);
				$parent_filters = array_values($parent_filters);
			}
		}
		$parent_filters[] = new FilterTextWebMethod( text(2085), 'search' );
		return $parent_filters;
	}
	
	function getFilterPredicates()
	{
		return array_merge( parent::getFilterPredicates(),
            array (
                new WikiDocumentWaitFilter($this->getDocumentIt()->idsToArray()),
                new WikiPageCompareContentFilter( $_REQUEST['comparemode'], $this->getCompareToSnapshot() )
            )
        );
	}

    function buildCompareBaselineFilter()
    {
    }

	function buildViewModeFilter()
	{
	    $mode_filter = new FilterObjectMethod( new DocumentMode(), '', 'viewmode' );
	    $mode_filter->setIdFieldName('ReferenceName');
	    $mode_filter->setHasAll( false );
	    $mode_filter->setHasNone( false );
	    $mode_filter->setType( 'singlevalue' );
	    if ( !getFactory()->getAccessPolicy()->can_modify($this->getObject()) ) {
            $mode_filter->setDefaultValue('view');
        }
	    return $mode_filter;
	}	
	
	function getTemplate()
	{
	    return 'pm/WikiDocument.php';
	}
	
	function getCompareToSnapshot()
	{
		if ( is_object($this->compareto_it) ) return $this->compareto_it;
	 
		$matches = array();
		if( preg_match('/document:(\d+)/', $_REQUEST['compareto'], $matches) && $matches[1] != '' )
		{
			if ( $matches[1] != $this->getRevisionIt()->getId() ) {
				$registry = new WikiPageRegistryContent($this->getObject());
				return $this->compareto_it = $registry->Query(array(new FilterInPredicate($matches[1])));
			}
		}
		else if ( !in_array($_REQUEST['compareto'], array('', 'none', 'all')) )
		{
			$snapshot = new WikiPageComparableSnapshot($this->getDocumentIt());
            if ( $_REQUEST['compareto'] == 'latest' ) {
                $snapshot_it = $snapshot->getLatest(2);
                if ( $snapshot_it->count() > 1 ) {
                    $snapshot_it->moveNext();
                }
            }
            else {
                $snapshot_it = $snapshot->getExact($_REQUEST['compareto']);
            }
			if ( $snapshot_it->getId() != '' ) return $this->compareto_it = $snapshot_it;
		}

    	return $this->getObject()->getEmptyIterator();
	}
	
	function getCompareToActions()
	{
		$snapshot = new WikiPageComparableSnapshot($this->getDocumentIt());
		$snapshot_it = $snapshot->getAll();
		$document_it = $this->getDocumentIt();
		$selected = $this->getCompareToSnapshot()->getId();

		$actions = array();
		$baselines = array();

		$title = text(1566);

		while( !$snapshot_it->end() )
		{
			if ( $snapshot_it->getId() != $this->getRevisionIt()->getId() && $snapshot_it->getId() != 'document:'.$document_it->getId() ) {
				$actions[] = array (
						'name' =>
							$snapshot_it->get('Type') == 'branch' || strpos($snapshot_it->getId(), 'document') !== false
								? translate('Бейзлайн').': '.$snapshot_it->getDisplayName()
								: translate('Версия').': '.$snapshot_it->getDisplayName(),
						'url' =>
							"javascript: window.location = updateLocation('compareto=".$snapshot_it->getId()."', window.location.toString());"
				);
			}
			
			if ( in_array($snapshot_it->getId(), array($selected,'document:'.$selected)) ) {
				$title .= ": ".$snapshot_it->getDisplayName();
				if ( mb_strlen($title) > 60 ) $title = mb_substr($title, 0, 60).'...';
			}
			
			if ( $snapshot_it->get('Type') == 'branch' || strpos($snapshot_it->getId(), 'document') !== false )
			{
				$registry = new ObjectRegistrySQL($this->getObject());
				$doc_it = $registry->Query(
					array (
						new FilterInPredicate($snapshot_it->get('ObjectId'))
					)
				);

				$baseline_url = "javascript: window.location = '".$doc_it->getViewUrl()."';";
				$baseline_title = translate('Бейзлайн').': '.$snapshot_it->getDisplayName();

				if ( $document_it->getId() == $snapshot_it->get('ObjectId') ) {
					$baseline_selected = $baseline_title;
				}
			}
			else
			{
				$baseline_url = "javascript: window.location = updateLocation('baseline=".$snapshot_it->getId()."', window.location.toString());";
				$baseline_title = translate('Версия').': '.$snapshot_it->getDisplayName();

				if ( $this->getRevisionIt()->getId() == $snapshot_it->getId() ) {
					$baseline_selected = $baseline_title;
				}
			}

			$baselines[] = array (
					'name' => $baseline_title,
					'url' => $baseline_url 
			); 
			
			$snapshot_it->moveNext();
		}

		if ( $this->getRevisionIt()->getId() != '' )
		{
			$document_title = translate('Бейзлайн').': '.($document_it->get('DocumentVersion') != '' ? $document_it->get('DocumentVersion') : $document_it->getDisplayName());
			if ( $document_it->getId() == $selected ) $title = $document_title;

			$actions[] = array (
				'name' => $document_title,
				'url' => "javascript: window.location = updateLocation('compareto=document:".$this->getDocumentIt()->getId()."', window.location.toString());"
			);
		}
		
		if ( $baseline_selected == "" ) $baseline_selected = $baseline_title;
		
		if ( mb_strlen($baseline_selected) > 60 ) $baseline_selected = mb_substr($baseline_selected, 0,60).'...';
		
		if ( count($baselines) < 2 && count($actions) < 2 )
		{
			return array();
		}
		else
		{
			$actions = array (
                array (
                    'name' => $baseline_selected,
                    'class' => $baseline_selected != translate('Версия') ? 'btn-info' : "btn-light",
                    'items' => $baselines,
                    'uid' => 'baseline'
                ),
                array (
                    'name' => $title,
                    'class' => $selected != '' ? 'btn-info' : "btn-light",
                    'items' => $actions,
                    'uid' => 'compareto'
                )
            );

			if ( $this->getCompareToSnapshot()->getId() != '' ) {
                $actions[] = array(
                    'name' => $_REQUEST['comparemode'] == 'modified' ? text(2601) : text(2600),
                    'class' => $_REQUEST['comparemode'] != '' ? 'btn-info' : "btn-light",
                    'items' => array(
                        array(
                            'name' => translate('Все'),
                            'url' => "javascript: window.location = updateLocation('comparemode=all', window.location.toString());"
                        ),
                        array(
                            'name' => translate('Измененные'),
                            'url' => "javascript: window.location = updateLocation('comparemode=modified', window.location.toString());"
                        )
                    )
                );
            }
            return $actions;
		}
	}

	function getBaselinesListWidget() {
		return null;
	}

 	function getRenderParms( $parms )
 	{
        if ( $_REQUEST['comparemode'] == '' ) {
            $_REQUEST['comparemode'] = 'modified';
        }

		$parent_parms = parent::getRenderParms( $parms );

        foreach( $parent_parms['sections'] as $key => $section ) {
            if ( $section instanceof WikiTreeSectionNew && $this->dataFilterApplied() ) {
                $section->setObjectIt( $this->getListIteratorRef() );
                $section->setPlainMode();
            }
        }

		if ( $this->getPreviewPagesNumber() < 2 ) {
    		unset($parent_parms['hint']);
		}
		else {
            if ( !$parent_parms['hint_open'] ) {
                unset($parent_parms['hint']);
            }
        }

        $module_it = $this->getDocumentsModuleIt();

		return array_merge( $parent_parms, array (
		    'object_id' => $this->getObjectIt()->getId() > 0 ? $this->getObjectIt()->getId() : $this->getDocumentIt()->getId(),
			'widget_id' => $this->getDocumentIt()->getId(),
            'docs_title' => array_shift(explode(' ',$module_it->getDisplayName())),
            'docs_url' => $module_it->getUrl()
 	    ));
 	}

 	function getDocumentsModuleIt() {
 	    return getFactory()->getObject('Module')->getExact($this->getPage()->getModule());
    }
 	
	function getList( $type = '', $iterator = null )
	{
	    $list = new PMWikiDocumentList( $this->getObject(), $iterator );
	    
	    $list->setInfiniteMode();
	    
	    return $list;
	}

	protected function getSectionName() {
		return $this->getObject()->getDisplayName();
	}

    function getExportPageIt() {
        if ( $this->dataFilterApplied() ) {
            return $this->getListIteratorRef();
        }
        else {
            return $this->getDocumentIt();
        }
    }

	function getNewActions()
	{
	    return array();
	}

	function getTraceActions()
	{
		return $this->getForm()->getTraceActions( $this->getDocumentIt() );
	}
	
	function getActions()
	{
 		if ( $this->getRevisionIt()->getId() > 0 ) {
 			return $this->getVersioningActions();
 		}

		$actions = array();
        $listWidgetIt = $this->getListViewWidgetIt();
        if ( getFactory()->getAccessPolicy()->can_read($listWidgetIt) ) {
            $actions[] = array (
                'name' => text(2271),
                'url' => $listWidgetIt->getUrl(
                    $this->dataFilterApplied()
                        ? strtolower(get_class($this->getObject())).'='.\TextUtils::buildIds($this->getListIteratorRef()->idsToArray())
                        : 'group=none&type=all&document='.$this->getDocumentIt()->getId()
                ),
                'uid' => 'list-view',
                'view' => 'button'
            );
        }

	 	$temp_actions = $this->getTraceActions();
 		if ( count($temp_actions) > 0 )
 		{
 			if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
 			$actions[] = array (
                'name' => translate('Трассировка'),
                'items' => $temp_actions,
                'uid' => 'trace'
			);
 		}

		$temp_actions = $this->getVersioningActions();
 		if ( count($temp_actions) > 0 ) {
 			$actions[] = array();
 			$actions = array_merge( $actions, $temp_actions );
 		}
 		
		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		
		$history_url = $this->getDocumentIt()->getHistoryUrl();
		if ( $this->getRevisionIt()->getId() > 0 )
		{
			$history_url .= '&start='.$this->getRevisionIt()->getDateTimeFormat('RecordCreated'); 
		}
		$actions[] = array(
		        'name' => text(824),
				'url' => $history_url,
		        'uid' => 'history'
		);

		$report_it = getFactory()->getObject('PMReport')->getExact('discussions');
		if ( $report_it->getId() != '' )
		{
			$class_name = strtolower(get_class($this->getObject()));
			$item = $report_it->buildMenuItem('entities='.$class_name.'&'.$class_name.'='.$this->getDocumentIt()->getId());

			$actions[] = array(
				'name' => $report_it->getDisplayName(),
				'url' => $item['url'],
				'uid' => 'document-discussion'
			);
		}
			
 		if ( $this->getRevisionIt()->getId() > 0 )
 		{
			return $actions;
 		}
 		
		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();

		$url = $this->getDocumentIt()->getViewUrl();
		$actions[] = array (
            'name' => translate('Просмотр'),
            'url' => $url.'&viewmode=view'
        );

		if ( $this->getObject()->getStateClassName() != '' ) {
            $actions[] = array (
                'name' => translate('Согласование'),
                'url' => $url.'&viewmode=recon'
            );
        }

		$method = new WikiRemoveStyleWebMethod($this->getDocumentIt());
		
		if ( $method->hasAccess() )
		{
			if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
			
			$actions[] = array (
					'name' => $method->getCaption(),
					'url' => $method->getJSCall()
			);
		}

 		return $actions;
	}
	
	function getDeleteActions()
	{
	    $actions = array();

	    if ( !$this->getObject() instanceof ProjectPage ) {
            $method = new CloneWikiPageWebMethod($this->getDocumentIt());
            if ( $method->hasAccess() ) {
                $actions[] = array(
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(),
                    'uid' => 'clone-doc'
                );
            }
        }

		return $actions;
	}
	
 	function getSortFields()
	{
		return array();
	}
	
	function getSort( $parm )
	{
		return 'none';
	}
	
	function getCaption()
	{
		return $this->getDocumentIt()->getDisplayName();
	}
	
	function getId()
	{
		return join(':', array(get_class($this->getObject()), $this->getDocumentIt()->getId()));
	}
	
 	function drawFooter()
 	{
 	}

    function dataFilterApplied()
    {
        $values = $this->getFilterValues();
        foreach( $this->getDataFilters(parent::getFilters()) as $filter ) {
            if ( !in_array($values[$filter->getValueParm()],array('','all','hide')) ) {
                return true;
            }
        }
        return false;
    }

    function getForm()
    {
        $form = parent::getForm();
        $form->setDocumentIt( $this->getDocumentIt() );
        $form->setRevisionIt( $this->getRevisionIt() );
        $form->setVersionIt( $this->getVersionIt() );
        $compare_to = $this->getCompareToSnapshot();
        if ( $compare_to->getId() != '' ) {
            $form->setCompareTo($compare_to);
        }
        return $form;
    }

    function getListViewWidgetIt() {
        return null;
    }

    function getDetailsParms() {
        return array (
            'active' => 'discussions'
        );
    }

    function getDetails()
    {
        $details = parent::getDetails();
        $documentIt = $this->getDocumentIt();
        $details['discussions']['url'] .= '&document='.$documentIt->getId();
        $details['more']['url'] .= '&document='.$documentIt->getId();
        return $details;
    }

    protected function buildQuickReports(& $base_actions)
    {
    }

    function buildLinkStateFilter( $values ) {
        return new PMWikiTransitiveLinkedStateFilter( $values['linkstate'] );
    }
}