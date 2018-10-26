<?php

include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class PMWikiDocumentList extends PMWikiList
{
    private $revision_it;
    private $visible_ids = array();
    private $trace_source_attribute = array();
    private $trace_attributes = array();
    private $data_filter_used = false;
	private $displayContentHeader = false;
    private $attributesVisible = false;
    private $workflowVisible = false;
    private $attributeFields = array();

    function __construct( $object )
    {
    	parent::__construct($object);
    	$this->trace_source_attribute = $this->getObject()->getAttributesByGroup('source-attribute');
        $this->trace_attributes = $this->getObject()->getAttributesByGroup('trace');
    }
    
 	function getSorts() {
	    return array( new SortDocumentClause() );
	}

	function extendModel()
    {
        parent::extendModel();
        $this->getObject()->addAttribute('Attributes', 'TEXT', translate('Атрибуты'), true);

        $this->pageForm = $this->getTable()->getForm();
        $this->pageForm->setObject( $this->getTable()->getPage()->buildObject() );

        $values = $this->getFilterValues();
        if ( $values['viewmode'] == 'recon' ) {
            $this->workflowVisible = true;
        }
    }

    protected function getPersisters( $object, $sorts )
    {
        $persisters = parent::getPersisters($object, $sorts);

        $version_it = $this->getTable()->getRevisionIt();
        if ( $version_it->getId() > 0 ) {
            $registry = new WikiPageRegistryVersion();
            $registry->setDocumentIt($this->getTable()->getDocumentIt());
            $registry->setSnapshotIt($version_it);
            $object->setRegistry($registry);
        }

        $snapshot_it = $this->getTable()->getCompareToSnapshot();

        if ( $snapshot_it->getId() != '' && $version_it->getId() > 0 ) {
            $registry->setComparisonMode();
        }
        elseif ( $snapshot_it->getId() != '' && in_array($snapshot_it->get('Type'), array('branch','document')) ) {
            $registry = new WikiPageRegistryBaseline();
            $registry->setDocumentIt($this->getTable()->getDocumentIt());
            $registry->setBaselineIt($object->getExact($snapshot_it->get('ObjectId')));
            $object->setRegistry($registry);
        }
        elseif ( $snapshot_it->getId() != '' && $snapshot_it->object instanceof WikiPageComparableSnapshot ) {
            // previous structure should be used to show differences
            $registry = new WikiPageRegistryVersionStructure();
            $registry->setDocumentIt($this->getTable()->getDocumentIt());
            $registry->setSnapshotIt($snapshot_it);
            $object->setRegistry($registry);
        }
        elseif ( $snapshot_it->getId() != '' ) {
            $registry = new WikiPageRegistryBaseline();
            $registry->setDocumentIt($this->getTable()->getDocumentIt());
            $registry->setBaselineIt($snapshot_it);
            $object->setRegistry($registry);
        }

        if ( $_REQUEST['revision'] > 0 )
        {
            $this->revision_it = getFactory()->getObject('WikiPageChange')->getExact($_REQUEST['revision']);
            if ( $this->revision_it->getId() > 0 ) {
                $persisters[] = new WikiPageRevisionPersister($this->revision_it);
            }
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
            case 'Attributes':
                return $this->attributesVisible;
            case 'Workflow':
                return $this->workflowVisible || parent::getColumnVisibility($attr);
            default:
                return parent::getColumnVisibility($attr);
        }
    }

	function getColumnFields()
	{
		return array_filter(parent::getColumnFields(), function($value) {
			return !in_array($value, array (
                'Attributes',
				'UID',
				'State',
				'RecentComment',
				'DocumentId',
				'Watchers',
				'Caption',
				'Attachments',
				'Project',
				'ParentPage'
			));
		});
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
            || in_array('source-attribute', $this->getObject()->getAttributeGroups($attribute));
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

		    case 'Attributes':
	    		$this->drawAttributes( $object_it );
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
						echo $entity_it->getHtmlDecoded('Description');
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
                    $field = new FieldCompareToContent($source_it,$compare_to);
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
        $form->setObject(getFactory()->getObject(get_class($form->getObject())));
		$form->show( $form->getObject()->createCachedIterator(array($object_it->getData())) );
		$form->setPage( $this->getTable()->getPage() );
        $form->buildForm();

        if ( in_array($filter_values['search'], array('','all','none')) ) {
            $filter_values['search'] = '';
        }
        $form->setSearchText($filter_values['search']);

		$compareto_it = $this->getTable()->getCompareToSnapshot();
		if ( $compareto_it->getId() != '' )
		{
			$registry = new WikiPageRegistryComparison($this->getObject());
			$registry->setPageIt($object_it);
			$registry->setBaselineIt($compareto_it);
			$form->setCompareTo($registry->Query());
		}

		$viewMode = $filter_values['viewmode'];
        if ( $viewMode == '' ) {
            $viewMode = $_REQUEST['viewmode'];
        }

        $section = new PageSectionComments($object_it, $this->getTable()->getRevisionIt()->getId());
        $section->setOptions(
            array (
                'collapsable' => true,
                'autorefresh' => false
            )
        );
        $form_render_parms['sections'] = array( $section );

		$form_render_parms['modifiable'] = $viewMode != 'view' &&
			$this->itemsEditable && getFactory()->getAccessPolicy()->can_modify($object_it);

		$revision = $object_it->get('RevisionId');
		if ( $revision == '' && is_object($this->revision_it) && $this->revision_it->get('WikiPage') == $object_it->getId() ) {
			$revision = $this->revision_it->getId();
		}
		if ( $revision > 0 ) {
			$form_render_parms['modifiable'] = false;
			$form_render_parms['revision'] = array ( 'id' => $revision );
		}
        		
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
        		
		$form->setReviewMode();

		if ( $this->getTable()->getRevisionIt()->getId() == '' ) {
            ob_start();
            $this->drawTraceAttributes($object_it);
            $form_render_parms['traces_html'] = ob_get_contents();
            ob_end_clean();
        }

		$form->render( $this->getRenderView(), $form_render_parms);
	}

	function drawTraceAttributes( $object_it )
	{
		$traces = array_intersect(
			array_keys($this->getObject()->getAttributes()),
            array_merge(
                $this->getObject()->getAttributesByGroup('trace'),
                $this->getObject()->getAttributesByGroup('source-attribute')
            )
		);

		$items = '';
		foreach( $traces as $key => $attribute ) {
			$items .= $object_it->get($attribute);
		}

		echo '<div class="well well-small well-traces hover-holder hidden-print '.($items == '' ? 'hidden' : '').'">';
		    echo '<div class="wel-tbl">';
                echo '<div class="lf-pn">';
                    echo '<ul class="inline">';
                        $firstItem = true;
                        foreach( $traces as $attribute ) {
                            if ( $object_it->get($attribute) == '' ) continue;
                            if ( $attribute == 'Dependency' ) {
                                $items = preg_split('/,/', $object_it->get($attribute));
                                $objects = array();
                                foreach( $items as $item ) {
                                    list($className, $objectId) = preg_split('/:/', $item);
                                    $objects[$className][] = $objectId;
                                }
                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random"></i>';
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
                            else if ( $object_it->object->IsReference($attribute) ) {
                                $ref_it = $this->getFilteredReferenceIt($attribute, $object_it->get($attribute));
                                if ( $ref_it->count() < 1 ) continue;

                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random"></i>';
                                    $firstItem = false;
                                }
                                echo translate($this->getObject()->getAttributeUserName($attribute)).': ';
                                parent::drawRefCell($ref_it, $object_it, $attribute);
                                echo '</li>';
                            }
                            else {
                                echo '<li>';
                                if ( $firstItem ) {
                                    echo '<i class="icon-random"></i>';
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
                $method->setRedirectUrl('donothing');
                if ( $method->hasAccess() ) {
                    echo '<div class="rg-pn">';
                        echo '<a id="doc-page-trace" class="dashed" onclick="'.$method->getJSCall(array('tab'=>2)).'">'.translate('трассировки').'</a>';
                    echo '</div>';
                }
            echo '</div>';
		echo '</div>';
	}

	function drawAttributes( $object_it )
	{
		foreach( $this->getObject()->getAttributes() as $key => $attribute )
		{
			if ( !in_array($key, $this->attributeFields) ) continue;
			if ( !$this->getColumnVisibility($key) ) continue;
			if ( $object_it->get($key) == '' ) continue;

			echo translate($this->getObject()->getAttributeUserName($key)).': ';
			
			if ( $this->getObject()->IsReference($key) ) {
				$this->drawRefCell( $object_it->getRef($key), $object_it, $key );
			}
			else {
				$this->drawCell( $object_it, $key );
			}
			echo '<br/>';
		}
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

                    $widget = $this->getTable()->getBaselinesListWidget();
                    if ( is_object($widget) ) {
                        $parms['baselines_widget'] = array (
                            'name' => text(2161),
                            'url' => $widget->getUrl()
                        );
                    }

					if ( $this->getTable()->getCompareToSnapshot()->getId() != '' ) {
                        $parms['reset_comparison'] = array (
                            'name' => text(1710),
                            'url' => "javascript: window.location = updateLocation('compareto=', window.location.toString());"
                        );

                        $actions = $this->getForm()->getReintegrateActions( $this->getTable()->getDocumentIt(), $this->getTable()->getCompareToSnapshot() );
                        if ( count($actions) > 0 ) {
                            ob_start();
                            ?>
                            <div class="btn-group"></div>
                            <div class="btn-group operation">
                                <a tabindex="-1" class="btn btn-sm btn-warning dropdown-toggle actions-button" data-toggle="dropdown" href="#">
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

	function getScrollable()
	{
		return $this->getTable()->getPreviewPagesNumber() > 1;
	}

 	function getRenderParms()
 	{
        $parent_parms = parent::getRenderParms();

        $visibleColumns = array_merge(array('Content','Attributes'), $this->trace_source_attribute);
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
                    'PageType'
                )
            );

        $this->data_filter_used = $this->getTable()->dataFilterApplied();
        $this->attributesVisible = $this->getAttributesVisible();

		$filter_values = $this->getFilterValues();
		$this->itemsEditable =
			$filter_values['viewmode'] != 'view'
			&& in_array($filter_values['search'], array('','all','hide'))
			&& $this->getTable()->getRevisionIt()->getId() < 1
			&& $this->getTable()->getCompareToSnapshot()->getId() < 1
            && getFactory()->getAccessPolicy()->can_modify($this->getObject());


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
                $this->getTable()->getPreviewPagesNumber()
            );
        }

		$attributes = array_merge(
			$this->getObject()->getAttributesByGroup('source-attribute'),
			array (
				'Attributes'
			)
		);
		foreach( $attributes as $attribute ) {
			if ( $this->getColumnVisibility($attribute) ) {
				$this->displayContentHeader = true;
				break;
			}
		}

		return array_merge( $parent_parms, array (
		    'table_class_name' => 'table-document',
			'reorder' => true,
			'draggable' => false,
			'toolbar' => $this->hasToolbar(),
			'visible_pages' => $this->getTable()->getPreviewPagesNumber(),
            'columns' => array_filter($parent_parms['columns'], function($value) use ($visibleColumns) {
                                return in_array($value, $visibleColumns);
                            })
 	    ));
 	}

	function buildFilterActions( &$base_actions )
	{
		parent::buildFilterActions( $base_actions );

		foreach ( $base_actions as $key => $action ) {
			if ( $action['uid'] == 'columns' ) {
				$base_actions[$key]['name'] = translate('Атрибуты');
				break;
			}
		}
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
}