<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class WorkloadDetailsList extends PMDetailsList
{
	private $strategy = null;
	private $reportIt = null;

	function setupColumns()
	{
        $this->reportIt = getFactory()->getObject('PMReport')->getExact('workitemchart');

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Task')) ) {
			$this->strategy = $methodology_it->TaskEstimationUsed() ? new EstimationHoursStrategy() : new EstimationNoneStrategy();
		}
		else {
			$this->strategy = $methodology_it->getEstimationStrategy();
		}

		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
			if ( $attribute == 'Caption' ) continue;
			$this->getObject()->setAttributeVisible($attribute, false);
		}
		parent::setupColumns();
	}

	function drawCell( $object_it, $attr )
	{
		echo $this->getRenderView()->render('pm/UserWorkloadDetails.php', array (
			'user_id' => $object_it->getId(),
			'user_name' => $object_it->getDisplayName(),
			'measure' => $this->strategy,
            'url' => $this->reportIt->getUrl('taskassignee='.$object_it->getId()),
            'leftWork' => $this->getLeftWork($object_it)
		));
	}

    function drawCellShort( $object_it, $attr )
    {
        echo $this->getRenderView()->render('pm/UserWorkloadDetails.php', array (
            'user_id' => $object_it->getId(),
            'user_name' => $object_it->getDisplayName(),
            'measure' => $this->strategy,
            'url' => $this->reportIt->getUrl('taskassignee='.$object_it->getId()),
            'skipPhoto' => 'true',
            'leftWork' => $this->getLeftWork($object_it)
        ));
    }

    function getLeftWork( $object_it )
    {
        $leftWorkValue = 0;

        $work = getFactory()->getObject('Task');
        $work->addFilter( new FilterAttributePredicate('Assignee', $object_it->getId()) );
        $sum_aggregate = new AggregateBase( 'Assignee', 'LeftWork', 'SUM' );
        $work->addAggregate( $sum_aggregate );
        $workIt = $work->getAggregated();
        $leftWorkValue += $workIt->get($sum_aggregate->getAggregateAlias());

        $work = getFactory()->getObject('Request');
        $work->addFilter( new FilterAttributePredicate('Owner', $object_it->getId()) );
        $sum_aggregate = new AggregateBase( 'Owner', 'EstimationLeft', 'SUM' );
        $work->addAggregate( $sum_aggregate );
        $workIt = $work->getAggregated();
        $leftWorkValue += $workIt->get($sum_aggregate->getAggregateAlias());

        return $leftWorkValue;
    }
}
