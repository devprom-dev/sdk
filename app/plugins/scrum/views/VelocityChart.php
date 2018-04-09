<?php
include "FlotChartVelocityWidget.php";

class VelocityChart extends PMPageChart
{
 	function __construct( $object )
 	{
		parent::__construct( $object );
 	}

    function buildData( $aggs )
    {
        $dataIt = $this->getObject()->getRegistry()->Query(
            array_merge(
                array(
                    new FilterVpdPredicate()
                ),
                $this->getTable()->getFilterPredicates(),
                array(
                    new SortAttributeClause('StartDate')
                )
            )
        );

        $data = array();
        while( !$dataIt->end() ) {
            $data[$dataIt->getDisplayName().' '] = array(
                'data' => array(
                    text('scrum21') => round($dataIt->get('Velocity'),1),
                    text('scrum20') => round($dataIt->get('InitialVelocity'),1)
                )
            );
            $dataIt->moveNext();
        }
        if ( count($data) < 1 ) {
            return array(
                '0 ' => array(
                    'data' => array(
                        text('scrum21') => 70,
                        text('scrum20') => 90
                    )
                ),
                '1 ' => array(
                    'data' => array(
                        text('scrum21') => 80,
                        text('scrum20') => 90
                    )
                ),
                '2 ' => array(
                    'data' => array(
                        text('scrum21') => 110,
                        text('scrum20') => 90
                    )
                )
            );
        }
        return $data;
    }

	function getChartWidget() {
        return new FlotChartMultiLineWidget();
	}

    function getAggByFields()
    {
        return array();
    }

    function getAggregators()
    {
        return array();
    }

    function getLegendVisible()
    {
        return false;
    }

    function getGroupFields()
    {
        return array();
    }

    function getOptions($filter_values)
    {
        return array();
    }
}