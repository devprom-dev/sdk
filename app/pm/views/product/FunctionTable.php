<?php
include "FunctionList.php";

class FunctionTable extends PMPageTable
{
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
			default:
				return new FunctionList( $this->getObject() );
		}
	}

	function getNewActions()
	{
		$type_it = getFactory()->getObject('FeatureType')->getAll();
		
		if ( $type_it->count() < 1 ) return parent::getNewActions(); 

		$actions = array();
		
		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return $actions;

        $method->setRedirectUrl('donothing');
		while( !$type_it->end() )
		{
			$uid = 'append-feature-'.$type_it->get('ReferenceName');
			$parms['Type'] = $type_it->getId();
			
			$actions[$uid] = array ( 
				'name' => $type_it->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms, $type_it->getDisplayName())
			);
			
			$type_it->moveNext();
		}
		
		return $actions;  
	}
	
	function getFilters()
	{
		$filters = array(
			new FunctionFilterStateWebMethod(),
			$this->buildFilterType(),
			$this->buildTagsFilter(),
			new FilterObjectMethod( getFactory()->getObject('Importance'), '', 'importance'),
			new FunctionFilterStageWebMethod(),
			new FilterObjectMethod($this->getObject(), text(2094), 'parent')
		);

		$view = new FunctionFilterViewWebMethod();
		$view->setFilter( $this->getFiltersName() );
		
		return array_merge( $filters, parent::getFilters() );
	}
	
	function getFilterPredicates()
	{
	    $filters = $this->getFilterValues();
	    
		$predicates = array(
			new FeatureStateFilter( $filters['state'] ),
			new FeatureStageFilter( $filters['stage'] ),
			new CustomTagFilter( $this->getObject(), $filters['tag'] ),
			new FilterAttributePredicate( 'Importance', $filters['importance'] ),
			new FilterAttributePredicate( 'Type', $filters['type'] ),
			new ParentTransitiveFilter( $filters['parent'] )
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
		
	function getViewFilter()
	{
		return new FunctionFilterViewWebMethod();
	}

	protected function buildFilterType()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('FeatureType'), '', 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		return $type_method;
	}

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('FeatureTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }
}