<?php
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include_once SERVER_ROOT_PATH.'pm/methods/c_request_methods.php';
include "LeadAndCycleTimeChart.php";

class LeadAndCycleTimeTable extends PMPageTable
{
	function getList()
	{
		return new LeadAndCycleTimeChart( $this->getObject() );
	}

 	function getFilterPredicates()
 	{
 		$values = $this->getFilterValues();
 		return array_merge(
			parent::getFilterPredicates(),
			array (
				new FilterSubmittedAfterPredicate($values['submittedon']),
				new FilterSubmittedBeforePredicate($values['submittedbefore']),
				new RequestFinishAfterPredicate($values['modifiedafter']),
				new RequestAuthorFilter( $values['author'] ),
				new FilterAttributePredicate('Type', $values['type']),
				new FilterAttributePredicate('Priority', $values['priority']),
                new FilterAttributePredicate( 'Owner', $values['owner'] ),
                new RequestEstimationFilter($values['estimation'])
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
            $this->buildFilterEstimation()
		);
		return array_merge( $filters, parent::getFilters() );
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
		return new FilterObjectMethod(getFactory()->getObject('IssueAuthor'), translate('Автор'), 'author');
	}

	function getFiltersDefault() {
        return array('type','priority','modifiedafter','owner','estimation');
    }
}