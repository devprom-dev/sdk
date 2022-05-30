<?php
include_once SERVER_ROOT_PATH."pm/methods/MoveToProjectWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/FilterStateMethod.php";

include "PMWikiDocumentList.php";
include "DocumentSectionNumberingMode.php";

class PMWikiDocument extends PMWikiTable
{
 	private $object_it = null;
 	private $document_it = null;
 	private $revision_it = null;

    function getDocumentIt()
	{
	    if ( !is_object($this->document_it) ) {
            $this->document_it = $this->buildDocumentIt();
	    }
        return $this->getObject()->createCachedIterator($this->document_it->getRowset());
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
        $state_it = \WorkflowScheme::Instance()->getStateIt($this->getObject());
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

	function getDefaultPagesNumber() {
        if ( $_REQUEST['compareto'] != '' ) return 1;
        return 10;
    }

	function getPreviewPagesNumber($values)
	{
	    if ( $values['viewpages'] != '' ) return $values['viewpages'];
		return $this->getDefaultPagesNumber();
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

	function getFilterParms()
    {
		return array_merge(
            parent::getFilterParms(),
            array(
                'baseline', 'compareto', 'comparemode', 'treeoptions', 'viewpages'
            )
        );
	}

    function IsFilterParmPersisted( $parm ) {
        switch ($parm) {
            case 'baseline':
            case 'compareto':
            case 'comparemode':
                return false;
        }
        return true;
    }

    function getShareUrlParms() {
        return array_merge(
            array(
                'page' => $this->getDocumentIt()->getId(),
                'document' => $this->getDocumentIt()->getId()
            ),
            parent::getShareUrlParms()
        );
    }

    function getSaveActions( $actions )
    {
        $actions[] = array(
            'name' => text(2481),
            'url' => $this->getWidgetShareUrl(),
            'uid' => 'share'
        );

        return array_filter(
            parent::getSaveActions($actions),
            function($item) {
                return $item['uid'] != 'save-report';
            }
        );
    }

    public function buildFilterValuesByDefault( & $filters )
    {
        $values = parent::buildFilterValuesByDefault($filters);

        if ( $values['viewpages'] == '' ) {
            $values['viewpages'] = $this->getDefaultPagesNumber();
        }

        return $values;
    }

	function getFilters() {
		return $this->getDataFilters( parent::getFilters() );
	}

	protected function buildCustomFilters()
    {
        $filters = parent::buildCustomFilters();
        foreach( $filters as $filter ) {
            if ( $filter instanceof FilterWebMethod ) {
                $filter->setDefaultValue('');
            }
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
		return $parent_filters;
	}
	
	function getFilterPredicates( $values )
	{
		return array_merge(parent::getFilterPredicates( $values ),
            array(
                new WikiPageCompareContentFilter($values['comparemode'], $this->getCompareToSnapshot($values)),
                new FilterAttributePredicate('DocumentId', $this->getDocumentIt()->idsToArray())
            )
        );
	}

    function buildCompareBaselineFilter()
    {
    }

	function getTemplate() {
	    return 'pm/WikiDocument.php';
	}
	

	function getCompareToActions()
	{
		$selectedBaseline = $this->getRevisionIt()->getId();
		$selectedBranch = $this->getDocumentIt()->getId();
		$comparedToBaselineIt = $this->getCompareToSnapshot($_REQUEST);
        $comparedToBaselineText = text(1566);

		$baselines = array();
        $comparable = array();

        $registry = new ObjectRegistrySQL($this->getObject());
        $branchesIt = $registry->useImportantPersistersOnly()->Query(
            array(
                new FilterTextExactPredicate('UID', $this->getDocumentIt()->get('UID')),
                new ProjectActiveVpdPredicate(),
                new SortDocumentBaselineClause()
            )
        );
        $branchesMode = $branchesIt->count() > 1;

        while( !$branchesIt->end() )
        {
            $title = $branchesIt->get('DocumentVersion');
            if ( $title == '' ) {
                $title = $branchesIt->get('Caption');
            }

            $baselineUrl = $branchesIt->getUidUrl();
            $compareToUrl = "javascript: window.location = filterLocation.setup('compareto=document:".$branchesIt->getId()."', 1);";

            $appendBaselineToMenu = $selectedBranch != $branchesIt->getId() || $selectedBaseline != '';
            if ( $appendBaselineToMenu ) {
                if ( $branchesMode ) {
                    $baselines[$branchesIt->getId()] = array (
                        'name' => $title,
                        'items' =>  array(
                            array(
                                'name' => translate('Текущий'),
                                'url' => $baselineUrl
                            )
                        )
                    );
                }
                else {
                    $baselines[] = array (
                        'name' => $title,
                        'url' => $baselineUrl
                    );
                }
            }
            else {
                $selectedBaselineText = \TextUtils::getWords($title, 8);
            }

            if ( $branchesIt->getId() != $comparedToBaselineIt->get('DocumentId') ) {
                if ( $branchesMode ) {
                    $comparable[$branchesIt->getId()] = array (
                        'name' => $title,
                        'items' =>  array(
                            array(
                                'name' => translate('Текущий'),
                                'url' => $compareToUrl
                            )
                        )
                    );
                }
                else {
                    $comparable[] = array (
                        'name' => $title,
                        'url' => $compareToUrl
                    );
                }
            }
            else {
                $comparedToBaselineText = \TextUtils::getWords($title, 8);
            }

            $branchesIt->moveNext();
        }

        $baselineIt = $this->getBaselineObject()->getRegistry()->Query(
            array(
                new WikiPageBaselineUIDPredicate($this->getDocumentIt()->get('UID')),
                new WikiPageBaselineDocumentPersister(),
                new FilterAttributeNullPredicate('Type'),
                new SortAttributeClause('ObjectId'),
                new SortAttributeClause('Caption')
            )
        );

        $baselinesTop = array();
        $comparableTop = array();

        $baselineIt->moveFirst();
        while( !$baselineIt->end() )
        {
            $title = $baselineIt->getDisplayName();

            $branchesIt->moveToId($baselineIt->get('DocumentId'));
            $baselineUrl = $branchesIt->getUidUrl() . '&baseline='.$baselineIt->getId();
            $compareToUrl = "javascript: window.location = filterLocation.setup('compareto=".$baselineIt->getId()."', 1);";

            $appendBaselineToMenu = $baselineIt->getId() != $selectedBaseline;
            if ( $appendBaselineToMenu ) {
                if ( $branchesMode && $baselineIt->get('DocumentId') != $selectedBranch ) {
                    $baselines[$baselineIt->get('DocumentId')]['items'][] = array (
                        'name' => $title,
                        'url' => $baselineUrl
                    );
                }
                else {
                    array_unshift($baselinesTop, array (
                        'name' => $title,
                        'url' => $baselineUrl
                    ));
                }
            }
            else {
                $selectedBaselineText = \TextUtils::getWords($title, 8);
            }

            if ( $baselineIt->getId() != $comparedToBaselineIt->getId() ) {
                if ( $appendBaselineToMenu ) {
                    if ( $branchesMode && $baselineIt->get('DocumentId') != $comparedToBaselineIt->get('DocumentId') ) {
                        $comparable[$baselineIt->get('DocumentId')]['items'][] = array (
                            'name' => $title,
                            'url' => $compareToUrl
                        );
                    }
                    else {
                        array_unshift($comparableTop, array (
                            'name' => $title,
                            'url' => $compareToUrl
                        ));
                    }
                }
            }
            else {
                $comparedToBaselineText = \TextUtils::getWords($title, 8);
            }

            $baselineIt->moveNext();
        }

        $baselines = array_merge($baselinesTop, $baselines);
        $comparable = array_merge($comparableTop, $comparable);

        if ( count($baselines) < 1 ) return array();

        array_unshift($baselines, array(
            'uid' => 'search'
        ));
        array_unshift($comparable, array(
            'uid' => 'search'
        ));

        if ( $comparedToBaselineIt->getId() != '' ) {
            $comparable[] = array();
            $comparable['reset_comparison'] = array (
                'name' => text(1710),
                'url' => "javascript: window.location = filterLocation.setup('compareto=',1);"
            );
        }

        $actions = array (
            array (
                'name' => translate('Бейзлайн') . ': ' . $selectedBaselineText,
                'class' => 'btn-info',
                'items' => $baselines,
                'uid' => 'baseline'
            ),
            array (
                'name' => $comparedToBaselineText,
                'class' => $comparedToBaselineIt->getId() != '' ? 'btn-info' : "btn-light",
                'items' => $comparable,
                'uid' => 'compareto'
            )
        );

        if ( $comparedToBaselineIt->getId() != '' ) {
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

 	function getRenderParms( $parms )
 	{
        if ( $_REQUEST['compareto'] != '' && $_REQUEST['comparemode'] == '' ) {
            $_REQUEST['comparemode'] = 'modified';
        }

		$parent_parms = parent::getRenderParms( $parms );

        foreach( $parent_parms['sections'] as $key => $section ) {
            if ( $section instanceof WikiTreeSectionNew && $this->dataFilterApplied() ) {
                $section->setObjectIt( $this->getListIteratorRef() );
            }
        }

        $registry_url = $this->getListViewWidgetIt()->getUrl('group=none&type=all&document='.$this->getDocumentIt()->getId());
        $module_it = $this->getDocumentsModuleIt();

		return array_merge( $parent_parms, array (
		    'object_id' => $this->getObjectIt()->getId() > 0 ? $this->getObjectIt()->getId() : $this->getDocumentIt()->getId(),
			'widget_id' => $this->getDocumentIt()->getId(),
            'docs_title' => array_shift(explode(' ',$module_it->getDisplayName())),
            'registry_title' => translate('Реестр'),
            'docs_url' => $module_it->getUrl(),
            'document_url' => $this->getDocumentIt()->getUidUrl(),
            'registry_url' => $registry_url,
            'has_hierarchy' => array_sum($this->getDocumentIt()->fieldToArray('TotalCount')) > 0,
            'hint' => $this->getPreviewPagesNumber($this->getFilterValues()) > 1 ? text(2090) : ''
 	    ));
 	}

    function getDocumentsModuleIt() {
 	    return getFactory()->getObject('Module')->getExact($this->getPage()->getModule());
    }
 	
	function getList( $type = '', $iterator = null ) {
	    return new PMWikiDocumentList( $this->getObject(), $iterator );
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

    function getTraceActions() {
		return array();
	}
	
	function getActions()
	{
 		if ( $this->getRevisionIt()->getId() > 0 ) {
 			return $this->getVersioningActions();
 		}
        $actions = array();

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
			
 		if ( $this->getRevisionIt()->getId() > 0 ) return $actions;

		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();

		$url = $this->getDocumentIt()->getUidUrl();
        $actions[] = array (
            'name' => translate('Просмотр'),
            'url' => $url.'&viewmode=view'
        );

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() ) {
            if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
            $actions['import'] = array(
                'name' => text(2262),
                'url' => $method->getJSCall(
                            array(
                                'ParentPage' => $this->getDocumentIt()->getId(),
                                'view' => 'importdoc'
                            ),
                            translate('Импорт')
                         ),
                'uid' => 'import-doc'
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

            $method = new MoveToProjectWebMethod($this->getDocumentIt());
            if ( $method->hasAccess() ) {
                $method->setRequestIt($this->getDocumentIt());
                $actions[] = array (
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall()
                );
            }
        }

        $method = new WikiRemoveStyleWebMethod($this->getDocumentIt());
        if ( $method->hasAccess() ) {
            $actions[] = array (
                'name' => $method->getCaption(),
                'url' => $method->getJSCall()
            );
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

 	function drawFooter()
 	{
 	}

 	function getEditable()
    {
        $filter_values = $this->getFilterValues();
        return $_REQUEST['viewmode'] != 'view'
            && in_array($filter_values['search'], array('','all','hide'))
            && $this->getRevisionIt()->getId() < 1
            && $this->getCompareToSnapshot($filter_values)->getId() < 1
            && getFactory()->getAccessPolicy()->can_modify($this->getObject());
    }

    function dataFilterApplied()
    {
        $values = $this->getFilterValues();
        foreach( $this->getDataFilters($this->buildFilters()) as $filter ) {
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
        $compare_to = $this->getCompareToSnapshot($_REQUEST);
        if ( $compare_to->getId() != '' ) {
            $form->setCompareTo($compare_to);
        }
        return $form;
    }

    function getListViewWidgetIt() {
        return null;
    }

    function getDetails()
    {
        $documentIt = $this->getDocumentIt();
        $filterValues = $this->getFilterValues();

        return array(
            'comments' => array (
                'image' => 'icon-comment',
                'title' => text(980),
                'url' => getSession()->getApplicationUrl().
                            'details/comments/'.get_class($documentIt->object).'/'.$documentIt->getId()
                                .'?tableonly=true&commentstate='.$filterValues['commentstate']
            ),
           'props' => array (
                'image' => 'icon-zoom-in',
                'title' => text(2167),
                'url' => getSession()->getApplicationUrl().'tooltip/%class%/%id%?extended'
            ),
            'more' => array (
                'image' => 'icon-time',
                'title' => text(2166),
                'url' => getSession()->getApplicationUrl().'details/log?tableonly=true&document='
                            .$documentIt->getId()
            )
        );
    }

    protected function buildQuickReports(& $base_actions)
    {
    }

    function buildLinkStateFilter( $values ) {
        return new PMWikiTransitiveLinkedStateFilter( $values['linkstate'] );
    }

    function buildCompareToSnapshot($values)
    {
        $matches = array();
        if( preg_match('/document:(\d+)/', $values['compareto'], $matches) && $matches[1] != '' ) {
            if ( $matches[1] != $this->getRevisionIt()->getId() ) {
                $registry = new WikiPageRegistryContent($this->getObject());
                return $registry->Query(array(new FilterInPredicate($matches[1])));
            }
        }
        else if ( $values['compareto'] == 'latest' ) {
            $snapshot = new WikiPageComparableSnapshot($this->getDocumentIt());
            $snapshot_it = $snapshot->getLatest(2);
            if ( $snapshot_it->count() > 1 ) {
                $snapshot_it->moveNext();
            }
            return $snapshot_it;
        }
        return parent::buildCompareToSnapshot($values);
    }
}