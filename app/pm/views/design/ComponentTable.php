<?php
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';
include "ComponentList.php";
include "ComponentChart.php";
include "ComponentTreeGrid.php";

class ComponentTable extends PMPageTable
{
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
            case 'chart':
                return new ComponentChart( $this->getObject() );
            case 'trace':
                return new ComponentList( $this->getObject() );
			default:
			    if ( $_REQUEST['export'] != '' ) {
                    return new ComponentList( $this->getObject() );
                }
				return new ComponentTreeGrid( $this->getObject() );
		}
	}

    function buildFiltersName() {
        return md5($_REQUEST['view'].parent::buildFiltersName());
    }

    function getNewActions()
	{
		$type_it = getFactory()->getObject('ComponentType')->getAll();
		if ( $type_it->count() < 1 ) return parent::getNewActions();

		$actions = array();
		
		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return $actions;

		while( !$type_it->end() )
		{
			$uid = 'append-component-'.$type_it->get('ReferenceName');
			$parms['Type'] = $type_it->getId();
			
			$actions[$uid] = array ( 
				'name' => $type_it->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms)
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
                array ('pm_ComponentId' => '%ids%')
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
		return array_merge(
            array(
                $this->buildFilterType()
            ),
            parent::getFilters()
        );
	}

    function getFilterPredicates( $values )
    {
        $predicates = array(
            $_REQUEST['roots'] == '0'
                ? new ObjectRootFilter()
                : new FilterAttributePredicate('ParentComponent', $_REQUEST['roots']),
            new ParentTransitiveFilter($values['parent'])
        );
        return array_merge(parent::getFilterPredicates( $values ), $predicates);
    }

	protected function buildFilterType()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('ComponentType'), '', 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		return $type_method;
	}

    protected function getFamilyModules( $module ) {
        return array(
            'components-list',
            'components-trace',
            'dicts-componenttype'
        );
    }

    protected function getChartModules( $module ) {
        return array(
            'components-chart'
        );
    }

    protected function getChartsModuleName() {
        return 'components-chart';
    }
}