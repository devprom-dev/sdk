<?php

include SERVER_ROOT_PATH."pm/classes/wiki/WikiPageComparableSnapshot.php";

include "PMWikiDocumentList.php";
include "DocumentMode.php";
include "DocumentSectionNumberingMode.php";

class PMWikiDocument extends PMWikiTable
{
 	private $object_it = null;
 	
 	private $document_it = null;
 	
 	private $revision_it = null;
 	
 	private $compareto_it = null;
 	
    function getDocumentIt()
	{
	    if ( is_object($this->document_it) )
	    {
	    	return $this->getObject()->createCachedIterator($this->document_it->getRowset());
	    }
	    
	    return $this->document_it = $this->buildDocumentIt();
	}
	
	protected function buildDocumentIt()
	{
		if ( !in_array($_REQUEST['document'], array('', 'all')) )
	    {
        	return $this->getObject()->getExact($_REQUEST['document']);    
	    }
	    else
	    {
	        return $this->getObject()->getEmptyIterator();
	    }
	}
	
	function getObjectIt()
	{
	    if ( is_object($this->object_it) ) return $this->object_it->copy();
	    
	    $key = 'page';
	    
	    if ( $_REQUEST[$key] != '' )
	    {
	        $this->object_it = $this->getObject()->getExact($_REQUEST[$key]);
	    }
	    else
	    {
	        $this->object_it = $this->getObject()->getEmptyIterator();
	    }
	    
	    return $this->object_it;
	}
	
	function getPreviewPagesNumber()
	{
		return 3;
	}
	
	function & getRevisionIt()
	{
		global $model_factory;
		
	    if ( is_object($this->revision_it) ) return $this->revision_it;
	    
	    $baseline = $model_factory->getObject('Snapshot');
	    
	    $values = $this->getFilterValues();

 		if ( in_array($values['baseline'], array('', 'none', 'all')) )
 		{
 			$this->revision_it = $baseline->getEmptyIterator();
 		}
 		else
 		{
 			$this->revision_it = $baseline->getExact($values['baseline']);
 		}
 		
 		return $this->revision_it;
	}
	
	function getFiltersName()
	{
        return parent::getFiltersName().'-'.$this->getDocumentIt()->getId();
	}
	
	function getFilterParms()
	{
		return array_merge( parent::getFilterParms(), array( 'baseline' ));
	}
	
	function getFilters()
	{
	    $parent_filters = parent::getFilters();
	    
	    foreach( $parent_filters as $key => $filter )
	    {
	        if ( is_a($filter, 'FilterStateMethod') ) $filter->setDefaultValue('all');
	        
	        if ( $filter->getValueParm() == 'document' )
	        {
	        	unset($parent_filters[$key]);
	        	
	        	$parent_filters = array_values($parent_filters);
	        }
	    }
	    
		return array_merge( 
		        array_slice($parent_filters, 0, 1),
		        array ( 
		                $this->buildViewModeFilter(),
		                $this->buildSectionNumberingModeFilter()
		              ), 
		        array_slice($parent_filters, 1)
		       );
	}
	
	function getFilterPredicates()
	{
		$predicates = array ( 
		    new FilterAttributePredicate('DocumentId', $this->getDocumentIt()->idsToArray())
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
	
	
	function buildViewModeFilter()
	{
	    $mode_filter = new FilterObjectMethod( new DocumentMode(), '', 'viewmode' );
	    
	    $mode_filter->setIdFieldName('ReferenceName');
	    $mode_filter->setHasAll( false );
	    $mode_filter->setHasNone( false );
	    $mode_filter->setType( 'singlevalue' );
	    
	    $mode_filter->setFilter( $this->getFiltersName() );
	    
	    return $mode_filter;
	}	
	
	function buildSectionNumberingModeFilter()
	{
	    $mode_filter = new FilterObjectMethod( new DocumentSectionNumberingMode(), '', 'numbering' );
	    
	    $mode_filter->setIdFieldName( 'ReferenceName' );
	    $mode_filter->setHasAll( false );
	    $mode_filter->setType( 'singlevalue' );
	    $mode_filter->setNoneTitle( translate('Выкл.') );
	    
	    $mode_filter->setFilter( $this->getFiltersName() );
	    
	    return $mode_filter;
	}	
	
	function getTemplate()
	{
	    return 'pm/WikiDocument.php';
	}
	
	function getCompareToSnapshot()
	{
		if ( is_object($this->compareto_it) ) return $this->compareto_it;
	 
		$snapshot = new WikiPageComparableSnapshot($this->getDocumentIt());
		
		if ( !in_array($_REQUEST['compareto'], array('', 'none', 'all')) )
		{ 
			$snapshot_it = $snapshot->getExact($_REQUEST['compareto']);
			
			if ( $snapshot_it->getId() != '' ) return $this->compareto_it = $snapshot_it;
		}
    			
		$matches = array();
		
		if( preg_match('/document:(\d+)/', $_REQUEST['compareto'], $matches) )
		{
			return $this->compareto_it = $this->getObject()->getExact($matches[1]);
		}
		
    	return $snapshot->getEmptyIterator();
	}
	
	function getCompareToActions()
	{
		$snapshot = new WikiPageComparableSnapshot($this->getDocumentIt());
		
		$snapshot_it = $snapshot->getAll();
		
		$actions = array();
		
		$baselines = array();
		
		$selected = $this->getCompareToSnapshot()->getId();
		
		$title = text(1566);

		while( !$snapshot_it->end() )
		{
			if ( $snapshot_it->getId() != $this->getRevisionIt()->getId() )
			{
				$actions[] = array (
						'name' => $snapshot_it->getDisplayName(),
						'url' => "javascript: window.location = updateLocation('compareto=".$snapshot_it->getId()."', window.location.toString());"
				);
			}
			
			if ( $selected == $snapshot_it->getId() )
			{
				$title .= ": ".$snapshot_it->getDisplayName();
				
				if ( mb_strlen($title) > 30 ) $title = mb_substr($title, 0, 30).'...';
			}
			
			if ( $snapshot_it->get('Type') == 'branch' || strpos($snapshot_it->getId(), 'document') !== false )
			{
				$doc_it = $this->getObject()->getRegistry()->Query( 
						array ( 
								new FilterInPredicate($snapshot_it->get('ObjectId')) 
						)
					);
				
				$baseline_url = "javascript: window.location = '".$doc_it->getViewUrl()."';";
				$baseline_title = translate('Бейзлайн').': '.$snapshot_it->getDisplayName();
			}
			else
			{
				$baseline_url = "javascript: window.location = updateLocation('baseline=".$snapshot_it->getId()."', window.location.toString());";
				$baseline_title = translate('Версия').': '.$snapshot_it->getDisplayName();

				if ( $this->getRevisionIt()->getId() == $snapshot_it->getId() )
				{
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
			$document_title = translate('Бейзлайн').': '.$this->getDocumentIt()->getDisplayName();
			
			if ( $this->getDocumentIt()->getId() == $selected )
			{
				$title = $document_title;
			}
			
			$actions[] = array (
				'name' => $document_title,
				'url' => "javascript: window.location = updateLocation('compareto=document:".$this->getDocumentIt()->getId()."', window.location.toString());"
			);
		}
		
		if ( count($actions) > 0 && $selected != '' )
		{
			$actions[] = array();
			
			$actions[] = array (
					'name' => text(1710),
					'url' => "javascript: window.location = updateLocation('compareto=', window.location.toString());"
			);
		}
		
		if ( $this->getDocumentIt()->get('DocumentVersion') != '' )
		{
			$baseline_title = translate('Бейзлайн').': '.$this->getDocumentIt()->get('DocumentVersion');
			
			$baselines[] = array (
				'name' => $baseline_title,
				'url' => "javascript: window.location = updateLocation('baseline=', window.location.toString());"
			);
		}
		elseif ( count($baselines) > 0 )
		{
			$baseline_title = translate('Бейзлайн').': '.$this->getDocumentIt()->getDisplayName();
			
			$baselines[] = array (
				'name' => $baseline_title,
				'url' => "javascript: window.location = updateLocation('baseline=', window.location.toString());"
			);
		}

		if ( $baseline_selected == "" ) $baseline_selected = $baseline_title;
		
		if ( mb_strlen($baseline_selected) > 30 ) $baseline_selected = mb_substr($baseline_selected, 0, 30).'...';
		
		if ( count($baselines) < 1 && count($actions) < 1 )
		{
			return array();
		}
		else
		{
			return array ( 
					array (
						'name' => $baseline_selected,
						'class' => $baseline_selected != translate('Версия') ? 'btn-info' : "btn",
						'items' => $baselines,
						'uid' => 'baseline'
					),
					array (
						'name' => $title,
						'class' => $selected != '' ? 'btn-info' : "btn",
						'items' => $actions,
						'uid' => 'compareto'
					)
			);
		}
	}
	
 	function getRenderParms( $parms )
 	{
		$parent_parms = parent::getRenderParms( $parms );

		$form_parms = $this->getForm()->getRenderParms();
		
		return array_merge( $parent_parms, array (
 	        'scripts' => $form_parms['scripts'],
		    'object_id' => $this->getObjectIt()->getId() > 0 ? $this->getObjectIt()->getId() : $this->getDocumentIt()->getId()
 	    ));
 	}
 	
	function getList( $type = '', $iterator = null )
	{
	    $list = new PMWikiDocumentList( $this->getObject(), $iterator );
	    
	    $list->setInfiniteMode();
	    
	    return $list;
	}
 	
	function getNewActions()
	{
 		if ( $this->getRevisionIt()->getId() > 0 ) return array();
		
 		$actions = parent::getNewActions();
	    
	    foreach( $actions as $key => $action )
	    {
	        $actions[$key]['url'] .= '&ParentPage='.$this->getDocumentIt()->getId();
	    }
	    
	    return $actions;
	}
	
	function getTraceActions()
	{
		return $this->getForm()->getTraceActions( $this->getDocumentIt() );
	}
	
	function getActions()
	{
		$actions = array();
		
 		if ( $this->getRevisionIt()->getId() > 0 )
 		{
 			return array_merge($this->getExportActions(), $this->getVersioningActions());
 		}
 		
 		$temp_actions = $this->getExportActions();
 		
 		if ( count($temp_actions) > 0 )
 		{
 			$actions[] = array (
					'name' => translate('Экспорт'),
 					'items' => $temp_actions,
 					'uid' => 'export'
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
 		
 		if ( count($temp_actions) > 0 )
 		{
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
		        'name' => translate('История изменений'),
				'url' => $history_url,
		        'uid' => 'history'
		);
			
 		if ( $this->getRevisionIt()->getId() > 0 )
 		{
			return $actions;
 		}
 		
		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		
		$actions[] = array (
                'name' => translate('Просмотр'),
                'url' => $this->getDocumentIt()->getViewUrl().'&viewmode=view'
        );
		
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
		return array();
		
 		if ( $this->getRevisionIt()->getId() > 0 )
 		{
 		 	$method = new DeleteObjectWebMethod($this->getRevisionIt());
			
			if ( $method->hasAccess() )
			{
			    $actions[] = array(
				    'name' => text(1738), 
			    	'url' => $method->getJSCall(),
			    	'title' => text(1563) 
			    );
			}
			
			return $actions;
 		}
 		else
 		{
 			$this->getForm()->show($this->getDocumentIt());
 			
			return $this->getForm()->getDeleteActions();
 		}
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
		$title = $this->getDocumentIt()->getDisplayName();
		
 		if ( $this->getRevisionIt()->getId() > 0 )
 		{
 			$title .= ' [rev - '.$this->getRevisionIt()->getDisplayName().']'; 
 		}
 		
 		return $title;
	}
	
	function getId()
	{
		return join(':', array(get_class($this->getObject()), $this->getDocumentIt()->getId()));
	}
	
 	function drawFooter()
 	{
 	}	
}