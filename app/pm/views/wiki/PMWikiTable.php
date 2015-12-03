<?php

include "PMWikiList.php";
include "PMWikiChart.php";

class PMWikiTable extends PMPageTable
{
    private $state_object = null;
    
    private $form = null;
    
 	function __construct( $object, $state_object, $form )
 	{
 	    $this->state_object = $state_object;
 	    
 	    $this->form = $form;

 	    parent::__construct( $object );
 	}
 	
 	function getForm()
 	{
 	    return $this->form;
 	}
 	
	function getStateObject()
	{
	    return $this->state_object;
	}
	
 	function getSortAttributeClause( $field )
	{
		$parts = preg_split('/\./', $field);
		
		if ( $parts[0] == 'DocumentId' ) return new SortDocumentClause();
		
		return parent::getSortAttributeClause( $field );
	}
	
 	function Statable( $object = null )
	{
		if ( is_object($object) )
		{
			return $object->IsStatable();
		}
		
		return false;
	}
	
	function getList( $type = '', $iterator = null )
	{
		switch ( $type )
		{
			case 'chart':
		 		return new PMWikiChart( $this->getObject(), $iterator );

			case 'files':
		 		return new FilesWikiPagesList( $this->getObject() );
		 		
			case 'templates':
				return new WikiTemplateList( $this->getObject() );
				 
		 	default:
		 		return new PMWikiList( $this->getObject() );
		}
	}
	
	function getFiltersName()
	{
        return md5($_REQUEST['view'].parent::getFiltersName());
	}
	
	function getFilters()
	{
		$object = $this->getObject();
		
		$parent_filter = new FilterAutoCompleteWebMethod( 
			$object, translate($object->getAttributeUserName( 'ParentPage' )) 
		);
		$parent_filter->setValueParm( 'parentpage' );

		$document = getFactory()->getObject(get_class($object));
		$document->addFilter( new WikiRootFilter() );
		$document_filter = new FilterObjectMethod( $document, translate('Документ'), 'document' );
		$document_filter->setType( 'singlevalue' );
		$document_filter->setUseUid( true );
		
		$filters = array( $document_filter );
		if ( is_a($this->getStateObject(), 'StateBase') )
		{
		    $filters[] = new FilterStateMethod( $this->getStateObject() );
		    $filters[] = new FilterStateTransitionMethod( $this->getStateObject() );
		}
		
		$filters[] = new WikiFilterActualLinkWebMethod();
		$filters[] = new ViewWikiModifiedAfterDateWebMethod();
		$filters[] = new ViewWikiTagWebMethod( $object );
		$filters[] = $parent_filter;
		$filters[] = new FilterAutoCompleteWebMethod( 
				$object->getAttributeObject('Author'), 
				translate($object->getAttributeUserName( 'Author' )) 
				);
		$filters[] = new ViewWikiContentWebMethod();
			
		$type_it = $this->object->getTypeIt();
		if ( is_object($type_it) )
		{
		    $filter = new FilterObjectMethod( $type_it, '', 'type' );
		    $filter->setIdFieldName( 'ReferenceName' );
			$filters[] = $filter;
		}
		
		return array_merge( $filters, parent::getFilters() );
	}
	
	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
		$predicates = array (
			new PMWikiStageFilter( $values['version'] ),
			new StatePredicate( $values['state'] ),
			new FilterAttributePredicate( 'PageType', $values['type'] ),
			new PMWikiLinkedStateFilter( $values['linkstate'] ),
			new FilterAttributePredicate( 'Author', $values['participant'] ),
			new WikiRootTransitiveFilter( $values['parentpage'] ),
			new WikiTagFilter( $values['tag'] ),
			new WikiContentFilter( $values['content'] ),
			new WikiRelatedIssuesPredicate( $_REQUEST['issues'] ),
		    new WikiRootTransitiveFilter( $values['document'] ),
			new FilterModifiedAfterPredicate($values['modifiedafter'])
		);
		
		if ( $this->Statable($this->getObject()) )
		{
		    $predicates[] = new TransitionObjectPredicate($this->getObject(), $values['transition']);
		}
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
	
	function getNewActions()
	{
		$actions = array();

		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return $actions;
		$method->setRedirectUrl('donothing');

		$actions['create'] = array( 
	        'name' => $this->object->getDisplayName(),
			'url' => $method->getJSCall(),
			'uid' => 'create' 
		);

        $type_it = $this->getForm()->getTypeIt();
        while ( is_object($type_it) && !$type_it->end() )
        {
            $uid = 'create'.$type_it->get('ReferenceName');
            $actions[$uid] = array(
                'name' => $type_it->getDisplayName(),
                'url' => $method->getJSCall(array('PageType'=>$type_it->getId())),
                'uid' => $uid
            );
            $type_it->moveNext();
        }
		return $actions;
	}
	
	function getExportActions()
	{
	    $page_it = $this->getObject()->getEmptyIterator();
	    
	    $actions = $this->getForm()->getExportActions( $page_it );

		$method = new ExcelExportWebMethod();
		
		$actions[] = array( 
			'name' => $method->getCaption().' ('.translate('Текст').')',
			'url' => $method->getJSCall( $this->getCaption(), 'WikiIteratorExportExcelText' ) 
		);
		
		$actions[] = array( 
			'name' => $method->getCaption().' ('.translate('HTML').')',
			'url' => $method->getJSCall( $this->getCaption(), 'WikiIteratorExportExcelHtml' ) 
		);
		
		return $actions;
	}
	
	function getTraceActions()
	{
		return array();
	}
	
	function getActions()
	{
		$actions = array();
		
		$export_actions = $this->getExportActions();
		if ( count($export_actions) > 0 )
		{
			$actions[] = array( 
			        'name' => translate('Экспорт'),
					'items' => $export_actions,
			        'uid' => 'export'
			);
		}

		$trace_actions = $this->getTraceActions();
		if ( count($trace_actions) > 0 )
		{
	        if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			$actions[] = array (
				'uid' => 'trace', 
				'name' => translate('Трассировка'),
				'items' => $trace_actions 
			);
		}

		$module_it = getFactory()->getObject('Module')->getExact('attachments');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$item = $module_it->buildMenuItem('class='.get_class($this->getObject()));
			$actions[] = array();
			$actions[] = array(
					'name' => text(1373),
					'url' => $item['url']
			);
		}

		return array_merge($actions, parent::getActions());
	}

	function getMethodTitle( $left_part, $right_part )
	{
		if ( function_exists('mb_strtolower') )
		{
			$right_part[0] = mb_strtolower( $right_part[0] );
		}
		else
		{
			$right_part[0] = strtolower( $right_part[0] );
		}
		
		return $left_part.': '.$right_part;
	}
	
	function IsNeedNavigator2() 
	{
		return false;
	}
	
	function getViewFilter()
	{
		return new WikiFilterViewWebMethod();
	}
}