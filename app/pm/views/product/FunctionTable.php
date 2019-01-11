<?php
include_once SERVER_ROOT_PATH.'pm/methods/FunctionFilterStageWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/methods/FunctionFilterStateWebMethod.php';
include "FunctionList.php";
include "FunctionChart.php";
include "FunctionTreeGrid.php";

class FunctionTable extends PMPageTable
{
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
            case 'chart':
                return new FunctionChart( $this->getObject() );
            case 'trace':
                return new FunctionList( $this->getObject() );
			default:
				return new FunctionTreeGrid( $this->getObject() );
		}
	}

    function buildFiltersName() {
        return md5($_REQUEST['view'].parent::buildFiltersName());
    }

    function getActions()
    {
        $actions = array();
        $object = $this->getObject();

        if ( getFactory()->getAccessPolicy()->can_create($object) && !getSession()->getProjectIt()->IsPortfolio() ) {
            $actions[] = array();
            $actions['import-excel'] = array(
                'name' => text(2261),
                'url' => '?view=import&mode=xml&object='.get_class($this->getObject()),
                'uid' => 'import-excel'
            );
        }

        return array_merge($actions, parent::getActions());
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

		return array_merge( $filters, parent::getFilters() );
	}
	
	function getFilterPredicates()
	{
	    $filters = $this->getFilterValues();

		$predicates = array(
			new FeatureStateFilter( $filters['state'] ),
			new CustomTagFilter( $this->getObject(), $filters['tag'] ),
			new FilterAttributePredicate( 'Importance', $filters['importance'] ),
			new FilterAttributePredicate( 'Type', $filters['type'] ),
            new ParentTransitiveFilter($_REQUEST['roots']),
            new ParentTransitiveFilter($filters['parent'])
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
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

    protected function getFamilyModules( $module )
    {
        return array(
            'features-trace',
            'features-list'
        );
    }

    protected function getChartModules( $module )
    {
        return array(
            'features-chart'
        );
    }

    protected function getChartsModuleName()
    {
        return 'features-chart';
    }
}