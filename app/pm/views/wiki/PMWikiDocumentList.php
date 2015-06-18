<?php

include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class PMWikiDocumentList extends PMWikiList
{
    private $first_object_it;
    private $revision_it;
    private $visible_ids = array();
    private $trace_source_attribute = '';
    
    function __construct( $object )
    {
    	parent::__construct($object);
    	
    	$this->trace_source_attribute = array_pop($this->getObject()->getAttributesByGroup('source-attribute'));
    }
    
 	function getSorts()
	{
	    return array( new SortDocumentClause() );
	}
    
    function retrieve()
    {
    	global $model_factory;
    	
    	$snapshot_it = $this->getTable()->getCompareToSnapshot();

   		if ( $snapshot_it->getId() != '' && in_array($snapshot_it->get('Type'), array('branch', 'document')) )
   		{
   			$baseline_it = $this->getObject()->getExact($snapshot_it->get('ObjectId'));
   			
   			$registry = new WikiPageRegistryBaseline();
   			
   			$registry->setDocumentIt($this->getTable()->getDocumentIt());
    		
   			$registry->setBaselineIt($baseline_it);

    		$this->getObject()->setRegistry($registry);
   		}
   		elseif ( $snapshot_it->getId() != '' )
   		{
   			$registry = new WikiPageRegistryVersion();
   			
   			$registry->setDocumentIt($this->getTable()->getDocumentIt());

   			$registry->setSnapshotIt($snapshot_it);
   			
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
    		$this->revision_it = $model_factory->getObject('WikiPageChange')->getExact($_REQUEST['revision']);
    		
    		if ( $this->revision_it->getId() > 0 )
    		{
    			$this->getObject()->addPersister( new WikiPageRevisionPersister($this->revision_it) );
    		}    			
    	}
    	
        parent::retrieve();

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
        	
        	if ( $first_pos === false )
        	{
        		$this->visible_ids = array(0);
        	}
        	else
        	{
        		$this->visible_ids = array_slice($ids, $first_pos, array_search($last, $ids) - $first_pos + 1);
        	}
        }

        $page_id = $this->getTable()->getObjectIt()->getId();
        
        $iterator = $this->getObject()->createCachedIterator($this->getIteratorRef()->getRowset());
        
        $iterator->moveToId( $this->getTable()->getObjectIt()->getId() );
        
        $this->first_object_it = $iterator->object->createCachedIterator(
        		array_slice($iterator->getRowset(), $iterator->getPos(), $this->getTable()->getPreviewPagesNumber())
        );
    }
    
 	function getGroupFields()
 	{
 		return array();
 	}
 	
	function getColumns()
	{
		return array($this->trace_source_attribute, 'Content', 'Attributes');
	}
 	
 	function setupColumns()
 	{
 	 	if ( $this->getAttributesVisible() )
 		{
 			$this->getObject()->addAttribute('Attributes', '', translate('Атрибуты'), true);
 		}
 		
 		parent::setupColumns();
 		
 	 	if ( $this->getAttributesVisible() )
 		{
 			$this->getObject()->setAttributeVisible('Attributes', true);
 		}
 	}
    
	function getColumnFields()
	{
		$fields = parent::getColumnFields();
		
		unset($fields[array_search('Attributes', $fields)]);
		unset($fields[array_search('UID', $fields)]);
		unset($fields[array_search('State', $fields)]);
		
		return $fields;
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
		if ( count($this->visible_ids) > 0 && !in_array($object_it->getId(), $this->visible_ids) ) return false;
		
		if ( $_REQUEST['object'] != '' ) return true;
		
	    return in_array($object_it->getId(), $this->first_object_it->idsToArray());
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
		    case $this->trace_source_attribute:
		    	$this->drawSourcePage( $entity_it );
		    	break;
		    	
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
	    global $model_factory;
	    
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

 	        case $this->trace_source_attribute:
 	            return '50%';
 	            
 	        default:
 	            return parent::getColumnWidth( $column );
 	    }
 	}
	
	function drawSourcePage( $object_it )
	{
		while( !$object_it->end() )
		{
			echo '<div style="margin-top: 2px;padding-right:18px;display:table;">';
				echo '<div style="display:table-cell;padding-right:8px;vertical-align: top;">';
					echo $this->getUidService()->getUidIcon($object_it);
				echo '</div>';
				echo '<h4 class="title-cell bs" style="display:table-cell;">';
					echo $object_it->getDisplayName();
				echo '</h4>';
			echo '</div>';
			echo '<div style="margin-top: 11px;padding-right:18px;">';
				$field = new FieldCompareToContent($object_it,$object_it);
				$field->draw();
			echo '</div>';
			$object_it->moveNext();
		}
	}
	
	function drawPageContent( $object_it )
	{
		$this->getTable()->getForm()->setDocumentIt( $this->getTable()->getDocumentIt() );
		
		$this->getTable()->getForm()->setRevisionIt( $this->getTable()->getRevisionIt() );
	    		
		$filter_values = $this->getFilterValues();
		        
		$this->getTable()->getForm()->setFormIndex( $object_it->getId() );

		$this->getTable()->getForm()->show( $object_it->getCurrentIt() );

		$this->getTable()->getForm()->setPage( $this->getTable()->getPage() );
		
		$compareto_it = $this->getTable()->getCompareToSnapshot();
		
		if ( $compareto_it->getId() != '' )
		{
			$registry = new WikiPageRegistryComparison($this->getObject());
			
			$registry->setPageIt($object_it);
			
			$registry->setBaselineIt($compareto_it);

			$this->getTable()->getForm()->setCompareTo($registry->Query());
		}
        		
		if ( $filter_values['viewmode'] != 'view' )
		{
			$form_render_parms['sections'] = array( 
            		new PageSectionComments($object_it, $this->getTable()->getRevisionIt()->getId()) 
			);
		}

		$form_render_parms['modifiable'] = 
        		$filter_values['viewmode'] != 'view' 
        		&& $this->getTable()->getRevisionIt()->getId() < 1 && getFactory()->getAccessPolicy()->can_modify($object_it);
        		
		if ( is_object($this->revision_it) && $this->revision_it->getId() > 0 )
        {
			if ( $this->revision_it->get('WikiPage') == $object_it->getId() )
			{
				$form_render_parms['modifiable'] = false;
        				
				$form_render_parms['revision'] = array (
						'id' => $_REQUEST['revision']
	        		);
        	}
		}
        		
		if (!$form_render_parms['modifiable'] ) $this->getTable()->getForm()->setReadonly();
        		
		$form_render_parms['show_section_number'] = $filter_values['numbering'] == 'display';
        		
		$comment = getFactory()->getObject('Comment');
	    		
		if ( $this->getTable()->getRevisionIt()->getId() > 0 )
		{
			$snapshot = getFactory()->getObject('Snapshot');
	    			
			$comment->addFilter( new SnapshotBeforeDatePredicate($this->getTable()->getRevisionIt()->getId()) );
		}
	    
		$form_render_parms['comments_count'] = $comment->getCount($object_it);
        		
		$this->getTable()->getForm()->setReviewMode();
        		
		$this->getTable()->getForm()->render( $this->getTable()->getView(), $form_render_parms);		
	}
	
	function drawAttributes( $object_it )
	{
		foreach( $this->getObject()->getAttributes() as $key => $attribute )
		{
			if ( in_array($key, $this->getColumns()) ) continue;
			
			if ( !$this->getColumnVisibility($key) ) continue;
			
			echo translate($this->getObject()->getAttributeUserName($key)).': ';
			
			if ( $this->getObject()->IsReference($key) )
			{
				$this->drawRefCell( $object_it->getRef($key), $object_it, $key );
			}
			else
			{
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
		foreach( $this->getObject()->getAttributes() as $key => $attribute )
		{
			if ( $key == "Content" ) continue;
			if ( $key == $this->trace_source_attribute ) continue;
			if ( $this->getColumnVisibility($key) ) return true;
		}
		
		return false;
	}
	
	function getHeaderAttributes( $attr )
	{
		switch ( $attr )
		{
		    case 'Content':

		    	if ( $_REQUEST['tableonly'] != '' ) return; 
		    	
	    		$documents = $this->getPagesWithDifferences($this->getTable()->getCompareToSnapshot());
		    	
		    	$compare_actions = $this->getTable()->getCompareToActions();
		    		
		    	if ( count($documents) + count($compare_actions) > 0 )
		    	{
			    	return array (
			    			'script' => '#',
			    			'name' => $this->getTable()->getView()->render('pm/WikiDocumentHeader.php',
			    							array (
			    									'documents' => $documents,
			    									'actions' => $compare_actions
			    							)
			    					  )
			    	);
		    	}
		    	else
		    	{
			    	return array (
			    			'script' => '#',
			    			'name' => translate($this->getObject()->getAttributeUserName($attr))
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
		
 	function getRenderParms()
 	{
		$parent_parms = parent::getRenderParms();

		return array_merge( $parent_parms, array (
		    'table_class_name' => 'table-document',
			'scrollable' => $this->getTable()->getPreviewPagesNumber() > 1,
			'reorder' => true
 	    ));
 	}
}