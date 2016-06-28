<?php

include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class PMWikiDocumentList extends PMWikiList
{
    private $revision_it;
    private $visible_ids = array();
    private $trace_source_attribute = array();
    private $data_filter_used = false;
	private $displayContentHeader = false;

    function __construct( $object )
    {
    	parent::__construct($object);
    	$this->trace_source_attribute = $this->getObject()->getAttributesByGroup('source-attribute');
    }
    
 	function getSorts()
	{
	    return array( new SortDocumentClause() );
	}
    
    function retrieve()
    {
    	$snapshot_it = $this->getTable()->getCompareToSnapshot();

   		if ( $snapshot_it->getId() != '' && in_array($snapshot_it->get('Type'), array('branch','document')) ) {
   			$registry = new WikiPageRegistryBaseline();
   			$registry->setDocumentIt($this->getTable()->getDocumentIt());
   			$registry->setBaselineIt($this->getObject()->getExact($snapshot_it->get('ObjectId')));
    		$this->getObject()->setRegistry($registry);
   		}
   		elseif ( $snapshot_it->getId() != '' && $snapshot_it->object instanceof WikiPageComparableSnapshot ) {
   			$registry = new WikiPageRegistryVersion();
   			$registry->setDocumentIt($this->getTable()->getDocumentIt());
   			$registry->setSnapshotIt($snapshot_it);
    		$this->getObject()->setRegistry($registry);
   		}
		elseif ( $snapshot_it->getId() != '' ) {
			$registry = new WikiPageRegistryBaseline();
			$registry->setDocumentIt($this->getTable()->getDocumentIt());
			$registry->setBaselineIt($snapshot_it);
			$this->getObject()->setRegistry($registry);
		}

    	$version_it = $this->getTable()->getRevisionIt();
    	if ( $version_it->getId() > 0 )
    	{
    		$registry = new WikiPageRegistryVersion();
    		$registry->setDocumentIt($this->getTable()->getDocumentIt());
    		$registry->setSnapshotIt($version_it);
	    	$this->getObject()->setRegistry($registry);
    	}

		if ( $_REQUEST['revision'] > 0 )
    	{
    		$this->revision_it = getFactory()->getObject('WikiPageChange')->getExact($_REQUEST['revision']);
    		if ( $this->revision_it->getId() > 0 ) {
    			$this->getObject()->addPersister( new WikiPageRevisionPersister($this->revision_it) );
    		}    			
    	}

        foreach( $this->trace_source_attribute as $attribute ) {
            if ( $this->getColumnVisibility($attribute) ) {
                $this->getObject()->addPersister( new WikiPageTracesRevisionsPersister() );
                break;
            }
        }

        return parent::retrieve();
    }
    
 	function getGroupFields()
 	{
 		return array();
 	}
 	
	function getColumns()
	{
		return array_merge(array_merge(array('Content'), $this->trace_source_attribute), array('Attributes'));
	}
 	
 	function setupColumns()
 	{
 	 	if ( $this->getAttributesVisible() ) {
 			$this->getObject()->addAttribute('Attributes', '', translate('Атрибуты'), true);
 		}
 		
 		parent::setupColumns();
 		
 	 	if ( $this->getAttributesVisible() ) {
 			$this->getObject()->setAttributeVisible('Attributes', true);
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
					return '45%';
				}
 	            return parent::getColumnWidth( $column );
 	    }
 	}
	
	function drawSourcePage( $entity_it, $object_it, $attr, $revisions )
	{
		$registry = new ObjectRegistrySQL($entity_it->object);

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
			$source_it = $registry->Query(
				array (
					new FilterInPredicate($entity_it->getId()),
					new DocumentVersionPersister()
				)
			);
			if ( $baselines[$source_it->getId()] != '' ) {
				$source_it = $registry->Query(
					array (
						new FilterInPredicate($source_it->getId()),
						new DocumentVersionPersister(),
						new SnapshotItemValuePersister($baselines[$source_it->getId()])
					)
				);
			}

            $compare_to = $source_it;
            foreach( $revisions as $revision ) {
                if ( $revision[$entity_it->getId()] == '' ) continue;
				$compare_to = $registry->Query(
					array (
						new FilterInPredicate($entity_it->getId()),
						new DocumentVersionPersister(),
						new WikiPageRevisionPersister($revision[$entity_it->getId()])
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
                    echo '<h4 class="title-cell bs" style="display:table-cell;">';
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
		$form = $this->getTable()->getForm();

		$form->setDocumentIt( $this->getTable()->getDocumentIt() );
		$form->setRevisionIt( $this->getTable()->getRevisionIt() );
		$form->setFormIndex( $object_it->getId() );
		$form->show( $object_it->getCurrentIt() );
		$form->setPage( $this->getTable()->getPage() );

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
        		
		if ( $filter_values['viewmode'] != 'view' )
		{
			$form_render_parms['sections'] = array( 
            		new PageSectionComments($object_it, $this->getTable()->getRevisionIt()->getId()) 
			);
		}

		$form_render_parms['modifiable'] =
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
        		
		$form_render_parms['show_section_number'] = $this->getColumnVisibility('SectionNumber');

		$comment = getFactory()->getObject('Comment');
		if ( $this->getTable()->getRevisionIt()->getId() > 0 )
		{
			$snapshot = getFactory()->getObject('Snapshot');
	    			
			$comment->addFilter( new SnapshotBeforeDatePredicate($this->getTable()->getRevisionIt()->getId()) );
		}
	    
		$form_render_parms['comments_count'] = $comment->getCountForIt($object_it);
        		
		$form->setReviewMode();

		ob_start();
		$this->drawTraceAttributes($object_it);
		$form_render_parms['traces_html'] = ob_get_contents();
		ob_end_clean();

		$form->render( $this->getTable()->getView(), $form_render_parms);
	}

	function getColumnVisibility($attr) {
		return $attr == 'Content' ? true : parent::getColumnVisibility($attr);
	}

	function drawTraceAttributes( $object_it )
	{
		$sourceAttributes = $this->getObject()->getAttributesByGroup('source-attribute');
		$traces = array_intersect(
			array_keys($this->getObject()->getAttributesSorted()),
			$this->getObject()->getAttributesByGroup('trace')
		);

		$items = '';
		foreach( $traces as $key => $attribute ) {
			$visible = $this->getColumnVisibility($attribute);
			if ( !$visible && !in_array($attribute,$sourceAttributes) || $visible && in_array($attribute,$sourceAttributes) ) {
				unset($traces[$key]);
				continue;
			}
			$items .= $object_it->get($attribute);
		}
		if ( $items == '' ) return;

		echo '<div class="well well-small well-traces hover-holder hidden-print">';
			echo '<ul class="inline">';
				echo '<li><i class="icon-random"></i></li>';
				foreach( $traces as $attribute ) {
					if ( $object_it->get($attribute) == '' ) continue;
					if ( $object_it->object->IsReference($attribute) ) {
						$ref_it = $this->getFilteredReferenceIt($attribute, $object_it->get($attribute));
						if ( $ref_it->count() < 1 ) continue;

						echo '<li>';
						echo $this->getObject()->getAttributeUserName($attribute).': ';
						parent::drawRefCell($ref_it, $object_it, $attribute);
						echo '</li>';
					}
					else {
						echo '<li>';
						echo $this->getObject()->getAttributeUserName($attribute).': ';
						parent::drawCell($object_it, $attribute);
						echo '</li>';
					}
				}
				$method = new ObjectModifyWebMethod($object_it);
				if ( $method->hasAccess() ) {
					$method->setRedirectUrl('donothing');
					echo '<li>';
						echo '<a class="dashed dashed-hidden" onclick="'.$method->getJSCall(array('tab'=>2)).'">'.translate('изменить').'</a>';
					echo '</li>';
				}
		echo '</ul>';
		echo '</div>';
	}

	function drawAttributes( $object_it )
	{
		$trace = array_merge(
			$this->getObject()->getAttributesByGroup('trace'),
			array (
				'SectionNumber'
			)
		);
		foreach( $this->getObject()->getAttributes() as $key => $attribute )
		{
			if ( in_array($key, $this->getColumns()) ) continue;
			if ( in_array($key, $trace) ) continue;
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
	
	function getPagesWithDifferences( $compareto_it )
	{
		if ( $compareto_it->getId() == '' ) return array();

		$iterator = $this->getObject()->createCachedIterator( $this->getIteratorRef()->getRowset() );
		
		$ids = array();
		
		while( !$iterator->end() )
		{
			$registry = new WikiPageRegistryComparison($this->getObject());
	
			$registry->setBaselineIt($compareto_it);

			$registry->setPageIt($iterator);
			
			$page_it = $registry->Query();
			
	 		$result = IteratorBase::utf8towin(
	 				html_diff(
	 						IteratorBase::wintoutf8($page_it->getHtmlDecoded('Content')),
	 						IteratorBase::wintoutf8($iterator->getHtmlDecoded('Content'))
					)
	 		);  
			
			$different = 
					preg_match('/class="diff-html/i', $result) 
					|| $page_it->getHtmlDecoded('Caption') != $iterator->getHtmlDecoded('Caption');
			
			if ( $different )
			{
				$ids[] = $iterator->getId();
			}
			
			$iterator->moveNext();
		}
		
		return $ids;
	}
	
	function getAttributesVisible()
	{
		$trace = $this->getObject()->getAttributesByGroup('trace');
		foreach( $this->getObject()->getAttributes() as $key => $attribute ) {
			if ( in_array($key, array("Content","SectionNumber")) ) continue;
			if ( in_array($key, $this->trace_source_attribute) ) continue;
			if ( in_array($key, $trace) ) continue;
			if ( $this->getColumnVisibility($key) ) return true;
		}
		return false;
	}
	
	function getHeaderAttributes( $attr )
	{
		switch ( $attr )
		{
		    case 'Content':
		    	if ( $_REQUEST['tableonly'] != '' || $this->getTable()->dataFilterApplied() ) {
					return array (
						'script' => '#',
						'name' => $this->displayContentHeader ? translate('Документ') : ''
					);
				}
		    	
	    		$documents = $this->getPagesWithDifferences($this->getTable()->getCompareToSnapshot());
		    	$compare_actions = $this->getTable()->getCompareToActions();
		    	if ( count($documents) + count($compare_actions) > 0 )
		    	{
					$parms = array (
						'documents' => $documents,
						'actions' => $compare_actions
					);

					$widget = $this->getTable()->getBaselinesListWidget();
					if ( is_object($widget) ) {
						$parms['baselines_widget'] = array (
							'name' => text(2161),
							'url' => $widget->getUrl()
						);
					}


					return array (
						'script' => '#',
						'name' => $this->getTable()->getView()->render('pm/WikiDocumentHeader.php', $parms)
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
	
	function getItemActions( $column_name, $object_it ) 
	{
		return array();
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

        $this->data_filter_used = $this->getTable()->dataFilterApplied();

		$filter_values = $this->getFilterValues();
		$this->itemsEditable =
			$filter_values['viewmode'] != 'view'
			&& $filter_values['search'] == ''
			&& $this->getTable()->getRevisionIt()->getId() < 1
			&& $this->getTable()->getCompareToSnapshot()->getId() < 1;

        if ( $_REQUEST['doc-visible-ids'] != '' )
        {
            list($first, $last) = preg_split('/,/', $_REQUEST['doc-visible-ids']);

            $object = getFactory()->getObject(get_class($this->getObject()));
            $object->resetPersisters();

            $ids = $object->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('DocumentId', $this->getTable()->getDocumentIt()->getId()),
                    new SortDocumentClause()
                )
            )->idsToArray();

            $first_pos = array_search($first, $ids);
            if ( $first_pos === false ) {
                $this->visible_ids = array(0);
            }
            else {
                $this->visible_ids = array_slice($ids, $first_pos, array_search($last, $ids) - $first_pos + 1);
            }
        }
        else {
            if ( $_REQUEST['object'] != '' || $this->data_filter_used ) {
                $this->visible_ids = array();
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
			'toolbar' => $this->hasToolbar(),
			'visible_pages' => $this->getTable()->getPreviewPagesNumber()
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
}