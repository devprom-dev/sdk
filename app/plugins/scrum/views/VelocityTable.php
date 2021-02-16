<?php
include "VelocityChart.php";

class VelocityTable extends PMPageTable
{
	function getList()
	{
		return new VelocityChart( $this->getObject() );
	}

	function getExportActions()
    {
        return array();
    }

    function getNewActions()
    {
        return array();
    }

    function getFilterPredicates( $values )
    {
        if ( $this->getObject() instanceof Iteration ) {
            return array_merge(
                parent::getFilterPredicates( $values ),
                array (
                    new IterationTimelinePredicate(IterationTimelinePredicate::PAST)
                )
            );
        }
        else {
            return array_merge(
                parent::getFilterPredicates( $values ),
                array (
                    new ReleaseTimelinePredicate('past')
                )
            );
        }
    }
} 