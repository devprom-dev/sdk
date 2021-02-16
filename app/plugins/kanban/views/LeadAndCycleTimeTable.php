<?php
include_once SERVER_ROOT_PATH."pm/methods/ViewRequestEstimationWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/StateExFilterWebMethod.php";
include "LeadAndCycleTimeChart.php";

class LeadAndCycleTimeTable extends PMPageTable
{
	function getList()
	{
		return new LeadAndCycleTimeChart( $this->getObject() );
	}

 	function getFilterPredicates( $values )
 	{
        return array_merge(
			parent::getFilterPredicates( $values ),
			array (
				new FilterSubmittedAfterPredicate($values['submittedon']),
				new FilterSubmittedBeforePredicate($values['submittedbefore']),
				new RequestFinishAfterPredicate($values['modifiedafter']),
				new RequestAuthorFilter( $values['author'] ),
				new FilterAttributePredicate('Type', $values['type']),
				new FilterAttributePredicate('Priority', $values['priority']),
                new FilterAttributePredicate( 'Owner', $values['owner'] ),
                new RequestEstimationFilter($values['estimation']),
                new StatePredicate( $values['state'] )
			)
 		);
 	}

	function getFilters()
	{
		$filter = new ViewModifiedAfterDateWebMethod();
		$filter->setCaption(text(2162));

		$filters = array(
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			$filter,
			new FilterObjectMethod( getFactory()->getObject('Priority'), '', 'priority'),
			$this->buildTypeFilter(),
			$this->buildFilterAuthor(),
            $this->buildFilterOwner(),
            $this->buildFilterEstimation(),
            new StateExFilterWebMethod(WorkflowScheme::Instance()->getStateIt($this->getObject()), 'state', "Y")
        );
		return array_merge( $filters, parent::getFilters() );
	}

    function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);
        if ( !array_key_exists('modifiedafter', $values) ) {
            $values['modifiedafter'] = 'last-month';
        }
        return $values;
    }

    protected function buildFilterOwner() {
        return new FilterObjectMethod(
            getFactory()->getObject('ProjectUser'),
            translate($this->getObject()->getAttributeUserName('Owner')),
            'owner'
        );
    }

    protected function buildFilterEstimation()
    {
        return new ViewRequestEstimationWebMethod(
            getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getFilterScale()
        );
    }

	function buildTypeFilter()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('pm_IssueType'), translate('Тип'), 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		$type_method->setNoneTitle( getFactory()->getObject('Request')->getDisplayName() );
		return $type_method;
	}

	protected function buildFilterAuthor() {
        $filter = new FilterObjectMethod(getFactory()->getObject('IssueAuthor'), translate('Автор'), 'author');
        $filter->setLazyLoad(true);
		return $filter;
	}
}