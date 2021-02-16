<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBaselineService;
include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class PMWikiDocumentList extends PMWikiList
{
    private $visible_ids = array();
    private $trace_source_attribute = array();
    private $trace_attributes = array();
	private $displayContentHeader = false;
    private $attributesVisible = false;
    private $attributeFields = array();
    private $previewCount = 0;
    private $baselineService = null;

    function __construct( $object )
    {
    	parent::__construct($object);
    	$this->trace_source_attribute = $this->getObject()->getAttributesByGroup('source-attribute');
        $this->trace_attributes = $this->getObject()->getAttributesByGroup('trace');
        $this->baselineService = new WikiBaselineService(getFactory(), getSession());
        $this->setInfiniteMode();
    }
    
 	function getSorts() {
	    return array( new SortDocumentClause() );
	}

	function extendModel()
    {
        parent::extendModel();

        $this->pageForm = $this->getTable()->getForm();
        $this->pageForm->setObject( $this->getTable()->getPage()->getObject() );
    }

    function buildIterator()
    {
        $iterator = parent::buildIterator();
        $rowData = array_pop($iterator->getRowset());
        $this->lastItemId = $rowData[$this->getObject()->getIdAttribute()];
        return $iterator;
    }

    protected function getPersisters( $object, $sorts )
    {
        $persisters = parent::getPersisters($object, $sorts);

        $baselineIt = $this->getTable()->getRevisionIt();
        $compareToIt = $this->getTable()->getCompareToSnapshot();

        if ( $baselineIt->getId() > 0 ) {
            $registry = $this->baselineService->getBaselineRegistry(
                $this->getTable()->getDocumentIt(), $baselineIt
            );
            if ( $compareToIt->getId() != '' ) {
                $registry->setComparisonMode();
            }
            $object->setRegistry($registry);
        }
        else if ( $compareToIt->getId() != '' ) {
            $registry = $this->baselineService->getComparableRegistry(
                $this->getTable()->getDocumentIt(), $compareToIt
            );
            $object->setRegistry($registry);
        }

        foreach( $this->trace_source_attribute as $attribute ) {
            if ( $this->getColumnVisibility($attribute) ) {
                $persisters[] = new WikiPageTracesRevisionsPersister();
                break;
            }
        }
        return $persisters;
    }

 	function getGroupFields()
 	{
 		return array();
 	}

    function getColumnVisibility($attr)
    {
        switch( $attr ) {
            case 'Content':
                return true;
            default:
                return parent::getColumnVisibility($attr);
        }
    }

	function getColumnFields()
	{
	    return array_diff(
            parent::getColumnFields(),
            array(
                'UID',
                'State',
                'DocumentId',
                'Caption',
                'ParentPage',
                'Project',
                'RecentComment',
                'OrderNum',
                'DocumentVersion'
            )
        );
	}
 	
	function IsNeedToDisplayNumber()
	{
	    return false;
	}
	
	function hasRows()
	{
		return false;
	}
	
	function IsNeedToDisplayOperations()
	{
	    return false;
	}
	
	function IsNeedToSelect()
	{
		return false;
	}
	
	private function rowIsVisible( & $object_it )
	{
		return in_array($object_it->getId(), $this->visible_ids) || count($this->visible_ids) < 1;
	}
	
	function IsNeedToSelectRow( $object_it )
	{
	    return $this->rowIsVisible($object_it);
	}

    protected function IsAttributeInQuery( $attribute )
    {
        return parent::IsAttributeInQuery( $attribute )
            || in_array($attribute, $this->trace_source_attribute);
    }

	function getRowClassName( $object_it )
	{
		if ( !$this->rowIsVisible($object_it) ) return 'row-empty';
	}
	
	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
	    if ( !$this->rowIsVisible($object_it) ) return;
		switch ( $attr )
		{
			default:
				if ( in_array($attr, $this->trace_source_attribute) )
                {
					$this->drawSourcePage( $entity_it, $object_it, $attr,
                        array_filter(
                            array_map(
                                function($value) {
                                    if ( $value == '' ) return '';
                                    $parts = preg_split('/:/', $value);
                                    return array( $parts[0] => $parts[1] );
                                },
                                preg_split('/,/',$object_it->get('TracesRevisions'))
                            ),
                            function($value) {
                                return is_array($value);
                            }
                        )
                    );
					break;
				}

				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
	    if ( !$this->rowIsVisible($object_it) ) return;
	    
		switch ( $attr )
		{
		    case 'Content':
	    		$this->drawPageContent( $object_it );
        		break;
			default:
				parent::drawCell( $object_it, $attr ) ;
		}
	}	
	
 	function getColumnWidth( $column )
 	{
 	    switch ( $column )
 	    {
 	        case 'Attributes':
 	            return '20%';
 	        default:
				if ( in_array($column, $this->trace_source_attribute) ) {
					return '50%';
				}
 	            return parent::getColumnWidth( $column );
 	    }
 	}
	
	function drawSourcePage( $entity_it, $object_it, $attr, $revisions )
	{
		$baselines = array();
		$baselines_data = $object_it->get($attr.'Baselines');
		if ( $baselines_data != '' ) {
			foreach( preg_split('/,/',$baselines_data) as $info ) {
				list($id, $baseline) = preg_split('/:/', $info);
				$baselines[$id] = $baseline;
			}
		}

		while( !$entity_it->end() )
		{
			if ( !$entity_it->object instanceof WikiPage ) {
				echo '<div class="trace-artefact" style="padding-left:18px;">';
					echo '<div style="margin-top: 2px;display:table;width:98%;">';
						echo '<div style="display:table-cell;padding-right:8px;vertical-align: top;width:1%;">';
							echo $this->getUidService()->getUidIcon($entity_it);
						echo '</div>';
						echo '<h4 class="bs" style="display:table-cell;">';
							echo $entity_it->getDisplayName();
						echo '</h4>';
					echo '</div>';
					echo '<div style="margin-top: 11px;">';
					    if ( $entity_it->object instanceof Feature ) {
                            $featureTraceIt = getFactory()->getObject('FunctionTrace')->getRegistry()->Query(
                                array(
                                    new FilterAttributePredicate('ObjectId', $object_it->getId()),
                                    new FilterAttributePredicate('Feature', $entity_it->getId()),
                                    new FilterAttributePredicate('IsActual', 'N')
                                )
                            );
                            if ( $featureTraceIt->count() < 1 ) {
                                echo $entity_it->getHtmlDecoded('Description');
                            }
                            else {
                                $issueIds = \TextUtils::parseIds(join(',', $featureTraceIt->fieldToArray('Issues')));
                                if ( count($issueIds) > 0 ) {
                                    $uid = new ObjectUID();
                                    $issueIt = getFactory()->getObject('Request')->getExact($issueIds);
                                    while( !$issueIt->end() ) {
                                        $uid->drawUidInCaption($issueIt);
                                        echo '<br/><br/>';
                                        echo $issueIt->getHtmlDecoded('Description');
                                        echo '<br/><br/>';
                                        $issueIt->moveNext();
                                    }
                                }
                                else {
                                    $reqIds = \TextUtils::parseIds(join(',', $featureTraceIt->fieldToArray('Requirements')));
                                    if ( count($reqIds) > 0 ) {
                                        $uid = new ObjectUID();
                                        $reqIt = getFactory()->getObject('Requirement')->getExact($reqIds);
                                        while( !$reqIt->end() ) {
                                            $uid->drawUidInCaption($reqIt);
                                            echo '<br/><br/>';
                                            $reqIt->moveNext();
                                        }
                                    }
                                    else {
                                        echo $entity_it->getHtmlDecoded('Description');
                                    }
                                }

                            }
                        }
					    else {
                            echo $entity_it->getHtmlDecoded('Description');
                        }
					echo '</div>';
				echo '</div>';
				$entity_it->moveNext();
				continue;
			}

            $registry = new ObjectRegistrySQL($entity_it->object);
			if ( $baselines[$entity_it->getId()] != '' ) {
				$source_it = $registry->Query(
					array (
						new FilterInPredicate($entity_it->getId()),
						new DocumentVersionPersister(),
						new SnapshotItemValuePersister($baselines[$entity_it->getId()]),
                        new EntityProjectPersister(),
                        new ProjectAccessibleVpdPredicate()
					)
				);
			}
			else {
                $source_it = $registry->Query(
                    array (
                        new FilterInPredicate($entity_it->getId()),
                        new DocumentVersionPersister(),
                        new EntityProjectPersister(),
                        new ProjectAccessibleVpdPredicate()
                    )
                );
			}

            if ( $source_it->getId() == '' ) {
                $entity_it->moveNext();
                continue;
            }

            $compare_to = $source_it;
            foreach( $revisions as $revision ) {
                if ( $revision[$entity_it->getId()] == '' ) continue;
				$compare_to = $registry->Query(
					array (
						new FilterInPredicate($entity_it->getId()),
						new DocumentVersionPersister(),
						new WikiPageRevisionPersister($revision[$entity_it->getId()]),
                        new EntityProjectPersister(),
                        new ProjectAccessibleVpdPredicate()
					)
				);
				break;
            }

			if ( count($revisions) > 0 && $compare_to->get('Content') == $source_it->get('Content') ) {
				$entity_it->moveNext();
                continue;
            }

			echo '<div class="trace-artefact" style="padding-left:18px;">';
                echo '<div style="margin-top: 2px;display:table;width:98%;">';
                    echo '<div style="display:table-cell;padding-right:8px;vertical-align: top;width:1%;">';
						$this->getUidService()->setBaseline($baselines[$source_it->getId()]);
                        echo $this->getUidService()->getUidIcon($source_it);
                    echo '</div>';
                    echo '<h4 class="bs" style="display:table-cell;">';
                        echo $source_it->getDisplayName();
                    echo '</h4>';
					if ( method_exists($source_it, 'getHistoryUrl') ) {
						echo '<div style="display:table-cell;vertical-align: top;text-align: right;">';
							echo '<a class="trace-history" target="_blank" href="'.$source_it->getHistoryUrl().'">'.text(824).'</a>';
						echo '</div>';
					}
                echo '</div>';
                echo '<div style="margin-top: 11px;">';
                    $field = new FieldCompareToContent($source_it, $source_it->getHtmlDecoded('Content'),$compare_to->getHtmlDecoded('Content'));
                    $field->draw();
                echo '</div>';
            echo '</div>';
			$entity_it->moveNext();
		}
		$this->getUidService()->setBaseline('');
	}
	
	function drawPageContent( $object_it )
	{
		$filter_values = $this->getFilterValues();
		$form = $this->pageForm;

		$form->setFormIndex( $object_it->getId() );
        $form->setObject($this->getTable()->getPage()->getObject());
		$form->show( $form->getObject()->createCachedIterator(array($object_it->getData())) );
		$form->setPage( $this->getTable()->getPage() );
        $form->buildForm();

        if ( in_array($filter_values['search'], array('','all','none')) ) {
            $filter_values['search'] = '';
        }
        $form->setSearchText($filter_values['search']);

		$compareto_it = $this->getTable()->getCompareToSnapshot();
		if ( $compareto_it->getId() != '' ) {
            $form->setCompareTo($this->baselineService->getComparedPageIt($object_it, $compareto_it));
		}

		$form_render_parms['modifiable'] = $this->itemsEditable;
		if (!$form_render_parms['modifiable'] ) $form->setReadonly();

		$visibility = array();
		foreach( $this->getObject()->getAttributes() as $key => $data ) {
            $visibility[$key] = $this->getColumnVisibility($key);
        }
        $form->setAttributesVisibility($visibility);

		$comment = getFactory()->getObject('Comment');
		if ( $form->getRevisionIt()->getId() > 0 ) {
			$snapshot = getFactory()->getObject('Snapshot');
			$comment->addFilter( new SnapshotBeforeDatePredicate($this->getTable()->getRevisionIt()->getId()) );
		}
		$form_render_parms['comments_count'] = $comment->getCountForIt($object_it);
		$form_render_parms['scrollable'] = $this->getScrollable();
        $form_render_parms['treeOptions'] = preg_split('/-/',$filter_values['treeoptions']);

		$form->setReviewMode();

        ob_start();
        $this->drawTraceAttributes(
            $form->getObjectIt(),
            $object_it->get('Includes') != '' ? array('IncludedIn') : array()
        );
        $form_render_parms['traces_html'] = ob_get_contents();
        ob_end_clean();

        if ( $this->attributesVisible ) {
            ob_start();
            $this->drawAttributes( $object_it );
            $form_render_parms['attributes_html'] = ob_get_contents();
            ob_end_clean();
        }

		$form->render( $this->getRenderView(), $form_render_parms);

		if ( $this->previewCount == 1 )
		{
            $registry = new ObjectRegistrySQL($this->getObject());
            $registry->setLimit(1);

            $documentIt = $this->getTable()->getDocumentIt();
            $documentIt->moveToId($object_it->get('DocumentId'));

            $prevIt = $registry->Query(
                    array_merge(
                        $this->getPredicates($this->getFilterValues()),
                        array(
                            new FilterAttributeLesserPredicate('SortIndex', $object_it->get('SortIndex')),
                            new WikiDocumentFilter($documentIt),
                            new SortDocumentDescClause()
                        )
                    )
                );
            $nextIt = $registry->Query(
                    array_merge(
                        $this->getPredicates($this->getFilterValues()),
                        array(
                            new FilterAttributeGreaterPredicate('SortIndex', $object_it->get('SortIndex')),
                            new WikiDocumentFilter($documentIt),
                            new SortDocumentClause()
                        )
                    )
                );

            if ( $prevIt->getId() != '' ) { ?>
                <div class="btn-group pull-left hidden-print" style="margin-top:8px;">
                    <a class="btn append-btn btn-sm btn-secondary" href="javascript: gotoRandomPage(<?=$prevIt->getId()?>,1,false);" title="<?=$prevIt->getDisplayNameSearch()?>">
                        <i class="icon-backward icon-white"></i> <?=\TextUtils::getWords($prevIt->get('Caption'),3)?>
                    </a>
                </div>
            <?php }

            $method = new ObjectCreateNewWebMethod($this->getObject());
            if ( $method->hasAccess() && $this->getTable()->getEditable() ) {
                $method->setVpd($documentIt->get('VPD'));
                $method->setRedirectUrl('function(jsonText){gotoPageJson(jsonText);}');
                $doc_section_url = $method->url(array(
                        'ParentPage' => $documentIt->getId()
                    ));
                ?>
                <div id="new-doc-section" class="btn-group pull-left hidden-print" style="margin-top:8px;">
                    <a class="btn append-btn btn-sm btn-success" href="<?=$doc_section_url?>">
                        <i class="icon-plus icon-white"></i> <?=$this->getObject()->getSectionName()?>
                    </a>
                </div>
                <?php
            }

            if ( $nextIt->getId() != '' ) { ?>
                <div class="btn-group pull-right hidden-print" style="margin-top:8px;">
                    <a class="btn append-btn btn-sm btn-secondary" href="javascript: gotoRandomPage(<?=$nextIt->getId()?>,1,false);" title="<?=$nextIt->getDisplayNameSearch()?>">
                        <i class="icon-forward icon-white"></i> <?= \TextUtils::getWords($nextIt->get('Caption'),3) ?>
                    </a>
                </div>
                <?php
            }
        }
	}

	function drawTraceAttributes( $object_it, $excludeAttributes )
	{
		$traces = array_intersect(
			array_keys($this->getObject()->getAttributes()),
            array_merge(
                $this->getObject()->getAttributesByGroup('trace'),
                $this->trace_source_attribute
            )
		);
        $traces = array_diff(
            $traces, $excludeAttributes
        );

		$items = '';
		foreach( $traces as $key => $attribute ) {
			$items .= $object_it->get($attribute);
		}

		echo '<div class="well well-small well-traces hover-holder '.($items == '' ? 'hidden' : '').'">';
		    echo '<div class="wel-tbl">';
                echo '<div class="lf-pn">';
                    echo '<ul class="inline">';
                        $firstItem = true;
                        foreach( $traces as $attribute ) {
                            if ( $object_it->get($attribute) == '' ) continue;

                            if ( $attribute == 'Dependency' ) {
                                $items = \TextUtils::parseItems($object_it->get($attribute));
                                $objects = array();
                                foreach( $items as $item ) {
                                    list($className, $objectId) = preg_split('/:/', $item);
                                    $objects[$className][] = $objectId;
                                }
                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random hidden-print"></i>';
                                    $firstItem = false;
                                }
                                echo translate($this->getObject()->getAttributeUserName($attribute)).': ';
                                foreach( $objects as $className => $ids ) {
                                    $object = getFactory()->getObject($className);
                                    if ( !is_object($object) ) continue;
                                    parent::drawRefCell($object->getExact($ids), $object_it, $attribute);
                                }
                                echo '</li>';
                            }
                            else if ( $object_it->object->IsReference($attribute) )
                            {
                                $ref_it = $this->getFilteredReferenceIt($attribute, $object_it->get($attribute));
                                if ( $ref_it->count() < 1 ) continue;

                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random hidden-print"></i>';
                                    $firstItem = false;
                                }
                                echo translate($this->getObject()->getAttributeUserName($attribute)).': ';
                                parent::drawRefCell($ref_it, $object_it, $attribute);
                                echo '</li>';
                            }
                            else {
                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random hidden-print"></i>';
                                    $firstItem = false;
                                }
                                echo translate($this->getObject()->getAttributeUserName($attribute)).': ';
                                parent::drawCell($object_it, $attribute);
                                echo '</li>';
                            }
                        }
                    echo '</ul>';
                echo '</div>';
                $method = new ObjectModifyWebMethod($object_it);
                if ( $method->hasAccess() ) {
                    echo '<div class="rg-pn hidden-print">';
                        echo '<a id="doc-page-trace" class="dashed" onclick="'.$method->getJSCall(array('tab'=>'trace')).'">'.translate('трассировки').'</a>';
                    echo '</div>';
                }
            echo '</div>';
		echo '</div>';
	}

	function drawAttributes( $object_it )
	{
	    $visibleColumns = array();
		foreach( $this->getObject()->getAttributes() as $key => $attribute )
		{
			if ( !in_array($key, $this->attributeFields) ) continue;
			if ( !$this->getColumnVisibility($key) ) continue;
			if ( trim($object_it->get($key)) == '' ) continue;
            $visibleColumns[] = $key;
		}

        $attributeColumns = array_chunk($visibleColumns, count($visibleColumns) / 2 + 1);
		echo '<div class="row-fluid">';
		foreach( $attributeColumns as $column ) {
		    echo '<div class="'.(count($attributeColumns) > 1 ? 'span6' : '').'"><table class="table table-bordered table-striped"><tbody>';
		    foreach( $column as $attribute ) {
		        echo '<tr><td width="25%">' . $object_it->object->getAttributeUserName($attribute) . '</td><td>';
                if ( $this->getObject()->IsReference($attribute) ) {
                    $this->drawRefCell( $object_it->getRef($attribute), $object_it, $attribute );
                }
                else {
                    $this->drawCell( $object_it, $attribute );
                }
                echo '</td></tr>';
            }
		    echo '</tbody></table></div>';
        }
        echo '</div>';
	}
	
	function getAttributesVisible()
	{
		foreach( $this->getObject()->getAttributes() as $key => $attribute ) {
			if ( $this->getColumnVisibility($key) && in_array($key, $this->attributeFields) ) return true;
		}
		return false;
	}
	
	function getHeaderAttributes( $attr )
	{
		switch ( $attr )
		{
		    case 'Content':
		    	if ( $_REQUEST['tableonly'] != '' ) {
					return array (
						'script' => '#',
						'name' => $this->displayContentHeader ? translate('Документ') : ''
					);
				}
		    	
		    	$compare_actions = $this->getTable()->getCompareToActions();
		    	if ( count($compare_actions) > 0 )
		    	{
					$parms = array (
						'actions' => $compare_actions
					);

					if ( $this->getTable()->getCompareToSnapshot()->getId() != '' ) {
                        $documentIt = $this->getTable()->getDocumentIt();
                        $actions = $this->getForm($documentIt)
                            ->getReintegrateActions( $documentIt, $this->getTable()->getCompareToSnapshot(), 'all' );

                        if ( count($actions) > 0 ) {
                            ob_start();
                            ?>
                            <div class="btn-group operation pull-left">
                                <a tabindex="-1" class="btn btn-sm btn-warning dropdown-toggle actions-button" data-toggle="dropdown" href="">
                                    <?=text(2675)?>
                                    <span class="caret"></span>
                                </a>
                                <?php
                                echo $this->getRenderView()->render('core/PopupMenu.php', array (
                                    'items' => $actions
                                ));
                                ?>
                            </div>
                            <?php
                            $parms['html'] = ob_get_contents();
                            ob_end_clean();
                        }
                    }

					return array (
						'script' => '#',
						'name' => $this->getRenderView()->render('pm/WikiDocumentHeader.php', $parms)
			    	);
		    	}
		    	else
		    	{
			    	return array (
			    			'script' => '#',
			    			'name' => $this->displayContentHeader ? translate('Документ') : ''
			    	);
		    	}
		    	
		    default:
                return parent::getHeaderAttributes( $attr );
		}
	}
	
	function getSortingParms()
	{
		return array (
				'SortIndex',
				'asc'
		);
	}

	function getScrollable() {
		return $this->getTable()->getPreviewPagesNumber($this->getFilterValues()) > 1;
	}

 	function getRenderParms()
 	{
        $parent_parms = parent::getRenderParms();

        $filterValues = $this->getFilterValues();
        $this->previewCount = $this->getTable()->getPreviewPagesNumber($filterValues);

        $visibleColumns = array('Content','Attributes');
        $this->attributeFields =
            array_diff(
                array_filter($parent_parms['columns'], function($value) use ($visibleColumns) {
                    return !in_array($value, $visibleColumns);
                }),
                $this->trace_source_attribute,
                $this->trace_attributes,
                array(
                    'SectionNumber',
                    'Tags',
                    'PageType',
                    'Attachments'
                )
            );

        $this->attributesVisible = $this->getAttributesVisible();
		$this->itemsEditable = $this->getTable()->getEditable();

        if ( $_REQUEST[strtolower(get_class($this->getObject()))] != '' ) {
            $this->visible_ids = preg_split('/\,/', $_REQUEST[strtolower(get_class($this->getObject()))]);
        }
        else {
            $pageId = $this->getTable()->getObjectIt()->getId();
            if ( $pageId == '' ) $pageId = $this->getIteratorRef()->getId();

            $ids = $this->getIteratorRef()->idsToArray();
            $this->visible_ids = array_slice(
                $ids,
                max(0, array_search($pageId, $ids)),
                $this->previewCount
            );
        }

        $columns = array();
        if ( count(\TextUtils::parseFilterItems($filterValues['linkstate'])) > 0 ) {
            $columns =
                array_intersect(
                    $parent_parms['columns'],
                    $this->getObject()->getAttributesByGroup('source-attribute')
                );
        }

		return array_merge( $parent_parms, array (
		    'table_class_name' => 'table-document',
			'reorder' => true,
			'draggable' => false,
			'toolbar' => $this->hasToolbar(),
			'visible_pages' => $this->previewCount,
            'columns' => array_merge(array('Content'), $columns)
 	    ));
 	}

 	function getSettingsViewParms()
    {
        $commonParms = parent::getSettingsViewParms();
        $commonParms['show']['name'] = translate('Атрибуты');
        $values = $this->getFilterValues();

        $parms = array(
            'show' => $commonParms['show']
        );

        $options = array (
            'uid' => text(2922)
        );
        if ( $this->getObject() instanceof MetaobjectStatable && $this->getObject()->getStateClassName() != '' ) {
            $options['state'] = text(2923);
        }
        $options['numbers'] = text(2935);
        $options['comments'] = text(3004);

        $parms['treeoptions'] = array(
            'attribute' => 'multiple',
            'name' => text(2921),
            'options' => $options,
            'value' => preg_split('/-/',$values['treeoptions'])
        );

        $options = array();
        foreach( array(1, 3, 5, 10, 15, 30, 60, 100) as $option ) {
            $options[$option] = $option;
        }

        $parms['viewpages'] = array(
            'name' => text(2924),
            'options' => $options,
            'value' => $values['viewpages']
        );

        return $parms;
    }

	protected function hasToolbar() {
		return $this->itemsEditable;
	}

	function IsNeedNavigator() {
        return false;
    }

    function getNoItemsMessage()
    {
        if ( $this->getTable()->getCompareToSnapshot()->getId() != '' ) {
            return text(2674);
        }
        return parent::getNoItemsMessage();
    }

    function getFilteredReferenceIt( $attr, $value )
    {
        return $this->getObject()->getAttributeObject($attr)->getExact(preg_split('/,/', $value));
    }

    function drawFooter()
    {
        $documentIt = $this->getTable()->getDocumentIt();

        echo '<div class="doc-buttons">';
        if ( $this->previewCount != 1 )
        {
            $method = new ObjectCreateNewWebMethod($this->getObject());
            if ( $method->hasAccess() && $this->getTable()->getEditable() ) {
                $method->setVpd($documentIt->get('VPD'));
                $method->setRedirectUrl('function(jsonText){gotoPageJson(jsonText);}');
                $doc_section_url = $method->url(array(
                    'ParentPage' => $documentIt->getId()
                ));
                ?>
                <div id="new-doc-section" class="btn-group pull-left hidden-print" style="display: none;">
                    <a class="btn append-btn btn-sm btn-success" href="<?=$doc_section_url?>">
                        <i class="icon-plus icon-white"></i> <?=$this->getObject()->getSectionName()?>
                    </a>
                </div>
                <?php
            }
            ?>
            <div id="doc-load-more" class="btn-group pull-left" style="display: none;">
                <a class="btn append-btn btn-sm btn-secondary" href="javascript: buildBottomWaypoint(localOptions);">
                    <i class="icon-forward icon-white"></i> <?= text(2822) ?>
                </a>
            </div>
            <?php
        }
        else {

        }
        echo '</div>';
    }
}