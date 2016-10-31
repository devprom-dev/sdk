<?php
include_once SERVER_ROOT_PATH."pm/methods/FilterStateTransitionMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/FilterStateMethod.php";
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

	function getCommonFilters()
	{
		$filters = array();

		if ( $this->getObject()->IsStatable() ) {
			$filters[] = new FilterStateMethod($this->getObject());
		}
		$filters[] = new ViewWikiTagWebMethod($this->getObject());
		$filters[] = new FilterObjectMethod(
			getFactory()->getObject('User'), translate($this->getObject()->getAttributeUserName('Author')), 'author'
		);

		return $filters;
	}
	
	function getFilters()
	{
		$object = $this->getObject();
		
		$filters = array( $this->buildFilterDocument() );

		$filters[] = $this->buildByDateFilter();
		$filters[] = new WikiFilterActualLinkWebMethod();
		$filters[] = new ViewWikiModifiedAfterDateWebMethod();

		$parent_filter = new FilterAutoCompleteWebMethod(
			$object, translate($object->getAttributeUserName( 'ParentPage' ))
		);
		$parent_filter->setValueParm( 'parentpage' );
		$filters[] = $parent_filter;

		$type_it = $this->object->getTypeIt();
		if ( is_object($type_it) ) {
			$filter = $this->buildTypeFilter($type_it);
			if ( is_array($filter) ) {
				$filters = array_merge($filters, $filter);
			}
			else {
				$filters[] = $filter;
			}
		}

		if ( $this->getObject()->IsStatable() ) {
			$filters[] = new FilterStateTransitionMethod($this->getObject());
		}

		return array_merge(
			$this->getCommonFilters(),
			$filters,
			parent::getFilters()
		);
	}
	
	function getFilterPredicates()
	{
		$values = $this->getFilterValues();

		$predicates = array (
			new PMWikiStageFilter( $values['version'] ),
			new StatePredicate( $values['state'] ),
			new FilterAttributePredicate( 'PageType', $values['type'] ),
			new WikiTypePlusChildren($values['typepluschildren']),
			new PMWikiLinkedStateFilter( $values['linkstate'] ),
			new WikiAuthorFilter( $values['author'] ),
			new WikiRootTransitiveFilter( $values['parentpage'] ),
			new WikiTagFilter( $values['tag'] ),
			new WikiRelatedIssuesPredicate( $_REQUEST['issues'] ),
		    new WikiDocumentUIDFilter( $values['document'] ),
			new FilterModifiedAfterPredicate($values['modifiedafter']),
			new FilterSearchAttributesPredicate($values['search'], array('Caption','Content'))
		);

		if ( $this->Statable($this->getObject()) ) {
		    $predicates[] = new TransitionObjectPredicate($this->getObject(), $values['transition']);
		}

		if ( !in_array($values['bydate'], array('','all','hide')) ) {
			$persister = new WikiPageHistoryPersister();
			$persister->setSinceDate($values['bydate']);
			$this->getObject()->addPersister( $persister );
			$predicates[] = new FilterSubmittedBeforePredicate($values['bydate']);
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

		return $actions;
	}
	
	function getExportActions()
	{
	    $actions = $this->getForm()->getExportActions( $this->getExportPageIt() );

		$method = new ExcelExportWebMethod();
		$actions[] = array(
			'uid' => 'export-excel-text',
			'name' => 'Excel ('.translate('Текст').')',
			'url' => $method->url( $this->getCaption(), 'WikiIteratorExportExcelText' )
		);
		$actions[] = array(
			'uid' => 'export-excel-html',
			'name' => 'Excel ('.translate('HTML').')',
			'url' => $method->url( $this->getCaption(), 'WikiIteratorExportExcelHtml' )
		);
		
		return $actions;
	}

	function getExportPageIt()
    {
	    return $this->getObject()->getEmptyIterator();
    }
	
	function getActions()
	{
		$actions = array();
		
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

	function buildFilterDocument()
	{
		$document = getFactory()->getObject(get_class($this->getObject()));
		$document->addFilter( new WikiRootFilter() );
		$document_filter = new FilterObjectMethod( $document, translate('Документ'), 'document' );
		$document_filter->setType( 'singlevalue' );
		$document_filter->setUseUid( true );
		$document_filter->setHasNone( false );
		$document_filter->setIdFieldName('UID');
		return $document_filter;
	}

	function buildByDateFilter()
	{
		$date = new FilterDateWebMethod();
		$date->setValueParm( 'bydate' );
		$date->setCaption(text(2122));
		return $date;
	}

	function buildTypeFilter( $type_it )
	{
		$filter = new FilterObjectMethod( $type_it, translate('Тип'), 'type' );
		$filter->setIdFieldName( 'ReferenceName' );
		return $filter;
	}

	function getSortFields()
	{
		$fields = parent::getSortFields();
		$fields[] = 'SectionNumber';
		return $fields;
	}

    function getDetails()
    {
        foreach( $this->getPage()->getInfoSections() as $section ) {
            if ( $section instanceof DetailsInfoSection ) {
                return parent::getDetails();
            }
        }
        return array();
    }
}