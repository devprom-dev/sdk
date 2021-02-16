<?php

include "FlotChartLeadAndCycleTimeWidget.php";

class LeadAndCycleTimeChart extends PMPageChart
{
	private $widget = null;
	private $iterator = null;

 	function __construct( $object )
 	{
		parent::__construct( $object );
		$this->widget = new FlotChartLeadAndCycleTimeWidget();
 	}

	function getChartWidget() {
		$this->widget->setIterator($this->iterator);
		return $this->widget;
	}

    function buildData( $aggs )
    {
 	    $this->iterator = $this->getObject()->getRegistry()->Query(
            array_merge(
                array(
                    new FilterVpdPredicate()
                ),
                $this->getTable()->getFilterPredicates($this->getTable()->getFilterValues()),
                $this->getSorts()
            )
        );

        $data = array();
 	    while( !$this->iterator->end() ) {
 	        $data[$this->iterator->getId()] = array(
 	            'data' => $this->iterator->get('LifecycleDuration')
            );
            $this->iterator->moveNext();
        }
        $this->iterator->moveFirst();

        return $data;
    }

	protected function getDemoData($aggs)
	{
		$x_attribute = $aggs[0]->getAttribute();
		$y_attribute = $aggs[0]->getAggregateAlias();
		$x_value = time() / 1000;
		$x_delta = 70000;

		$data = array();
		foreach( array(5, 3, 1) as $max_index => $max ) {
			for( $i = 0; $i < 20; $i++ ) {
				$data[] = array (
						$x_attribute => $x_value + $x_delta * ($i + 1) * ($max_index + 1),
						$y_attribute => rand($max - 1, $max)
				);
			}
		}
		return $data;
	}

	function getColumnFields()
	{
		return array();
	}
	
	function getGroupFields()
	{
		return array();
	}
	
	function getAggByFields()
	{
		return array();
	}
	
	function getAggregators()
	{
		return array();
	}

    function getOptions($filter_values)
    {
        return array();
    }

    function getAggregateBy() {
 	    return "LifecycleDuration";
    }

    function getAggregator() {
 	    return "SUM";
    }

    function getGroup() {
        return $this->getObject()->getClassName().'Id';
    }

    function getSorts() {
        return array(
            $this->getTable()->getSortAttributeClause('FinishDate')
        );
    }

    function drawLegend( $data, & $aggs )
	{
	}
}