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
		 	default:
		 		return new PMWikiList( $this->getObject() );
		}
	}
	
	function buildFiltersName()
	{
        return md5($_REQUEST['view'].parent::buildFiltersName());
	}

	function getCommonFilters()
	{
		$filters = array();

		if ( $this->getObject()->IsStatable() ) {
			$filters[] = $this->buildFilterState();
		}
		$filters[] = $this->buildTagsFilter();
		$filters[] = new FilterObjectMethod(
			getFactory()->getObject('ProjectUser'), translate($this->getObject()->getAttributeUserName('Author')), 'author'
		);
        $filters[] = $this->buildFunctionFilter();

		return $filters;
	}

    function buildTagsFilter()
    {
        $tag = getFactory()->getObject('WikiTag');
        $tag->addFilter( new WikiTagReferenceFilter($this->getObject()->getReferenceName()) );
 	    $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }
	
	function getFilters()
	{
		$object = $this->getObject();
		
		$filters = array( $this->buildFilterDocument() );

		$filters[] = $this->buildByDateFilter();
		$filters[] = new WikiFilterActualLinkWebMethod();

        $filters[] = new ViewSubmmitedAfterDateWebMethod();
        $filters[] = new ViewSubmmitedBeforeDateWebMethod();
		$filters[] = new ViewModifiedAfterDateWebMethod();
        $filters[] = new ViewModifiedBeforeDateWebMethod();

		$parent_filter = new FilterAutoCompleteWebMethod($object, translate('Входит в'));
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

		$filter = $this->buildCompareBaselineFilter();
		if ( is_object($filter) ) $filters[] = $filter;

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
		    new WikiPageFeaturePredicate($values['feature']),
			new PMWikiStageFilter( $values['version'] ),
			new StatePredicate( $values['state'] ),
			new FilterAttributePredicate( 'PageType', $values['type'] ),
			new WikiTypePlusChildren($values['typepluschildren']),
			new FilterAttributePredicate( 'Author', $values['author'] ),
			new ParentTransitiveFilter( $values['parentpage'] ),
			new WikiTagFilter( $values['tag'] ),
			new WikiRelatedIssuesPredicate( $_REQUEST['issues'] ),
			new FilterSearchAttributesPredicate($values['search'], array('Caption','Content')),
            $this->buildLinkStateFilter($values)
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

	function buildLinkStateFilter( $values ) {
        return new PMWikiLinkedStateFilter( $values['linkstate'] );
    }

	function getNewPageTitle()
    {
        return $this->object->getDisplayName();
    }

	function getNewActions()
	{
		$actions = array();

		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return $actions;
		$method->setRedirectUrl('donothing');

		$actions['create'] = array( 
	        'name' => $this->getNewPageTitle(),
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
	
	function buildFilterDocument()
	{
		$document_filter = new FilterObjectMethod(
            getFactory()->getObject('WikiDocument')->getRegistry()->Query(
		        array(
		            new FilterAttributePredicate('ReferenceName', $this->getObject()->getReferenceName()),
                    new FilterVpdPredicate()
                )
            ),
            $this->getObject()->getDocumentName(), 'document'
        );
		$document_filter->setType( 'singlevalue' );
		$document_filter->setHasNone( false );
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
		$filter = new FilterObjectMethod( $type_it->object, translate('Тип'), 'type' );
		$filter->setIdFieldName( 'ReferenceName' );
		return $filter;
	}

    function buildFunctionFilter() {
 	    $filter = new FilterObjectMethod(getFactory()->getObject('Feature'));
        $filter->setHasNone(false);
        return $filter;
    }

    function buildCompareBaselineFilter() {
        $filter = new FilterObjectMethod(getFactory()->getObject('Baseline'), text(1566), 'compareto');
        $filter->setHasNone(false);
        $filter->setIdFieldName('Caption');
        $filter->setType( 'singlevalue' );
        return $filter;
    }

    function getSortFields()
	{
		$fields = parent::getSortFields();
		$fields[] = 'SectionNumber';
		return $fields;
	}
}