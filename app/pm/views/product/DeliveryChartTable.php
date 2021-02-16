<?php
include "DeliveryChart.php";

class DeliveryChartTable extends PMPageTable
{
	function getList() {
		return new DeliveryChart( $this->getObject() );
	}
	
	function getTemplate() {
		return 'pm/DeliveryChart.tpl.php';
	}
	
  	function getFilters()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		$filters = array (
			$this->buildFilterType(),
			$this->buildFilterState(),
			$this->buildFilterPriority()
		);
		if ( $methodology_it->HasFeatures() ) {
			$filters[] = $this->buildFilterImportance();
		}
		$filter = $this->buildProjectFilter();
		if ( is_object($filter) ) $filters = array_merge(array($filter), $filters);

        $filters[] = $this->buildStartDateFilter();
        $filters[] = new FilterDateWebMethod(text(2267), 'finish');
		return $filters;
	}
	
	function getFilterPredicates( $values )
	{
		return array_merge (
            parent::getFilterPredicates( $values ),
            array (
                new DeliveryProductTypePredicate($values['type']),
                new DeliveryPriorityPredicate($values['priority']),
                new DeliveryImportancePredicate($values['importance']),
                new DeliveryStatePredicate($values['state']),
                new DeliveryStartAfterPredicate($values['start']),
                new DeliveryStartBeforePredicate($values['finish'])
            )
		);
	}

	protected function buildStartDateFilter()
    {
        $filter = new FilterDateWebMethod(text(2266), 'start');
        return $filter;
    }

	protected function buildFilterType()
	{
		$method = new FilterObjectMethod( getFactory()->getObject('DeliveryProduct'), translate('Детализация'), 'type');
		$method->setIdFieldName( 'ReferenceName' );
		$method->setHasNone(false);
        $method->setDefaultValue('Feature');
		return $method;
	}
	
	protected function buildFilterPriority()
	{
		$method = new FilterObjectMethod( getFactory()->getObject('Priority'), translate('Приоритет'), 'priority');
		$method->setHasNone(false);
		return $method;
	}

	protected function buildFilterImportance()
	{
		$method = new FilterObjectMethod( getFactory()->getObject('Importance'), text('ee228'), 'importance');
		$method->setHasNone(false);
		return $method;
	}
	
	protected function buildFilterState( $filterValues = array() )
	{
	 	$metastate = getFactory()->getObject('StateMeta');
		$metastate->setAggregatedStateObject(getFactory()->getObject('IssueState'));
	 	$metastate->setStatesDelimiter("-");

		$method = new FilterObjectMethod( $metastate, translate('Состояние'), 'state');
		$method->setHasNone(false);
		$method->setIdFieldName( 'ReferenceName' );
		return $method;
	}

    function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);
        if ( !array_key_exists('start', $values) ) {
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-1 year', strtotime(date('Y-m-j'))));
        }
        return $values;
    }

    protected function getFamilyModules( $module )
    {
        return array (
            'project-plan-hierarchy'
        );
    }

    function getNewActions()
    {
        $actions = array();
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
        if ( $methodology_it->HasPlanning() && $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Итерация'),
                'url' => $method->getJSCall()
            );
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Release'));
        if ( $methodology_it->HasReleases() && $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Релиз'),
                'url' => $method->getJSCall()
            );
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Milestone'));
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Веха'),
                'url' => $method->getJSCall()
            );
        }
        return $actions;
    }
}