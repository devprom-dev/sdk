<?php
include_once SERVER_ROOT_PATH.'pm/methods/FunctionFilterStateWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';
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
			    if ( $_REQUEST['export'] != '' ) {
                    return new FunctionList( $this->getObject() );
                }
				return new FunctionTreeGrid( $this->getObject() );
		}
	}

    function buildFiltersName() {
        return md5($_REQUEST['view'].parent::buildFiltersName());
    }

    function getNewActions()
	{
		$type_it = getFactory()->getObject('FeatureType')->getAll();
		
		if ( $type_it->count() < 1 ) return parent::getNewActions(); 

		$actions = array();
		
		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return $actions;

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

    function getExportActions()
    {
        $actions = parent::getExportActions();

        $method = new WikiExportBaseWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array ('pm_FunctionId' => '%ids%')
            )
        );
        $converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $actions[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url($methodPageIt, $converter_it->get('EngineClassName'))
            );
            $converter_it->moveNext();
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
			new FilterObjectMethod($this->getObject(), text(2094), 'parent')
		);

		$filter = $this->buildStageFilter();
		if ( is_object($filter) ) {
		    $filters[] = $filter;
        }

		return array_merge( $filters, parent::getFilters() );
	}

	function buildStageFilter()
    {
        $methodologIt = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodologIt->HasReleases() || $methodologIt->HasPlanning() ) {
            return new FilterObjectMethod( getFactory()->getObject('Stage'), '', 'stage');
        }
    }

	function getFilterPredicates( $values )
	{
		$predicates = array(
			new FeatureStateFilter( $values['state'] ),
			new CustomTagFilter( $this->getObject(), $values['tag'] ),
            $_REQUEST['roots'] == '0'
                ? new ObjectRootFilter()
                : new FilterAttributePredicate('ParentFeature', $_REQUEST['roots']),
            new ParentTransitiveFilter($values['parent']),
            new FeatureStageFilter($values['stage'])
		);
		
		return array_merge(parent::getFilterPredicates( $values ), $predicates);
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

    function getSortAttributeClause( $field )
    {
        $parts = preg_split('/\./', $field);
        switch( $parts[0] ) {
            case 'FeatureLevel':
                return new SortReferenceNameClause($this->getObject()->getAttributeObject($parts[0]), 'Type');
            default:
                return parent::getSortAttributeClause($field);
        }
    }

    protected function getFamilyModules( $module )
    {
        return array(
            'delivery',
            'features-trace',
            'features-list',
            'dicts-featuretype'
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